<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JabatanStrukturalUnitModel extends BaseModel
{
    public $table               = 'jabatan_struktural_u';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_unit' => [
            'rules'  => 'required',
        ],
        'id_jabatan_struktural' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama_unit', 'nama_jabatan_struktural');
    protected $column_search = array('nama_unit', 'nama_jabatan_struktural');
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
            if (file_exists('assets/files/jabatan-struktural-unit/input.xlsx')) {
                unlink("assets/files/jabatan-struktural-unit/input.xlsx");
            }
            if (file_exists('assets/files/jabatan-struktural-unit/input.xls')) {
                unlink("assets/files/jabatan-struktural-unit/input.xls");
            }
            $extension = $file->getExtension();
            $file->move('assets/files/jabatan-struktural-unit/', 'input.' . $extension);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-struktural-unit/{$file->getName()}");
        } else {
            if (file_exists('assets/files/jabatan-struktural-unit/input.xlsx')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-struktural-unit/input.xlsx");
            }
            if (file_exists('assets/files/jabatan-struktural-unit/input.xls')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("assets/files/jabatan-struktural-unit/input.xls");
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

        if ($highestColumn == 'B') {
            $no = 0;
            for ($row = 1; $row <= $highestRow; ++$row) {

                $nama_unit = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $nama_jabatan_struktural = $sheet->getCellByColumnAndRow(2, $row)->getValue();

                if ($row > 1) {
                    $no++;

                    $dataPreview = [
                        'nama_unit' => $nama_unit,
                        'nama_jabatan_struktural' => $nama_jabatan_struktural,
                    ];

                    $validation->reset();

                    if ($validation->run($dataPreview, 'jabatan_struktural_unit') == FALSE) {
                        foreach ($validation->getErrors() as $key => $value) {
                            if (!in_array($value, $error)) {
                                array_push($error, $value);
                            }
                        }
                    }

                    $error_nama_unit = $validation->hasError('nama_unit') ? "style='background: #E07171; color:black'" : '';
                    $error_nama_jabatan_struktural = $validation->hasError('nama_jabatan_struktural') ? "style='background: #E07171; color:black'" : '';

                    $preview .= "
							<tr>
								<td>$no</td>
								<td $error_nama_unit>$nama_unit</td>
								<td $error_nama_jabatan_struktural>$nama_jabatan_struktural</td>
							</tr>";
                }

                if ($row > 1) {
                    $unit = $this->db->table('unit')->select('id')->where('nama_unit', $nama_unit)->get()->getRow();
                    $jabatan_struktural = $this->db->table('jabatan_struktural')->select('id')->where('nama_jabatan_struktural', $nama_jabatan_struktural)->get()->getRow();
                    array_push($inputDataExcel, array(
                        'id_unit' => $unit->id ?? null,
                        'id_jabatan_struktural' => $jabatan_struktural->id ?? null,
                    ));
                }
            }
        } else {
            $session->setFlashdata('error', 'Kolom excel tidak sesuai dengan format yang disediakan');
            return 'error';
        }

        if (!empty($inputDataExcel) && !empty($import)) {
            $this->db->table('jabatan_struktural_u')->insertBatch($inputDataExcel);

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
