<?php
function filterData(string $table, $data, $dbname = null): array
{
    if ($dbname != null) {
        $db         = \Config\Database::connect($dbname);
    } else {
        $db         = \Config\Database::connect();
    }

    $filteredData = [];
    $columns = $db->getFieldNames($table);

    if (is_array($data)) {
        foreach ($columns as $column) {
            if (array_key_exists($column, $data)) {
                $filteredData[$column] = $data[$column];
            }
        }
    }

    return $filteredData;
}

// function to make dropdown filled from database
function getDropdownList($table, $columns, $defaultVal = '', $defaultOpt = '- Select -', $orderCol = 'id', $orderVal = 'asc', $exceptId = '', $dbname = null)
{
    if ($dbname != null) {
        $db         = \Config\Database::connect($dbname);
    } else {
        $db         = \Config\Database::connect();
    }
    $builder = $db->table($table);

    $builder->where('id !=', $exceptId);
    $builder->orderBy($orderCol, $orderVal);
    $query    = $builder->select($columns)->get()->getResultArray();

    if ($builder->countAllResults() >= 1) {
        $option1    = [$defaultVal => $defaultOpt];
        $option2    = array_column($query, $columns[1], $columns[0]);
        $options    = $option1 + $option2;

        return $options;
    }

    return $options    = [$defaultVal => $defaultOpt];
}

// function to make dropdown filled from database
function getDropdownGroups($table, $columns, $defaultVal = '', $defaultOpt = '- Select -', $orderCol = 'id', $orderVal = 'asc')
{
    $db         = \Config\Database::connect();
    $builder = $db->table($table);

    $builder->orderBy($orderCol, $orderVal);
    $query    = $builder->select($columns)->where('id!=', 1)->get()->getResultArray();

    if ($builder->countAllResults() >= 1) {
        $option    = array_column($query, $columns[1], $columns[0]);
        return $option;
    }

    return $option    = [$defaultVal => $defaultOpt];
}

function getDropdownJabatan($table, $columns, $defaultVal = '', $defaultOpt = '- Select -', $orderCol = 'id', $orderVal = 'asc')
{
    $db         = \Config\Database::connect();
    $builder = $db->table($table);

    $builder->orderBy($orderCol, $orderVal);
    $query    = $builder->select($columns)->get();
    if ($builder->countAllResults(false) >= 1) {
        $option1    = [$defaultVal => $defaultOpt];

        $data = [];
        foreach ($query->getResult() as $row) {
            $dataArr = [
                'id' => $row->id,
                $columns[2] => $row->{$columns[2]} . ' - ' . $row->{$columns[1]},
            ];
            array_push($data, $dataArr);
        }
        $option2    = array_column($data, $columns[2], $columns[0]);
        $options    = $option1 + $option2;

        return $options;
    }

    return $options    = [$defaultVal => $defaultOpt];
}

// get data menu, used in sidebar, menus, and groups access
function dataMenu($excludeGroup = null)
{
    $db         = \Config\Database::connect();
    $ionAuth    = new \IonAuth\Libraries\IonAuth();

    // check if super admin
    if ($ionAuth->inGroup(1)) {
        $builder = $db->table('menus');
        if (!is_null($excludeGroup)) {
            $builder->where('id !=', $excludeGroup);
            $builder->where('parent !=', $excludeGroup);
        }
        $builder->orderBy('sort', 'ASC');
        $data = $builder->get()->getResult();
    } else {
        // get data menu for spesific group
        $builder = $db->table('groups_access_menu');

        $builder->whereIn('group_id', getGroupId());
        $builder->orderBy('sort', 'ASC');

        $data = $builder->get()->getResult();
    }

    $ref   = [];
    $items = [];

    foreach ($data as $data) {
        $thisRef = &$ref[$data->id];

        $thisRef['parent'] = $data->parent;
        $thisRef['label'] = $data->label;
        $thisRef['link'] = $data->link;
        $thisRef['id'] = $data->id;
        $thisRef['sort'] = $data->sort;
        $thisRef['icon'] = $data->icon;

        if ($data->parent == 0) {
            $items[$data->id] = &$thisRef;
        } else {
            $ref[$data->parent]['child'][$data->id] = &$thisRef;
            if (!isset($ref[$data->parent]['id'])) {
                $items[$data->id] = &$thisRef;
            }
        }
    }
    return $items;
}

// function in array multidimentional
function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {

        if (!is_array($needle)) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        } else {
            if ((is_array($item) && in_array_r($needle, $item, $strict))) {
                return empty(array_diff($needle, $haystack));
            }
        }
    }

    return false;
}

// get groups of current user
function getGroupId()
{
    $db         = \Config\Database::connect();
    $user_id = session('user_id');

    // get group_id
    $queryUser =  $db->table('users_groups')->getWhere(['user_id' => $user_id])->getResult();

    // save group id to variable array
    $groupId = [];
    foreach ($queryUser as $row) {
        array_push($groupId, $row->group_id);
    }

    return $groupId;
}

function checkGroupUser($checkGroup, int $id = 0, bool $checkAll = false)
{
    $ionAuth    = new \IonAuth\Libraries\IonAuth();

    if ($id == 0) {
        $id = session('user_id');
    }

    if ($ionAuth->inGroup($checkGroup, $id, $checkAll)) {
        return true;
    }

    return false;
}

/**
 * allow access
 *
 * @param int|string|null $type read/insert/update/delete
 * @param int|string|null $menuId id menu of controller
 */
function is_allow($type = 'read', $menuSlug = null)
{
    $db         = \Config\Database::connect();
    $ionAuth    = new \IonAuth\Libraries\IonAuth();

    if ($ionAuth->inGroup(1)) {
        return true;
    } else {

        // get menu id
        $builder = $db->table('menus')->where('link', $menuSlug);

        // check menu exist
        if ($builder->countAllResults(false) > 0) {
            $menu = $builder->get()->getRow();
        } else {
            return false;
        }

        // check if groups of current user have access
        $builder = $db->table('groups_access');
        $builder->whereIn('group_id', getGroupId());
        $builder->where('menu_id', $menu->id);

        if ($type == 'insert') {
            $builder->where('insert', 1);
        }
        if ($type == 'update') {
            $builder->where('update', 1);
        }
        if ($type == 'delete') {
            $builder->where('delete', 1);
        }
        if ($type == 'validate') {
            $builder->where('validate', 1);
        }

        if ($builder->countAllResults() > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function logged_in()
{
    $ionAuth = new \IonAuth\Libraries\IonAuth();
    if (!$ionAuth->loggedIn()) {
        return false;
    }
    return true;
}

function activityLog($data, $description)
{
    $db         = \Config\Database::connect();
    $request = \Config\Services::request();
    $agent = $request->getUserAgent();

    $data = [
        'id_user' => session('user_id'),
        'ip_address' => $request->getIPAddress(),
        'data' => $data,
        'description' => $description,
        'user_agent' => $agent->getAgentString(),
    ];

    $db->table('activity_log')->insert($data);
}


// https://stackoverflow.com/questions/21809116/how-to-use-php-in-array-with-associative-array
function is_in_array($array, $key, $key_value)
{
    $within_array = 'no';
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $within_array = is_in_array($v, $key, $key_value);
            if ($within_array == 'yes') {
                break;
            }
        } else {
            if ($v == $key_value && $k == $key) {
                $within_array = 'yes';
                break;
            }
        }
    }
    return $within_array;
}

// ISO 861 Format
function dayFromNumber($day)
{
    $dates = array(
        0 => '',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    );
    return $dates[$day];
}

// https://www.delftstack.com/howto/php/how-to-get-time-difference-in-minutes-in-php
// https://www.geeksforgeeks.org/how-to-get-time-difference-in-minutes-in-php/
function datetimeDifference($date_1, $date_2, $differenceFormat = '%a')
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    if ($interval->h > 0) {
        $hour = str_pad($interval->h, 2, '0', STR_PAD_LEFT);
        $minute = str_pad($interval->i, 2, '0', STR_PAD_LEFT);
        $second = str_pad($interval->s, 2, '0', STR_PAD_LEFT);
        return "$interval->h jam $interval->i menit";
    } else {
        $minutes = $interval->days * 24 * 60;
        $minutes += $interval->h * 60;
        $minutes += $interval->i;
        return "$minutes menit";
    }
}

// https://stackoverflow.com/questions/8563535/convert-number-of-minutes-into-hours-minutes-using-php
function convertToHoursMins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return "00:00";
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

// https://www.delftstack.com/howto/php/how-to-get-time-difference-in-minutes-in-php/
function timeDifference($start, $end)
{
    if ($end >= $start) {
        $minutesDifference = (strtotime($end) - strtotime($start)) / 60;
    } else {
        $dateTimeObject1 = date_create("1999-09-08 $start");
        $dateTimeObject2 = date_create("1999-09-09 $end");
        // Calculating the difference between DateTime objects
        $interval = date_diff($dateTimeObject1, $dateTimeObject2);
        $minutesDifference = $interval->days * 24 * 60;
        $minutesDifference += $interval->h * 60;
        $minutesDifference += $interval->i;
    }
    return $minutesDifference;
}

// https://stackoverflow.com/questions/12439801/how-to-check-if-a-certain-coordinates-fall-to-another-coordinates-radius-using-p
function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
{
    $earth_radius = 6371;

    $dLat = deg2rad($latitude2 - $latitude1);
    $dLon = deg2rad($longitude2 - $longitude1);

    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;

    return $d;
}

// https://stackoverflow.com/questions/23444968/how-can-i-calculate-the-number-of-working-days-between-two-dates
function getWorkingDays($startDate, $endDate, $weekdays)
{
    // $startDate = '2016-10-01';
    // $endDate =  '2016-10-31';
    // $weekdays = array('1','2','3','4','5','6'); //this i think monday-saturday

    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);

    $end = $end->modify('+1 day'); //add one day so as to include the end date of our range

    $interval = new DateInterval('P1D'); // 1 Day
    $dateRange = new DatePeriod($begin, $interval, $end);

    $total_days = 0;
    //this will calculate total days from monday to saturday in above date range
    foreach ($dateRange as $date) {
        if (in_array($date->format("N"), $weekdays)) {
            $total_days++;
        }
    }

    return $total_days;
}
function getHolidayDays($startDate, $endDate, $holidays)
{
    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);

    $end = $end->modify('+1 day'); //add one day so as to include the end date of our range

    $interval = new DateInterval('P1D'); // 1 Day
    $dateRange = new DatePeriod($begin, $interval, $end);

    $total_days = 0;
    //this will calculate total days from monday to saturday in above date range
    foreach ($dateRange as $date) {
        if (in_array($date->format("N"), $holidays)) {
            $total_days++;
        }
    }

    return $total_days;
}

function getWorkingHours($startDate, $endDate, $idPegawai)
{
    $db = \Config\Database::connect();
    $jadwalPegawai = $db->table('jadwal_pegawai')->select('id_jadwal_kerja')->where('id_pegawai', $idPegawai)->get()->getRow();
    if (!isset($jadwalPegawai->id_jadwal_kerja)) {
        return 0;
    }
    $jadwalKerjaDetail = $db->table('jadwal_kerja_detail_view')->select("day, TIMEDIFF(jam_pulang, jam_masuk) as timediff")->where('id_jadwal_kerja', $jadwalPegawai->id_jadwal_kerja)->where('libur', false)->get()->getResultArray();
    $hariLibur = $db->query("SELECT tanggal FROM hari_libur WHERE tanggal BETWEEN '$startDate' AND '$endDate'")->getResultArray();
    $weekdays = array_column($jadwalKerjaDetail, 'day');
    $holidays = array_column($hariLibur, 'tanggal');

    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);

    $end = $end->modify('+1 day'); //add one day so as to include the end date of our range

    $interval = new DateInterval('P1D'); // 1 Day
    $dateRange = new DatePeriod($begin, $interval, $end);

    $times = [];
    //this will calculate total days from monday to saturday in above date range
    foreach ($dateRange as $date) {
        if (in_array($date->format("N"), $weekdays) && !in_array($date->format("Y-m-d"), $holidays)) {
            $index = array_search($date->format("N"), array_column($jadwalKerjaDetail, 'day'));
            $checkHadir = $db->table('view_rekap_presensi')->select("day, jadwal_timediff as timediff")->where('id_pegawai', $idPegawai)->where('tanggal', $date->format("Y-m-d"))->get()->getRow();
            if ($checkHadir) {
                $times[] = $checkHadir->timediff;
            } else {
                $times[] = $jadwalKerjaDetail[$index]['timediff'];
            }
        }
    }

    return sum_the_time($times);
}

function getWorkingHoursSimple($date, $idPegawai)
{
    $db = \Config\Database::connect();
    $jadwalPegawai = $db->table('jadwal_pegawai')->select('id_jadwal_kerja')->where('id_pegawai', $idPegawai)->get()->getRow();
    $jadwalKerjaDetail = $db->table('jadwal_kerja_detail_view')->select("day, TIMEDIFF(jam_pulang, jam_masuk) as timediff")->where('id_jadwal_kerja', $jadwalPegawai->id_jadwal_kerja)->where('libur', false)->get()->getResultArray();
    $hariLibur = $db->query("SELECT tanggal FROM hari_libur WHERE tanggal = '$date'")->getResult();
    $weekdays = array_column($jadwalKerjaDetail, 'day');
    $holidays = array_column($hariLibur, 'tanggal');

    $date = new DateTime($date);

    $times = [];
    //this will calculate total days from monday to saturday in above date range
    if (in_array($date->format("N"), $weekdays) && !in_array($date->format("Y-m-d"), $holidays)) {
        $index = array_search($date->format("N"), array_column($jadwalKerjaDetail, 'day'));
        $times[] = $jadwalKerjaDetail[$index]['timediff'];
    }
    return sum_the_time($times);
}

// https://vasavaa.wordpress.com/2012/03/19/sum-time-with-format-hhmmss-by-using-php/
function sum_the_time($times)
{
    $seconds = 0;
    foreach ($times as $time) {
        list($hour, $minute, $second) = explode(':', $time);
        $seconds += $hour * 3600;
        $seconds += $minute * 60;
        $seconds += $second;
    }
    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes  = floor($seconds / 60);
    $seconds -= $minutes * 60;
    // return "{$hours}:{$minutes}:{$seconds}";
    // return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
    return sprintf('%02d', $hours); // Thanks to Patrick
}

function getSubordinateID($userID)
{
    $db = \Config\Database::connect();

    $idPegawaiStruktual = [];
    $idPegawaiBiasa = [];

    $jabatanStrukturalUser = $db->table('pegawai_jabatan_struktural_u_view')->where('id_pegawai', $userID)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan',  'Ketua', 'Wakil Rektor', 'Penanggung Jawab'])->get()->getRow();
    if (!$jabatanStrukturalUser) {
        return [
            'idPegawaiStruktual' => $idPegawaiStruktual,
            'idPegawaiBiasa' => $idPegawaiBiasa,
        ];
    }

    // set unitRelations based on check depth >= 2 (in case wr / biro)
    $checkDepth = $db->table('unit_relations')->where('parent', $jabatanStrukturalUser->id_unit)->orderBy('depth', 'desc')->get()->getRow();
    if ($checkDepth->depth >= 2) {
        $unitRelations = $db->table('view_unit_relations')->where('parent', $jabatanStrukturalUser->id_unit)->where('depth <', 2)->get()->getResult();
    } else {
        $unitRelations = $db->table('view_unit_relations')->where('parent', $jabatanStrukturalUser->id_unit)->get()->getResult();
    }
    // check depth >= 3 in case wr
    if ($checkDepth->depth >= 3) {
        foreach ($unitRelations as $row) {
            $dinilai1 = $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
            $idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
        }
        foreach ($unitRelations as $row) {
            $dinilai2 =    $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala'])->get()->getResultArray();
            $idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
        }
        $pegawaiBiasaTendik =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userID)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', $idPegawaiBiasa)->get()->getResultArray();
        $idPegawaiBiasa = \array_column($pegawaiBiasaTendik, 'id_pegawai');
    }
    // check depth >= 2 in case badan/biro
    else if ($checkDepth->depth >= 2) {
        foreach ($unitRelations as $row) {
            $dinilai1 = $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            $idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
        }
        foreach ($unitRelations as $row) {
            // jika unit child menilai maka penilai hanya  menilai kepala unit saja
            if ($jabatanStrukturalUser->id_unit == $row->child) {
                $dinilai2 =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            } else if ($row->is_child_assess) {
                $dinilai2 =    $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_jabatan_struktural')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
                $dinilai2 =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userID)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', array_column($dinilai2, 'id_pegawai'))->get()->getResultArray();
            } else {
                $dinilai2 =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            }
            $idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
        }
    } else {
        foreach ($unitRelations as $row) {
            $dinilai1 = $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            $idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
        }
        foreach ($unitRelations as $row) {
            // in case fakultas menilai prodi
            if ($row->is_child_assess && $row->depth > 0) {
                $dinilai2 =    $db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
            }
            // in case upt menilai/bagian
            else if ($row->is_child_assess && $row->depth == 0) {
                $dinilai2 =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            } else {
                $dinilai2 =    $db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userID)->where('id_unit', $row->child)->get()->getResultArray();
            }
            $idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
        }
    }

    return [
        'idPegawaiStruktual' => $idPegawaiStruktual,
        'idPegawaiBiasa' => $idPegawaiBiasa,
    ];
}

function getCategoryGrade($grade)
{
    if ($grade >= 90 && $grade <= 100) {
        return 'Baik Sekali';
    } elseif ($grade >= 80 && $grade < 90) {
        return 'Baik';
    } elseif ($grade >= 70 && $grade < 80) {
        return 'Cukup';
    } else {
        return 'Kurang';
    }
}

function unitPiket($idPegawai)
{
    $db = \Config\Database::connect();

    $pegawaiJabatanUnit = $db->table('pegawai_jabatan_u_view')->select('id_unit')->where('id_pegawai', $idPegawai)->get()->getRow();
    $pegawaiJabatanStrukturalUnit = $db->table('pegawai_jabatan_struktural_u_view')->select('id_unit')->where('id_pegawai', $idPegawai)->get()->getRow();
    $unitPiket = false;
    $unitStrukturalPiket = false;
    if ($pegawaiJabatanUnit) $unitPiket = $db->table('unit_piket')->where('id_unit', $pegawaiJabatanUnit->id_unit)->get()->getRow();
    if ($pegawaiJabatanStrukturalUnit) $unitStrukturalPiket = $db->table('unit_piket')->where('id_unit', $pegawaiJabatanStrukturalUnit->id_unit)->get()->getRow();

    return $unitPiket ?? $unitStrukturalPiket;
}

// function dec_hours($x)
// {
//     $sec = intval($x * (24 * 60 * 60));
//     $date = new DateTime("today +$sec seconds");
//     return $date->format('H:i:s');
// }

function integerToRoman($integer)
{
    // Convert the integer into an integer (just to make sure)
    $integer = intval($integer);
    $result = '';

    // Create a lookup array that contains all of the Roman numerals.
    $lookup = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1
    );

    foreach ($lookup as $roman => $value) {
        // Determine the number of matches
        $matches = intval($integer / $value);

        // Add the same number of characters to the string
        $result .= str_repeat($roman, $matches);

        // Set the integer to be the remainder of the integer and the value
        $integer = $integer % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
}
