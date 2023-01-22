<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PegawaiJabatanModel extends BaseModel

{
    public $table               = 'pegawai_jabatan_u';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_jabatan_u' => [
            'rules'  => 'required',
        ],
        'tmt' => [
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
