<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class HariLiburModel extends BaseModel
{
    public $table               = 'hari_libur';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'tanggal_awal' => [
            'rules'  => 'required',
        ],
        'tanggal_akhir' => [
            'rules'  => 'required|date_lt_begindate',
        ],
    ];

    protected $column_order = array('', 'tanggal', 'keterangan');
    protected $column_search = array('tanggal', 'keterangan');
    protected $order = array('tanggal_awal' => 'asc');
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
