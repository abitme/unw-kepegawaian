<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AbsensiModel extends BaseModel
{
    public $table               = 'absensi';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'id_pegawai' => [
            'rules'  => 'required',
        ],
        'jenis_absensi' => [
            'rules'  => 'required|in_list[Tugas/Izin Belajar, Sakit, Cuti Tahunan, Cuti Sosial]',
        ],
        // 'keterangan' => [
        //     'rules'  => 'required',
        // ], 
        'tanggal_awal' => [
            'rules'  => 'required',
        ],
        'tanggal_akhir' => [
            'rules'  => 'required|date_lt_begindate',
        ],
    ];

    protected $column_order = array('', 'nama', 'jenis_absensi', 'keterangan', 'tanggal_awal', 'tanggal_akhir');
    protected $column_search = array('nama', 'jenis_absensi', 'keterangan', 'tanggal_awal', 'tanggal_akhir');
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
