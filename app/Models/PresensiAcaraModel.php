<?php

namespace App\Models;

use App\Models\Admin\BaseModel;

class PresensiAcaraModel extends BaseModel
{
    public $table               = 'presensi_acara';
    protected $returnType       = 'object';

    protected $column_order = array('', 'nama', 'nama_acara', 'waktu');
    protected $column_search = array('nama', 'nama_acara', 'waktu');
    protected $order = array('waktu' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    public function rulesPresensiAcara()
    {
        $validationRules = [
            'nik' => [
                'rules'  => 'required|is_not_unique[pegawai.nik]',
                'errors' => [
                    'required' => 'NIK harus diisi',
                    'is_not_unique' => 'NIK tersebut belum terdaftar, pastikan NIK tersebut sudah benar dan apabila belum terdaftar hubungi bagian kepegawaian',
                ]
            ],
        ];

        return $validationRules;
    }
}
