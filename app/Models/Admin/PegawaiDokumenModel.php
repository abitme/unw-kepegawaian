<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PegawaiDokumenModel extends BaseModel
{
    public $table               = 'pegawai_dokumen';
    protected $returnType       = 'object';
    protected $validationRules  = [
        'nama_dokumen' => [
            'rules'  => 'required',
        ],
    ];

    protected $request;
    protected $db;
    protected $dt;

    public function rulesCreate()
    {
        $validationRules = [
            'dokumen' => [
                'rules'  => 'uploaded[dokumen]|max_size[dokumen,10048]|ext_in[dokumen,doc,docx,xls,xlsx,png,jpg,jpeg,pdf]',
                'errors' => [
                    'uploaded' => 'File dokumen harus diisi',
                    'max_size' => 'Ukuran file dokumen maksimal 10MB',
                ]
            ],
            'nama_dokumen' => [
                'rules'  => 'required',
            ],
        ];

        return $validationRules;
    }

    public function rulesUpdate()
    {
        $validationRules = [
            'dokumen' => [
                'rules'  => 'max_size[dokumen,10048]|ext_in[dokumen,doc,docx,xls,xlsx,png,jpg,jpeg,pdf]',
                'errors' => [
                    'max_size' => 'Ukuran dokumen maksimal 10MB',
                ]
            ],
            'nama_dokumen' => [
                'rules'  => 'required',
            ],
        ];

        return $validationRules;
    }

}
