<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PenilaianRekapModel extends BaseModel
{
    public $table            = 'setting_penilaian';
    protected $returnType       = 'object';

    public $column_order = array('');
    public $column_search = array('');
    // public $order = array('id' => 'asc');
    public $request;
    public $db;
    public $dt;

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
    }
}
