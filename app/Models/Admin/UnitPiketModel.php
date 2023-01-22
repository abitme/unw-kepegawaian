<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class UnitPiketModel extends BaseModel
{
    public $table               = 'unit_piket';
    protected $returnType       = 'object';

    protected $column_order = array('', 'nama_unit');
    protected $column_search = array('nama_unit');
    // protected $order = array('id' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    public function rules()
    {
        $validationRules = [
            'id_unit' => [
                'rules'  => 'required',
            ],
        ];
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
