<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class ToleransiTerlambatModel extends BaseModel
{
    public $table               = 'toleransi_terlambat';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_toleransi_terlambat' => [
            'rules'  => 'required',
        ],
        'tanggal_mulai' => [
            'rules'  => 'required',
        ],
        'durasi_toleransi' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('nama_toleransi_terlambat');
    protected $column_search = array('nama_toleransi_terlambat');
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
