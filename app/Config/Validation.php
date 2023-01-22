<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \IonAuth\Validation\MyRules::class
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    
    public $jabatan_unit = [
        'nama_unit' => 'required|is_not_unique[unit.nama_unit]',
        'nama_jabatan' => 'required|is_not_unique[jabatan.nama_jabatan]',

    ];
    public $jabatan_unit_errors = [
        'nama_unit' => [
            'required'    => 'Nama unit harus diisi.',
            'is_not_unique'    => 'Nama unit tersebut tidak ada dalam database, pastikan nama unit sama dengan yang pada di menu unit.',
        ],
        'nama_jabatan' => [
            'required'    => 'Nama jabatan harus diisi.',
            'is_not_unique'    => 'Nama jabatan tersebut tidak ada dalam database, pastikan nama jabatan sama dengan yang pada di menu jabatan.',
        ],
    ];

    public $jabatan_struktural_unit = [
        'nama_unit' => 'required|is_not_unique[unit.nama_unit]',
        'nama_jabatan_struktural' => 'required|is_not_unique[jabatan_struktural.nama_jabatan_struktural]',

    ];
    public $jabatan_struktural_unit_errors = [
        'nama_unit' => [
            'required'    => 'Nama unit harus diisi.',
            'is_not_unique'    => 'Nama unit tersebut tidak ada dalam database, pastikan nama unit sama dengan yang pada di menu unit.',
        ],
        'nama_jabatan_struktural' => [
            'required'    => 'Nama jabatan struktural harus diisi.',
            'is_not_unique'    => 'Nama jabatan struktural tersebut tidak ada dalam database, pastikan nama jabatan struktural sama dengan yang pada di menu jabatan struktural.',
        ],
    ];

    public $jabatan_fungsional = [
        'nama_jabatan' => 'required|is_not_unique[jabatan.nama_jabatan]',
        'nama_jabatan_fungsional' => 'required',

    ];
    public $jabatan_fungsional_errors = [
        'nama_jabatan' => [
            'required'    => 'Jabatan harus diisi.',
            'is_not_unique'    => 'Jabatan tersebut tidak ada dalam database, pastikan nama jabatan sama dengan yang pada di menu jabatan.',
        ],
        'nama_jabatan_fungsional' => [
            'required'    => 'Nama jabatan fungsional harus diisi.',
        ],
    ];

    public $penilaian_presensi = [
        'nik' => 'is_not_unique[pegawai.nik]',
        'cuti' => 'required',
        'alpha' => 'required',
        'total_cuti' => 'required',
        'terlambat' => 'required',
    ];
    public $penilaian_presensi_errors = [
        'nik' => [
            'required'    => 'NIK harus diisi.',
            'is_not_unique'    => 'NIK tersebut tidak ada dalam database, pastikan NIK ada pada data pegawai.',
        ],
    ];

    public $pegawai = [
        'username' => 'required|alpha_dash_period|is_unique[users.username,id,{id}]',
        // 'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'nik' => 'required|is_unique[pegawai.nik]',
        'nama' => 'required',
        // 'tempat_lahir' => 'required',
        // 'tanggal_lahir' => 'required|valid_date[m/d/Y]',
        // 'jenis_kelamin' => 'required|in_list[Laki-Laki,Perempuan]',
        // 'alamat' => 'required',
    ];
    public $pegawai_errors = [
        'username' => [
            'required'    => 'Username harus diisi.',
            'is_unique'    => 'Username tersebut sudah terdaftar, silakan gunakan username lain.',
        ],
        'email' => [
            'required'    => 'Email harus diisi.',
            'is_unique'    => 'email tersebut sudah terdaftar, silakan gunakan email lain.',
        ],
        'password' => [
            'required'    => 'Password harus diisi.',
        ],
        'nik' => [
            'required'    => 'NIK harus diisi.',
            'is_unique' => 'NIK terseut sudah terdaftar',
        ],
        'nama' => [
            'required'    => 'Nama harus diisi.',
        ],
        'tempat_lahir' => [
            'required'    => 'Tempat Lahir harus diisi.',
        ],
        'tanggal_lahir' => [
            'required'    => 'Tanggal Lahir harus diisi.',
        ],
        'jenis_kelamin' => [
            'required'    => 'Jenis Kelamin harus diisi.',
            'in_list'    => 'Jenis Kelamin harus Laki-Laki/Perempuan.',
        ],
        'alamat' => [
            'required'    => 'Alamat harus diisi.',
        ],
    ];

    public $verifikasi_form_presensi = [
        'nik_pegawai' => 'required|is_unique[view_verifikasi_form_presensi.nik_pegawai]|is_not_unique[pegawai.nik]',
        'nik_pegawai_verifikasi' => 'required|is_not_unique[pegawai.nik]',
    ];
    public $verifikasi_form_presensi_errors = [
        'nik_pegawai' => [
            'required'    => 'NIK harus diisi.',
            'is_not_unique'    => 'NIK tersebut tidak ada dalam database, pastikan NIK ada pada data pegawai.',
        ],
        'nik_pegawai_verifikasi' => [
            'required'    => 'NIK harus diisi.',
            'is_not_unique'    => 'NIK tersebut tidak ada dalam database, pastikan NIK ada pada data pegawai.',
        ],
    ];
}
