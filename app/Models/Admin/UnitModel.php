<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class UnitModel extends BaseModel
{
    public $table               = 'unit';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_unit' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama_unit');
    protected $column_search = array('nama_unit');
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
