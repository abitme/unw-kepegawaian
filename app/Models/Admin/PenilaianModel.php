<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PenilaianModel extends BaseModel
{
    protected $table            = 'penilaian';
    protected $returnType       = 'object';
    protected $allowedFields    = ['penilaian'];
    protected $validationRules  = [
        'penilaian' => [
            'rules'  => 'required',
        ],
    ];

    // protected $column_order = array('id', 'penilaian');
    // protected $column_search = array('penilaian');
    // protected $order = array('id' => 'asc');
    // protected $request;
    // protected $db;
    // protected $dt;

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
    }

    public function rules($settingPenilaian)
    {
        // $validationRules = [
        //     'id_peserta' => [
        //         'rules'  => 'required',
        //         'errors' => [
        //             'required' => 'Peserta harus dipilih'
        //         ]
        //     ],
        // ];

        // $today = date('Y-m-d');
        // $settingPenilaian = $this->db->query("SELECT * FROM setting_penilaian WHERE jenis = '$jenis' AND '$today' BETWEEN setting_penilaian.tanggal_mulai AND setting_penilaian.tanggal_selesai")->getRow();
        if (!$settingPenilaian) {
            session()->setFlashdata('warning', 'Tidak ada Penilaian untuk saat ini');
            return redirect()->to('/penilaian');
        }
        $settingPenilaianDetail = $this->db->table('setting_penilaian_detail_view')->where('id_setting_penilaian', $settingPenilaian->id)->get()->getResult();

        $no = 0;

        foreach ($settingPenilaianDetail as $row) {
            $no++;
            $name = 'nilai' . $row->id_pertanyaan;
            $validationRules[$name] = [
                'rules'  => "required|greater_than_equal_to[$row->nilai_min]|less_than_equal_to[$row->nilai_max]",
                'errors' => [
                    'required' => 'Nilai ini harus diisi',
                    'greater_than_equal_to' => 'Invalid',
                    'less_than_equal_to' => 'Invalid',
                ]
            ];
        }

        return $validationRules;
    }
}
