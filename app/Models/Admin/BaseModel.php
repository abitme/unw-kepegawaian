<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class BaseModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
    }

    public function get($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }

        return $this->where(['id' => $id])->first();
    }

    public function getrulesImport()
    {
        $validationRules = [
            'excel' => [
                'rules'  => 'uploaded[excel]|max_size[excel,1024]|ext_in[excel,xlsx,xls]|max_size[excel,2048]',
            ],
        ];

        return $validationRules;
    }

    private function _get_datatables_query()
    {
        $this->dt = $this->db->table($this->table);

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->dt->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if ($this->request->getPost('order')) {
            $this->dt->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($this->order)) {
            foreach ($this->order as $key => $value) {
                $this->dt->orderBy($key, $value);
            }
        }
    }

    function get_datatables($where = null, $or = null, $havingIn = null, $havingNotIn  = null)
    {
        $this->dt = $this->db->table($this->table);

        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));

        if (!is_null($where)) {
            foreach ($where as $key => $value) {
                $this->dt->where($key, $value);
            }
        }
        if (!is_null($or)) {
            foreach ($or as $key => $value) {
                $this->dt->orWhere($key, $value);
            }
        }
        if (!is_null($havingIn)) {
            foreach ($havingIn as $key => $value) {
                $this->dt->havingIn($key, $value);
            }
        }
        if (!is_null($havingNotIn)) {
            foreach ($havingNotIn as $key => $value) {
                $this->dt->havingNotIn($key, $value);
            }
        }

        if (isset($this->groupBy)) {
            $this->dt->groupBy(($this->groupBy));
        }

        $query = $this->dt->get();
        return $query->getResult();
    }

    function count_filtered($where = null, $or = null, $havingIn = null, $havingNotIn  = null)
    {
        $this->dt = $this->db->table($this->table);

        $this->_get_datatables_query();
        if (!is_null($where)) {
            foreach ($where as $key => $value) {
                $this->dt->where($key, $value);
            }
        }
        if (!is_null($or)) {
            foreach ($or as $key => $value) {
                $this->dt->orWhere($key, $value);
            }
        }
        if (!is_null($havingIn)) {
            foreach ($havingIn as $key => $value) {
                $this->dt->havingIn($key, $value);
            }
        }
        if (!is_null($havingNotIn)) {
            foreach ($havingNotIn as $key => $value) {
                $this->dt->havingNotIn($key, $value);
            }
        }
        if (isset($this->groupBy)) {
            $this->dt->groupBy(($this->groupBy));
        }

        return $this->dt->get()->getNumRows();
    }

    public function count_all($where = null, $or = null, $havingIn = null, $havingNotIn  = null)
    {
        $this->dt = $this->db->table($this->table);

        $tbl_storage = $this->db->table($this->table);
        if (!is_null($where)) {
            foreach ($where as $key => $value) {
                $this->dt->where($key, $value);
            }
        }
        if (!is_null($or)) {
            foreach ($or as $key => $value) {
                $this->dt->orWhere($key, $value);
            }
        }
        if (!is_null($havingIn)) {
            foreach ($havingIn as $key => $value) {
                $this->dt->havingIn($key, $value);
            }
        }
        if (!is_null($havingNotIn)) {
            foreach ($havingNotIn as $key => $value) {
                $this->dt->havingNotIn($key, $value);
            }
        }
        if (isset($this->groupBy)) {
            $this->dt->groupBy(($this->groupBy));
        }
        
        // return $tbl_storage->countAll();
        return $this->dt->get()->getNumRows();
    }
}
