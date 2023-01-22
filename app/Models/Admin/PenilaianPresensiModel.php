<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PenilaianPresensiModel extends BaseModel
{
    public $table               = '';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'cuti' => [
            'rules'  => 'required',
        ],
        'alpha' => [
            'rules'  => 'required',
        ],
        'total_cuti' => [
            'rules'  => 'required',
        ],
        'terlambat' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama');
    protected $column_search = array('nama');
    protected $order = array('nama' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
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
            if (file_exists('assets/files/penilaian-presensi/input.xlsx')) {
                unlink("assets/files/penilaian-presensi/input.xlsx");
            }
            if (file_exists('assets/files/penilaian-presensi/input.xls')) {
                unlink("assets/files/penilaian-presensi/input.xls");
            }
            $extension = $file->getExtension();
            $file->move('assets/files/penilaian-presensi/', 'input.' . $extension);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/penilaian-presensi/{$file->getName()}");
        } else {
            if (file_exists('assets/files/penilaian-presensi/input.xlsx')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/penilaian-presensi/input.xlsx");
            }
            if (file_exists('assets/files/penilaian-presensi/input.xls')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/penilaian-presensi/input.xls");
            }
        }

        // $sheet	= $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        // var_dump($sheet);
        $sheet    = $spreadsheet->getActiveSheet();
        $highestColumn = $spreadsheet->getActiveSheet()->getHighestDataColumn();
        $highestRow = $spreadsheet->getActiveSheet()->getHighestDataRow();

        $error = [];
        $preview = '';

        $insertDataExcel = [];
        $updateDataExcel = [];

        if ($highestColumn == 'F') {
            $no = 0;
            for ($row = 1; $row <= $highestRow; ++$row) {

                $nik = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
                $nama = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $cuti = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $alpha = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                $total_cuti = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                $terlambat = $sheet->getCellByColumnAndRow(6, $row)->getValue();

                if ($row > 1) {
                    $no++;

                    $dataPreview = [
                        'nik' => $nik,
                        'nama' => $nama,
                        'cuti' => $cuti,
                        'alpha' => $alpha,
                        'total_cuti' => $total_cuti,
                        'terlambat' => $terlambat,
                    ];

                    $validation->reset();

                    if ($validation->run($dataPreview, 'penilaian_presensi') == FALSE) {
                        foreach ($validation->getErrors() as $key => $value) {
                            if (!in_array($value, $error)) {
                                array_push($error, $value);
                            }
                        }
                    }

                    $error_nik = $validation->hasError('nik') ? "style='background: #E07171; color:black'" : '';
                    $error_nama = $validation->hasError('nama') ? "style='background: #E07171; color:black'" : '';
                    $error_cuti = $validation->hasError('cuti') ? "style='background: #E07171; color:black'" : '';
                    $error_alpha = $validation->hasError('alpha') ? "style='background: #E07171; color:black'" : '';
                    $error_total_cuti = $validation->hasError('total_cuti') ? "style='background: #E07171; color:black'" : '';
                    $error_terlambat = $validation->hasError('terlambat') ? "style='background: #E07171; color:black'" : '';
                    // $error_pulang_cepat = $validation->hasError('pulang_cepat') ? "style='background: #E07171; color:black'" : '';

                    $preview .= "
							<tr>
								<td>$no</td>
								<td $error_nik>$nik</td>
								<td $error_nama>$nama</td>
								<td $error_cuti>$cuti</td>
								<td $error_alpha>$alpha</td>
								<td $error_total_cuti>$total_cuti</td>
								<td $error_terlambat>$terlambat</td>
							</tr>";
                }

                if ($row > 1) {
                    $pegawai = $this->db->table('pegawai')->select('id')->where('nik', $nik)->get()->getRow();
                    if ($pegawai) {
                        $checkExistsPenilaianPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $pegawai->id)->get()->getRow();
                        if (!$checkExistsPenilaianPresensi) {
                            array_push($insertDataExcel, array(
                                'id_pegawai' => $pegawai->id ?? null,
                                'cuti' => $cuti,
                                'alpha' => $alpha,
                                'total_cuti' => $total_cuti,
                                'terlambat' => $terlambat,
                            ));
                        } else {
                            array_push($updateDataExcel, array(
                                'id' => $checkExistsPenilaianPresensi->id,
                                'cuti' => $cuti,
                                'alpha' => $alpha,
                                'total_cuti' => $total_cuti,
                                'terlambat' => $terlambat,
                            ));
                        }
                    }
                }
            }
        } else {
            $session->setFlashdata('error', 'Kolom excel tidak sesuai dengan format yang disediakan');
            return 'error';
        }

        $affected = 0;
        if (!empty($insertDataExcel) && !empty($import)) {
            $this->db->table('penilaian_presensi')->insertBatch($insertDataExcel);

            // check if data has been updated and add to activity log
            if ($this->db->affectedRows() > 0) {
                $affected += 1;
            }
        }
        if (!empty($updateDataExcel) && !empty($import)) {
            $this->db->table('penilaian_presensi')->updateBatch($updateDataExcel, 'id');

            // check if data has been updated and add to activity log
            if ($this->db->affectedRows() > 0) {
                $affected += 1;
            }
        }
        if ($affected > 0) {
            $session->setFlashdata('success', 'Data berhasil disimpan');
            // activityLog('pegawai', "Import kesantrian kamar");
            return 'success';
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
