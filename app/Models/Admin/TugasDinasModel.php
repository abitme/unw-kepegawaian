<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class TugasDinasModel extends BaseModel
{
    public $table               = 'tugas_dinas';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_pegawai' => [
            'rules'  => 'required',
        ],
        'keterangan' => [
            'rules'  => 'required',
            // 'errors' => [
            //     'is_unique' => 'Surat dengan keterangan tersebut sudah ada'
            // ]
        ],
        'tanggal_awal' => [
            'rules'  => 'required',
        ],
        'tanggal_akhir' => [
            'rules'  => 'required|date_lt_begindate',
        ],
    ];

    protected $column_order = array('', 'nama', 'lumsum', 'keterangan', 'tanggal_awal', 'tanggal_akhir');
    protected $column_search = array('nama', 'lumsum', 'keterangan', 'tanggal_awal', 'tanggal_akhir');
    protected $order = array('tanggal_awal' => 'desc');
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
