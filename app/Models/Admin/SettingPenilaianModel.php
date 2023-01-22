<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SettingPenilaianModel extends BaseModel
{
    protected $table            = 'setting_penilaian';
    protected $returnType       = 'object';

    protected $column_order = array('');
    protected $column_search = array('');
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


    public function rules()
    {
        $validationRules = [
            'jenis' => [
                'rules'  => 'required',
            ],
            'periode_penilaian_awal' => [
                'rules'  => 'required',
                // 'errors' => [
                //     'required' => 'Siswa harus dipilih',
                // ]
            ],
            'periode_penilaian_akhir' => [
                'rules'  => 'required',
            ],
            'tanggal_mulai' => [
                'rules'  => 'required',
            ],
            'tanggal_selesai' => [
                'rules'  => 'required',
            ],
        ];

        // $request = \Config\Services::request();
        // $input = (object) $request->getPost();

        // $no = 0;
        // foreach ($input->id_skema_unit3 as $row) {
        //     $no++;
        //     $name = 'skema_unit3' . $no;
        //     $validationRules[$name] = [
        //         'rules'  => 'required',
        //         'errors' => [
        //             'required' => 'Pertanyaan ini harus dijawab'
        //         ]
        //     ];
        // }

        return $validationRules;
    }
}
