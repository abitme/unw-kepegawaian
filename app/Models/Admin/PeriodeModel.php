<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PeriodeModel extends BaseModel
{
    protected $table            = 'periode';
    protected $returnType       = 'object';
    protected $allowedFields    = ['tanggal_awal', 'tanggal_akhir'];
    protected $validationRules  = [
        'tahun' => [
            'rules'  => 'required',
        ],
        'semester' => [
            'rules'  => 'required',
        ],
        'tanggal_awal' => [
            'rules'  => 'required',
        ],
        'tanggal_akhir' => [
            'rules'  => 'required',
        ],
        'is_active' => [
            'rules'  => 'is_unique[periode.is_active,id,{id}]',
            'errors' => [
                'is_unique' => 'Sudah terdapat tahun yang aktif'
            ]
        ],
    ];

    protected $column_order = array('nama_unit');
    protected $column_search = array('nama_unit');
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
