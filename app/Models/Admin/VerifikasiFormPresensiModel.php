<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class VerifikasiFormPresensiModel extends BaseModel
{
    public $table               = 'view_verifikasi_form_presensi';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_pegawai' => [
            'rules'  => 'required|is_unique[view_verifikasi_form_presensi.id_pegawai]|is_not_unique[pegawai.id]',
        ],
        'id_pegawai_verifikasi' => [
            'rules'  => 'required|is_not_unique[pegawai.id]',
        ],
    ];

    protected $column_order = array('', 'nama_pegawai', 'nama_pegawai_verifikasi');
    protected $column_search = array('nama_pegawai', 'nama_pegawai_verifikasi');
    // protected $order = array('id' => 'asc');
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
            if (file_exists('assets/files/jabatan-fungsional/input.xlsx')) {
                unlink("assets/files/jabatan-fungsional/input.xlsx");
            }
            if (file_exists('assets/files/jabatan-fungsional/input.xls')) {
                unlink("assets/files/jabatan-fungsional/input.xls");
            }
            $extension = $file->getExtension();
            $file->move('assets/files/jabatan-fungsional/', 'input.' . $extension);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-fungsional/{$file->getName()}");
        } else {
            if (file_exists('assets/files/jabatan-fungsional/input.xlsx')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-fungsional/input.xlsx");
            }
            if (file_exists('assets/files/jabatan-fungsional/input.xls')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-fungsional/input.xls");
            }
        }

        // $sheet	= $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        // var_dump($sheet);
        $sheet    = $spreadsheet->getActiveSheet();
        $highestColumn = $spreadsheet->getActiveSheet()->getHighestDataColumn();
        $highestRow = $spreadsheet->getActiveSheet()->getHighestDataRow();

        $error = [];
        $preview = '';

        $inputDataExcel = [];

        if ($highestColumn == 'D') {
            $no = 0;
            for ($row = 1; $row <= $highestRow; ++$row) {

                $nik_pegawai = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $nama_pegawai = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $nik_pegawai_verifikasi = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $nama_pegawai_verifiaksi = $sheet->getCellByColumnAndRow(4, $row)->getValue();

                if ($row > 1) {
                    $no++;

                    $dataPreview = [
                        'nik_pegawai' => $nik_pegawai,
                        'nama_pegawai' => $nama_pegawai,
                        'nik_pegawai_verifikasi' => $nik_pegawai_verifikasi,
                        'nama_pegawai_verifiaksi' => $nama_pegawai_verifiaksi,
                    ];

                    $validation->reset();

                    if ($validation->run($dataPreview, 'verifikasi_form_presensi') == FALSE) {
                        foreach ($validation->getErrors() as $key => $value) {
                            if (!in_array($value, $error)) {
                                array_push($error, $value);
                            }
                        }
                    }

                    $nik_pegawai_error = $validation->hasError('nik_pegawai') ? "style='background: #E07171; color:black'" : '';
                    $nama_pegawai_error = $validation->hasError('nama_pegawai') ? "style='background: #E07171; color:black'" : '';
                    $nik_pegawai_verifikasi_error = $validation->hasError('nik_pegawai_verifikasi') ? "style='background: #E07171; color:black'" : '';
                    $nama_pegawai_verifiaksi_error = $validation->hasError('nama_pegawai_verifiaksi') ? "style='background: #E07171; color:black'" : '';

                    $preview .= "
							<tr>
								<td>$no</td>
								<td $nik_pegawai_error>$nik_pegawai</td>
								<td $nama_pegawai_error>$nama_pegawai</td>
								<td $nik_pegawai_verifikasi_error>$nik_pegawai_verifikasi</td>
								<td $nama_pegawai_verifiaksi_error>$nama_pegawai_verifiaksi</td>
							</tr>";
                }

                if ($row > 1) {
                    $pegawai = $this->db->table('pegawai')->select('id')->where('nik', $nik_pegawai)->get()->getRow();
                    $pegawaiVerifikasi = $this->db->table('pegawai')->select('id')->where('nik', $nik_pegawai_verifikasi)->get()->getRow();
                    if ($pegawai && $pegawaiVerifikasi) {
                        array_push($inputDataExcel, array(
                            'id_pegawai' => $pegawai->id,
                            'id_pegawai_verifikasi' => $pegawaiVerifikasi->id,
                        ));
                    }
                }
            }
        } else {
            $session->setFlashdata('error', 'Kolom excel tidak sesuai dengan format yang disediakan');
            return 'error';
        }

        if (!empty($inputDataExcel) && !empty($import)) {
            $this->db->table('verifikasi_form_presensi')->insertBatch($inputDataExcel);

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
