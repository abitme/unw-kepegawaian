<?php

namespace IonAuth\Validation;

class MyRules
{
    public function alpha_dash_period(string $str, string &$error = null): bool
    {
        if (preg_match('/[^a-z_.\-\-0-9]/i', $str)) {
            $error = 'Karakter yang dibolehkan Hanya huruf, angka, dash(-), underscore(_), dan titik (.) ';
            return false;
        }

        return true;
    }

    public function date_lt_today(string $str, string &$error = null): bool
    {
        $dateNow = date('Y-m-d');
        $dateInput = date('Y-m-d', strtotime($str));
        if ($dateInput < $dateNow) {
            $error = 'Tanggal tidak boleh kurang dari hari ini';
            return false;
        }

        return true;
    }

    public function date_lt_begindate(string $str, string &$error = null): bool
    {
        $request = \Config\Services::request();
        $dateBegin = date('Y-m-d', \strtotime($request->getPost('tanggal_awal')));
        $dateInput = date('Y-m-d', strtotime($str));
        if ($dateInput < $dateBegin) {
            $error = 'Tanggal akhir tidak boleh kurang dari tanggal awal';
            return false;
        }

        return true;
    }
}
