<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JadwalKerjaAutoModel extends BaseModel
{
    public $table               = 'jadwal_kerja_auto';
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
        ];


        $request = \Config\Services::request();
        $input = (object) $request->getPost();
        // \var_dump($input);
        // die;
        $i = 0;
        foreach ($input->id_jam_kerja as $key => $value) {
            $validationRules["id_jam_kerja.$key"] = [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Harus diisi'
                ]
            ];
            $i++;
        };
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
