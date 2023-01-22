<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PertanyaanKategoriModel extends BaseModel
{
    public $table               = 'pertanyaan_kategori';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'kategori' => [
            'rules'  => 'required',
        ],
        'nilai_min' => [
            'rules'  => 'required',
        ],
        'nilai_max' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('id', 'kategori', 'nilai_min', 'nilai_max', 'range_desc');
    protected $column_search = array('kategori', 'nilai_min', 'nilai_max', 'range_desc');
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
