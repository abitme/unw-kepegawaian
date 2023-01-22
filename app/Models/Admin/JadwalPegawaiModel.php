<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JadwalPegawaiModel extends BaseModel

{
    public $table               = 'jadwal_pegawai';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_pegawai' => [
            'rules'  => 'required',
        ],
    ];

    public $column_order = array('', 'nama', 'nama_jadwal_kerja');
    public $column_search = array('nama', 'nama_jadwal_kerja');
    public $order = array('nama' => 'asc');
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
}
