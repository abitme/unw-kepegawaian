<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JabatanStrukturalModel extends BaseModel
{
    public $table               = 'jabatan_struktural';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_jabatan_struktural' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama_jabatan_struktural');
    protected $column_search = array('nama_jabatan_struktural');
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
