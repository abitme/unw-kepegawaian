<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JadwalKerjaModel extends BaseModel
{
    public $table               = 'jadwal_kerja';
    protected $returnType       = 'object';

    protected $column_order = array('', 'nama_jadwal_kerja');
    protected $column_search = array('nama_jadwal_kerja');
    // protected $order = array('id' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    public function rules()
    {
        $validationRules = [
            'nama_jadwal_kerja' => [
                'rules'  => 'required',
            ],
            'is_default' => [
                'rules'  => 'is_unique[jadwal_kerja.is_default,id,{id}]',
                'errors' => [
                    'is_unique' => 'Sudah terdapat tahun yang aktif'
                ]
            ],
        ];

        $request = \Config\Services::request();
        $input = $request->getPost();
        $i = 0;
        foreach ($input['day'] as $row) {
            if (!isset($input['libur'][$i])) {
                $name = "id_jam_kerja.$i";
                $validationRules[$name] = [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Jam Kerja ini harus diisi'
                    ]
                ];
            }
            $i++;
        }

        return $validationRules;
    }

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
    }
}
