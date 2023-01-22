<?php

namespace IonAuth\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table            = 'users';
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'email', 'username', 'image'];
    protected $validationRules    = [
        'username'     => 'required|alpha_dash_period',
        'email'     => 'required|valid_email|is_unique[users.email,id,{id}]',
        'name'     => 'required',
        'image' => [
            'rules'  => 'max_size[image,2048]|is_image[image]|mime_in[image,image/png,image/jpg,image/jpeg]',
            'errors' => [
                'max_size' => 'Maksimal ukuran gambar 2MB'
            ]
        ],
    ];

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
    }
}
