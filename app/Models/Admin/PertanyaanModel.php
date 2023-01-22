<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PertanyaanModel extends BaseModel
{
    public $table            = 'pertanyaan';
    protected $returnType       = 'object';
    protected $allowedFields    = ['pertanyaan', 'id_pertanyaan_kategori'];
    protected $validationRules  = [
        'pertanyaan' => [
            'rules'  => 'required',
        ],
        'id_pertanyaan_kategori' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('id', 'pertanyaan', 'kategori');
    protected $column_search = array('pertanyaan', 'kategori');
    protected $order = array('id' => 'asc');
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
