<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PresensiPiketModel extends BaseModel
{
    public $table               = 'presensi_piket';
    protected $returnType       = 'object';

    protected $column_order = array('', 'nama', 'photo', 'tipe', 'waktu');
    protected $column_search = array('nama', 'photo');
    protected $order = array('waktu' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    public function rulesValidate()
    {
        $validationRules = [
            'validitas' => [
                'rules'  => 'required|in_list[Valid,Tidak Valid]',
            ],
        ];

        return $validationRules;
    }
}
