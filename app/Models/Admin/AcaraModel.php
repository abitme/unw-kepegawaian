<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AcaraModel extends BaseModel
{
    public $table               = 'acara';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_acara' => [
            'rules'  => 'required',
        ],
        'tanggal' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('', 'nama_acara', 'barcode', 'tanggal');
    protected $column_search = array('nama_acara');
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
