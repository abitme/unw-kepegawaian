<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SettingModel extends BaseModel
{
    public $table               = 'setting';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'title' => [
            'rules'  => 'required',
        ],
        'subtitle' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('title', 'subtitle');
    protected $column_search = array('title', 'subtitle');
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
