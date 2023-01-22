<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PegawaiJabatanStrukturalUnitModel extends BaseModel

{
    public $table               = 'pegawai_jabatan_struktural_u';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_jabatan_struktural_u' => [
            'rules'  => 'required',
        ],
        'tanggal_mulai' => [
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
