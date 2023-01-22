<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AllowedIpPresensiModel extends BaseModel
{
    public $table               = 'allowed_ip_presensi';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'ip_address' => [
            'rules'  => 'required',
        ],
    ];

    protected $column_order = array('', 'ip_address');
    protected $column_search = array('ip_address');
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
