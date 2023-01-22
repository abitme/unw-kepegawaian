<?php

namespace App\Models;

use App\Models\Admin\BaseModel;

class PresensiModel extends BaseModel
{
    public $table               = 'presensi';
    protected $returnType       = 'object';

    protected $column_order = array('', 'nama', 'photo', 'tipe', 'waktu');
    protected $column_search = array('nama', 'photo', 'tipe', 'waktu');
    protected $order = array('waktu' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    public function rulesPresensi()
    {
        $validationRules = [
            'nik' => [
                'rules'  => 'required|is_not_unique[pegawai.nik]',
                'errors' => [
                    'required' => 'NIK harus diisi',
                    'is_not_unique' => 'NIK tersebut belum terdaftar, pastikan NIK tersebut sudah benar dan apabila belum terdaftar hubungi bagian kepegawaian',
                ]
            ],
            'photo' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Foto belum dicapture, silakan klik tombol/icon camera diatas untuk mengambil foto'
                ]
            ],
            'tipe' => [
                'rules'  => 'required|in_list[Masuk,Pulang]',
            ],
            'coord_latitude' => [
                'rules'  => 'required',
            ],
            'coord_longitude' => [
                'rules'  => 'required',
            ],
        ];

        return $validationRules;
    }

    public function rulesPresensiScan()
    {
        $validationRules = [
            'nik' => [
                'rules'  => 'required|is_not_unique[pegawai.nik]',
                'errors' => [
                    'required' => 'NIK harus diisi',
                    'is_not_unique' => 'NIK tersebut belum terdaftar, pastikan NIK tersebut sudah benar dan apabila belum terdaftar hubungi bagian kepegawaian',
                ]
            ],
            'photo' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Foto belum dicapture, silakan klik tombol/icon camera diatas untuk mengambil foto'
                ]
            ],
        ];

        return $validationRules;
    }
}
