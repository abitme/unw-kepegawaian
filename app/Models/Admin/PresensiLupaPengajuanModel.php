<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PresensiLupaPengajuanModel extends BaseModel
{
    public $table               = 'presensi_lupa';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'tanggal' => [
            'rules'  => 'required',
        ],
        'jam_masuk' => [
            'rules'  => 'required',
        ],
        'jam_pulang' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('', '',  'tanggal');
    protected $column_search = array('tanggal');
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
