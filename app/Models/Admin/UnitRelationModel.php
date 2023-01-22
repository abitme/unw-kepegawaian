<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class UnitRelationModel extends BaseModel
{
    public $table               = 'view_unit_relations';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'parent' => [
            'rules'  => 'required',
        ],
        'child' => [
            'rules'  => 'required|differs[parent]',
        ],
    ];

    protected $column_order = array('id');
    protected $column_search = array('id');
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
