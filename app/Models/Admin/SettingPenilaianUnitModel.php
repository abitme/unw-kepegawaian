<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SettingPenilaianUnitModel extends BaseModel
{
    public $table               = 'view_setting_penilaian_unit';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_unit_penilai' => [
            'rules'  => 'required',
        ],
        'id_unit_dinilai' => [
            'rules'  => 'required|differs[id_unit_penilai]',
        ],
    ];

    protected $column_order = array('id');
    protected $column_search = array('id');
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
