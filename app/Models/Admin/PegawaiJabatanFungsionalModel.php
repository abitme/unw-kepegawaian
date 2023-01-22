<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PegawaiJabatanFungsionalModel extends BaseModel

{
    public $table               = 'pegawai_jabatan_fungsional';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_jabatan_fungsional' => [
            'rules'  => 'required',
        ],
    ];

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
}
