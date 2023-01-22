<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JabatanModel extends BaseModel
{
    public $table               = 'jabatan';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_jabatan' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama_jabatan');
    protected $column_search = array('nama_jabatan');
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
