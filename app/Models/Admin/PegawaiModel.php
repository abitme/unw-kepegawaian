<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PegawaiModel extends BaseModel

{
    public $table               = 'pegawai';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'username' => [
            'rules'  => 'required|alpha_dash_period|is_unique[users.username,id,{id}]',
        ],
        // 'email' => [
        //     'rules'  => 'required|valid_email|is_unique[users.email,id,{id}]',
        // ],
        // 'password' => [
        //     'rules'  => 'required|min_length[8]|matches[password_confirm]',
        // ],
        // 'password_confirm' => [
        //     'rules'  => 'required',
        // ],
        'nik' => [
            'rules'  => 'required|is_unique[pegawai.nik,id,{id_pegawai}]',
            'errors' => [
                'is_unique' => 'NIK tersebut sudah terdaftar',
            ]
        ],
        'nama' => [
            'rules'  => 'required',
        ],
        // 'image' => [
        //     'rules'  => 'is_image[image]|mime_in[image,image/png,image/jpg,image/jpeg]|max_size[image,5120]',
        //     'errors' => [
        //         'uploaded' => 'image stempel harus diisi',
        //         'max_size' => 'Ukuran image maksimal 5MB',
        //     ]
        // ],
        // 'tempat_lahir' => [
        //     'rules'  => 'required',
        // ],
        // 'tanggal_lahir' => [
        //     'rules'  => 'required',
        // ],
        // 'jenis_kelamin' => [
        //     'rules'  => 'required',
        // ],
        // 'alamat' => [
        //     'rules'  => 'required',
        // ],
        // 'agama' => [
        //     'rules'  => 'required',
        // ],
        // 'pendidikan' => [
        //     'rules'  => 'required',
        // ],
    ];

    protected $column_order = array('', '', 'nik', 'nama', ' tempat_lahir', 'jenis_kelamin', 'agama', 'pendidikan', 'jabatan');
    protected $column_search = array('nik', 'nama', ' tempat_lahir', 'jenis_kelamin', 'agama', 'pendidikan', 'jabatan');
    protected $order = array('nama' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    public function __construct()
    {
        $this->ionAuth          = new \IonAuth\Libraries\IonAuth();
        $this->ionAuthModel     = new \IonAuth\Models\IonAuthModel();
        $this->config           = config('IonAuth');
        $this->session          = session();
        $this->request          = \Config\Services::request();

        // initialize the database
        if (empty($this->config->databaseGroupName)) {
            // By default, use CI's db that should be already loaded
            $this->db = \Config\Database::connect();
        } else {
            // For specific group name, open a new specific connection
            $this->db = \Config\Database::connect($this->config->databaseGroupName);
        }

        // initialize db tables data
        $this->tables = $this->config->tables;
    }

    public function getValidationWithoutPass()
    {
        $validationRules = [
            'username' => [
                'rules'  => 'required|alpha_dash_period|is_unique[users.username,id,{id}]',
            ],
            // 'email' => [
            //     'rules'  => 'required|valid_email|is_unique[users.email,id,{id}]',
            // ],
            'nik' => [
                'rules'  => 'required|is_unique[pegawai.nik,id,{id_pegawai}]',
                'errors' => [
                    'is_unique' => 'NIK tersebut sudah terdaftar',
                ]
            ],
            'nama' => [
                'rules'  => 'required',
            ],
            // 'image' => [
            //     'rules'  => 'is_image[image]|mime_in[image,image/png,image/jpg,image/jpeg]|max_size[image,5120]',
            //     'errors' => [
            //         'uploaded' => 'image stempel harus diisi',
            //         'max_size' => 'Ukuran image maksimal 5MB',
            //     ]
            // ],
            // 'tempat_lahir' => [
            //     'rules'  => 'required',
            // ],
            // 'tanggal_lahir' => [
            //     'rules'  => 'required',
            // ],
            // 'jenis_kelamin' => [
            //     'rules'  => 'required',
            // ],
            // 'alamat' => [
            //     'rules'  => 'required',
            // ],
        ];

        return $validationRules;
    }

    public function insertUser($input, $pegawaiId = null)
    {
        $input = (object) $input;
        // check default group is missing
        if (!$this->config->defaultGroup && empty($input->group_id)) {
            $this->session->setFlashdata('danger', 'Default group is missing');
            return false;
        }

        // check if the default set in config exists in database
        $query = $this->db->table($this->tables['groups'])->where(['name' => $this->config->defaultGroup], 1)->get()->getRow();
        if (!isset($query->id) && empty($input->group_id)) {
            $this->session->setFlashdata('danger', 'Invalid default group');
            return false;
        }

        // capture default group details
        $defaultGroup = $query;

        // IP Address
        $ipAddress = \Config\Services::request()->getIPAddress();

        // Do not pass $identity as user is not known yet so there is no need
        $password = $this->ionAuthModel->hashPassword($input->password);

        if ($password === false) {
            $this->session->setFlashdata('danger', 'Kesalahan password');
            return false;
        }

        // insert users
        $data = [
            'id_pegawai'          => $pegawaiId ?? $input->id_pegawai,
            'ip_address'          => $ipAddress,
            'username'            => $input->username,
            'password'            => $password,
            'email'               => isset($input->email) && !empty($input->email) ? $input->email : null,
            'created_on'          => time(),
            'active'              => 1,
            'name'                => $input->nama,
            'image'               => $input->fileName ?? '',
        ];
        $this->db->table('users')->insert($data);
        $userId = $this->db->insertID();

        // insert users_groups
        if ($this->ionAuth->isAdmin() && isset($input->group_id)) {
            $dataUserGroup = [
                'user_id' => $userId,
                'group_id' => $input->group_id
            ];
        } else {
            $dataUserGroup = [
                'user_id' => $userId,
                'group_id' => $defaultGroup->id
            ];
        }
        $this->db->table('users_groups')->insert($dataUserGroup);
        return true;
    }

    public function updateUser($input, $userId)
    {
        // update users
        if (!empty($input->password)) {
            // Do not pass $identity as user is not known yet so there is no need
            $password = $this->ionAuthModel->hashPassword($input->password);
            if ($password === false) {
                $this->session->setFlashdata('danger', 'Kesalahan password');
                return false;
            }
            $data = [
                'username'            => $input->username,
                'email'               => !empty($input->email) ? $input->email : null,
                'password'            => $password,
                'name'                => $input->nama,
                'image'               => $input->fileName
            ];
            // \var_dump($data);die;
        } else {
            $data = [
                'username'            => $input->username,
                'email'               => !empty($input->email) ? $input->email : null,
                'name'                => $input->nama,
                'image'               => $input->fileName
            ];
        }
        $this->db->table('users')->where('id', $userId)->update($data);

        // update users_groups
        if ($this->ionAuth->isAdmin() && isset($input->group_id)) {
            // Update the groups user belongs to
            $groupData = [$input->group_id];

            if (!empty($groupData)) {
                $this->ionAuth->removeFromGroup('', $userId);

                foreach ($groupData as $grp) {
                    $this->ionAuth->addToGroup($grp, $userId);
                }
            }
        }
        return true;
    }

    public function getrulesImport()
    {
        $validationRules = [
            'excel' => [
                'rules'  => 'uploaded[excel]|max_size[excel,1024]|ext_in[excel,xlsx,xls]|max_size[excel,2048]',
            ],
        ];

        return $validationRules;
    }

    public function import($file, $import)
    {
        $validation =  \Config\Services::validation();
        $session = \Config\Services::session();
        // validation check in database
        $this->db = \Config\Database::connect();

        if ($file) {
            if (file_exists('assets/files/pegawai/input.xlsx')) {
                unlink("assets/files/pegawai/input.xlsx");
            }
            if (file_exists('assets/files/pegawai/input.xls')) {
                unlink("assets/files/pegawai/input.xls");
            }
            $extension = $file->getExtension();
            $file->move('assets/files/pegawai/', 'input.' . $extension);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/pegawai/{$file->getName()}");
        } else {
            if (file_exists('assets/files/pegawai/input.xlsx')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/pegawai/input.xlsx");
            }
            if (file_exists('assets/files/pegawai/input.xls')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/pegawai/input.xls");
            }
        }

        // $sheet	= $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        // var_dump($sheet);
        $sheet    = $spreadsheet->getActiveSheet();
        $highestColumn = $spreadsheet->getActiveSheet()->getHighestDataColumn();
        $highestRow = $spreadsheet->getActiveSheet()->getHighestDataRow();

        $error = [];
        $preview = '';

        $pegawai = $this->db->table('pegawai')->orderBy('id', 'desc')->get()->getRow();
        $pegawaiId = $pegawai && !empty($pegawai) ? $pegawai->id : 0;
        $dataPegawai = [];
        $dataUser = [];

        if ($highestColumn == 'I') {
            $no = 0;
            for ($row = 1; $row <= $highestRow; ++$row) {

                $username = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $email = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $password = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $nik = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                $nama = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                $tempat_lahir = $sheet->getCellByColumnAndRow(6, $row)->getValue();
                $tanggal_lahir = $sheet->getCellByColumnAndRow(7, $row)->getValue() != null ? $sheet->getCellByColumnAndRow(7, $row)->getFormattedValue() : '';
                $jenis_kelamin = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                $alamat = $sheet->getCellByColumnAndRow(9, $row)->getValue();

                if ($row > 1) {
                    $no++;

                    $tanggal_lahir == '' ? $tanggal_lahir = '' : '';

                    $dataPreview = [
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'nik' => $nik,
                        'nama' => $nama,
                        'tempat_lahir' => $tempat_lahir,
                        'tanggal_lahir' => $tanggal_lahir,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                    ];

                    $validation->reset();

                    if ($validation->run($dataPreview, 'pegawai') == FALSE) {
                        foreach ($validation->getErrors() as $key => $value) {
                            if (!in_array($value, $error)) {
                                array_push($error, $value);
                            }
                        }
                    }

                    $error_username = $validation->hasError('username') ? "style='background: #E07171; color:black'" : '';
                    $error_email = $validation->hasError('email') ? "style='background: #E07171; color:black'" : '';
                    $error_password = $validation->hasError('password') ? "style='background: #E07171; color:black'" : '';
                    $error_nik = $validation->hasError('nik') ? "style='background: #E07171; color:black'" : '';
                    $error_nama = $validation->hasError('nama') ? "style='background: #E07171; color:black'" : '';
                    $error_tempat_lahir = $validation->hasError('tempat_lahir') ? "style='background: #E07171; color:black'" : '';
                    $error_tanggal_lahir = $validation->hasError('tanggal_lahir') ? "style='background: #E07171; color:black'" : '';
                    $error_jenis_kelamin = $validation->hasError('jenis_kelamin') ? "style='background: #E07171; color:black'" : '';
                    $error_alamat = $validation->hasError('alamat') ? "style='background: #E07171; color:black'" : '';

                    if (!$validation->hasError('tanggal_lahir') && !empty($tanggal_lahir)) {
                        $explode = explode('/', $tanggal_lahir);
                        $tanggal_lahir = "$explode[1]-$explode[0]-$explode[2]";
                    } else {
                        $tanggal_lahir = '01-01-0001';
                    }

                    $prev_tanggal_lahir = $tanggal_lahir == '01-01-0001' ? '-' : $tanggal_lahir;

                    $preview .= "
							<tr>
								<td>$no</td>
								<td $error_username>$username</td>
								<td $error_email>$email</td>
								<td $error_password>$password</td>
								<td $error_nik>$nik</td>
								<td $error_nama>$nama</td>
								<td $error_tempat_lahir>$tempat_lahir</td>
                                <td $error_tanggal_lahir>$prev_tanggal_lahir</td>
								<td $error_jenis_kelamin>$jenis_kelamin</td>
								<td $error_alamat>$alamat</td>
							</tr>";
                }

                if ($row > 1) {
                    $tanggal_lahir != '' && $tanggal_lahir != '01-01-0001' ? $tanggal_lahir = date('Y-m-d', strtotime($tanggal_lahir)) : $tanggal_lahir = null;

                    $pegawaiId = ++$pegawaiId;
                    
                    array_push($dataPegawai, array(
                        'id' => $pegawaiId,
                        'nik' => $nik,
                        'nama' => $nama,
                        'tempat_lahir' => "$tempat_lahir",
                        'tanggal_lahir' => $tanggal_lahir,
                        'jenis_kelamin' => "$jenis_kelamin",
                        'alamat' => "$alamat",
                    ));
                    array_push($dataUser, array(
                        'id_pegawai' => $pegawaiId,
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'nama' => $nama,
                    ));
                }
            }
        } else {
            $session->setFlashdata('error', 'Kolom excel tidak sesuai dengan format yang disediakan');
            return 'error';
        }

        if (!empty($dataPegawai) && !empty($import)) {
            $this->db->table('pegawai')->insertBatch($dataPegawai);
            foreach ($dataUser as $row) {
                $this->insertUser($row);
            }

            // check if data has been updated and add to activity log
            if ($this->db->affectedRows() > 0) {
                $session->setFlashdata('success', 'Data berhasil ditambahkan');
                // activityLog('pegawai', "Import kesantrian kamar");
                return 'success';
            }
        }
        if (!empty($error)) {
            $session->setTempdata('danger', 'Data excel tidak sesuai dengan format yang disediakan', 2);
        }

        $data = [
            'error' => $error,
            'preview' => $preview,
        ];

        return $data;
    }
}
