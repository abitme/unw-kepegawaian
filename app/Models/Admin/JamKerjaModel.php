<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class JamKerjaModel extends BaseModel
{
    public $table               = 'jam_kerja';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_jam_kerja' => [
            'rules'  => 'required',
        ],
        'jam_masuk' => [
            'rules'  => 'required',
        ],
        'jam_istirahat_mulai' => [
            'rules'  => 'required',
        ],
        'jam_istirahat_selesai' => [
            'rules'  => 'required',
        ],
        'jam_pulang' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('jam_masuk');
    protected $column_search = array('jam_masuk');
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
