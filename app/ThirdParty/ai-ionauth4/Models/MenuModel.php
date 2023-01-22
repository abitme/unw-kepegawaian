<?php

namespace IonAuth\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menus';
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'label', 'link', 'parent', 'sort', 'icon'];
    protected $validationRules  = [
        'label'     => 'required',
        'link'      => 'required',
    ];

    public function updateSort($readbleArray)
    {
        $i = 0;
        foreach ($readbleArray as $row) {
            $i++;

            $data = array(
                'parent' => $row['parentID'],
                'sort' => $i,
            );
            // var_dump($data);
            // $db = \Config\Database::connect();
            $this->db->table('menus')->where('id', $row['id'])->update($data);
        }

        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
