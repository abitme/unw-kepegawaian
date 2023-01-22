<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PenilaianSettingModel extends BaseModel
{
    protected $table            = 'periode';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'pertanyaan' => [
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
