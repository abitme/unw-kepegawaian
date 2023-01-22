<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianModel;

class Penilaian extends AdminBaseController
{
	protected $Penilaian;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PenilaianModel = new PenilaianModel();

		$this->menuSlug = 'penilaian';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function index()
	{
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$jabatanStrukturalUserPenilai = $this->db->table('pegawai_jabatan_struktural_u_view')->where('id_pegawai', $userPenilai->id_pegawai)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan',  'Ketua', 'Wakil Rektor', 'Penanggung Jawab'])->get()->getResult();
		if (!$jabatanStrukturalUserPenilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/profile');
		}
		$idPegawaiStruktual = [];
		$idPegawaiBiasa = [];

		// set unitRelations based on check depth >= 2 (in case wr / biro)
		$unitRelations = [];
		foreach ($jabatanStrukturalUserPenilai as $row) {
			$checkDepth = $this->db->table('unit_relations')->where('parent', $row->id_unit)->orderBy('depth', 'desc')->get()->getRow();
			if ($checkDepth->depth >= 2) {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->where('depth <', 2)->get()->getResult();
			} else {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->get()->getResult();
			}
			$unitRelations = (object) array_merge((array) $unitRelations, (array) $result);
		}
		// check depth >= 3 in case wr
		if ($checkDepth->depth >= 3) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala'])->get()->getResultArray();
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
			$pegawaiBiasaTendik =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', $idPegawaiBiasa)->get()->getResultArray();
			$idPegawaiBiasa = \array_column($pegawaiBiasaTendik, 'id_pegawai');
		}
		// check depth >= 2 in case badan/biro
		else if ($checkDepth->depth >= 2) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// jika unit child menilai maka penilai hanya  menilai kepala unit saja
				if (in_array($row->child, (array) array_column($jabatanStrukturalUserPenilai, 'id_unit'))) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else if ($row->is_child_assess) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', array_column($dinilai2, 'id_pegawai'))->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		} else {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// in case fakultas menilai prodi
				if ($row->is_child_assess && $row->depth > 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				}
				// in case upt menilai/bagian
				else if ($row->is_child_assess && $row->depth == 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		}

		if ($idPegawaiStruktual) {
			$pegawaiDinilai = $this->db->table('pegawai_list_view')->havingIn('id', $idPegawaiStruktual)->get()->getResult();
			foreach ($pegawaiDinilai as $k => $pegawai) {
				if (in_array('Dekan', (array) $jabatanStrukturalUserPenilai)) {
					$pegawaiJabatan = $this->db->table('pegawai_jabatan_struktural_u_view')->select('nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $pegawai->id)->havingIn('nama_jabatan_struktural', ['Ketua', 'Sekretaris Dekan'])->get()->getRow();
				} else {
					$pegawaiJabatan = $this->db->table('pegawai_jabatan_struktural_u_view')->select('nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
				}
				$pegawaiDinilai[$k]->jabatan = "$pegawaiJabatan->nama_jabatan - $pegawaiJabatan->nama_unit";
			}
			$userDinilaiPejabatStruktural = $pegawaiDinilai;
		} else {
			$userDinilaiPejabatStruktural = [];
		}
		if ($idPegawaiBiasa) {
			$pegawaiDinilai = $this->db->table('pegawai_list_view')->havingIn('id', $idPegawaiBiasa)->get()->getResult();
			foreach ($pegawaiDinilai as $k => $pegawai) {
				$pegawaiJabatan = $this->db->table('pegawai_jabatan_u_view')->select('nama_unit, nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
				$pegawaiDinilai[$k]->jabatan = "$pegawaiJabatan->nama_jabatan - $pegawaiJabatan->nama_unit";
			}
			$userDinilaiKaryawan = $pegawaiDinilai;
		} else {
			$userDinilaiKaryawan = [];
		}

		$data = [
			'title' => 'Penilaian',
			'userDinilaiKaryawan' => $userDinilaiKaryawan,
			'userDinilaiPejabatStruktural' => $userDinilaiPejabatStruktural,
			'userPenilai' => $userPenilai,
		];

		return $this->view('pages/admin/penilaian/index', $data);
	}

	public function show($id, $jenis)
	{
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$jabatanStrukturalUserPenilai = $this->db->table('pegawai_jabatan_struktural_u_view')->where('id_pegawai', $userPenilai->id_pegawai)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan',  'Ketua', 'Wakil Rektor', 'Penanggung Jawab'])->get()->getResult();
		if (!$jabatanStrukturalUserPenilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/profile');
		}
		$idPegawaiStruktual = [];
		$idPegawaiBiasa = [];

		// set unitRelations based on check depth >= 2 (in case wr / biro)
		$unitRelations = [];
		foreach ($jabatanStrukturalUserPenilai as $row) {
			$checkDepth = $this->db->table('unit_relations')->where('parent', $row->id_unit)->orderBy('depth', 'desc')->get()->getRow();
			if ($checkDepth->depth >= 2) {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->where('depth <', 2)->get()->getResult();
			} else {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->get()->getResult();
			}
			$unitRelations = (object) array_merge((array) $unitRelations, (array) $result);
		}
		// check depth >= 3 in case wr
		if ($checkDepth->depth >= 3) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala'])->get()->getResultArray();
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
			$pegawaiBiasaTendik =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', $idPegawaiBiasa)->get()->getResultArray();
			$idPegawaiBiasa = \array_column($pegawaiBiasaTendik, 'id_pegawai');
		}
		// check depth >= 2 in case badan/biro
		else if ($checkDepth->depth >= 2) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// jika unit child menilai maka penilai hanya  menilai kepala unit saja
				if (in_array($row->child, (array) array_column($jabatanStrukturalUserPenilai, 'id_unit'))) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else if ($row->is_child_assess) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', array_column($dinilai2, 'id_pegawai'))->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		} else {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// in case fakultas menilai prodi
				if ($row->is_child_assess && $row->depth > 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				}
				// in case upt menilai/bagian
				else if ($row->is_child_assess && $row->depth == 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		}

		// check if user assessing their own subordinate
		if ($jenis == 'pejabat-struktural') {
			$jenisDB = 'Pejabat Struktural';
			if (!\in_array($id, $idPegawaiStruktual)) {
				$this->session->setFlashdata('warning', 'Data tidak ditemukan');
				return redirect()->to('/penilaian');
			}
		} else {
			$jenisDB = 'Karyawan';
			if (!\in_array($id, $idPegawaiBiasa)) {
				$this->session->setFlashdata('warning', 'Data tidak ditemukan');
				return redirect()->to('/penilaian');
			}
		}

		$pegawaiDinilai = $this->db->table('pegawai')->where('id', $id)->get()->getRow();
		$pegawaiDinilaiPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $id)->get()->getRow();
		if (!$pegawaiDinilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian');
		}

		if ($jenis == 'pejabat-struktural') {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $id)->get()->getRow();
		} else {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan')->where('id_pegawai', $id)->get()->getRow();
		}
		if (!$jabatanPegawaiDinilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian');
		}

		// cek ada penilaian
		$today = date('Y-m-d');
		$settingPenilaian = $this->db->query("SELECT * FROM setting_penilaian WHERE jenis = '$jenisDB' AND '$today' BETWEEN setting_penilaian.tanggal_mulai AND setting_penilaian.tanggal_selesai")->getRow();
		if (!$settingPenilaian) {
			$this->session->setFlashdata('warning', 'Tidak ada Penilaian untuk saat ini');
			return redirect()->to('/penilaian');
		}

		// cek penilaian sudah dimulai
		$startDate = $settingPenilaian->tanggal_mulai;
		$endDate = $settingPenilaian->tanggal_selesai;
		if ($today < $startDate) {
			$this->session->setFlashdata('warning', 'Belum waktunya penilaian');
			return redirect()->to("penilaian");
		}
		if ($today > $endDate) {
			$this->session->setFlashdata('warning', 'Penilaian sudah ditutup');
			return redirect()->to("penilaian");
		}

		// cek penilai sudah menilai
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$check = $this->db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $pegawaiDinilai->id)->where('jenis', $jenisDB)->countAllResults();
		if ($check > 0) {
			$this->session->setFlashdata('warning', 'Pegawai ini sudah anda nilai');
			return redirect()->to('/penilaian');
		}

		$settingPenilaianDetail = $this->db->table('setting_penilaian_detail_view')->where('id_setting_penilaian', $settingPenilaian->id)->get()->getResult();

		$data = [
			'title' => 'Penilaian',
			'jenis' => $jenis,
			'pegawaiDinilai' => $pegawaiDinilai,
			'pegawaiDinilaiPresensi' => $pegawaiDinilaiPresensi,
			'jabatanPegawaiDinilai' => $jabatanPegawaiDinilai,
			'settingPenilaian' => $settingPenilaian,
			'settingPenilaianDetail' => $settingPenilaianDetail,
			'validation' => \Config\Services::validation()
		];
		return $this->view('pages/admin/penilaian/show', $data);
	}

	public function menilai($id, $jenis)
	{
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$jabatanStrukturalUserPenilai = $this->db->table('pegawai_jabatan_struktural_u_view')->where('id_pegawai', $userPenilai->id_pegawai)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan',  'Ketua', 'Wakil Rektor', 'Penanggung Jawab'])->get()->getResult();
		if (!$jabatanStrukturalUserPenilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/profile');
		}
		$idPegawaiStruktual = [];
		$idPegawaiBiasa = [];

		// set unitRelations based on check depth >= 2 (in case wr / biro)
		$unitRelations = [];
		foreach ($jabatanStrukturalUserPenilai as $row) {
			$checkDepth = $this->db->table('unit_relations')->where('parent', $row->id_unit)->orderBy('depth', 'desc')->get()->getRow();
			if ($checkDepth->depth >= 2) {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->where('depth <', 2)->get()->getResult();
			} else {
				$result = $this->db->table('view_unit_relations')->where('parent', $row->id_unit)->get()->getResult();
			}
			$unitRelations = (object) array_merge((array) $unitRelations, (array) $result);
		}
		// check depth >= 3 in case wr
		if ($checkDepth->depth >= 3) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala'])->get()->getResultArray();
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
			$pegawaiBiasaTendik =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', $idPegawaiBiasa)->get()->getResultArray();
			$idPegawaiBiasa = \array_column($pegawaiBiasaTendik, 'id_pegawai');
		}
		// check depth >= 2 in case badan/biro
		else if ($checkDepth->depth >= 2) {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// jika unit child menilai maka penilai hanya  menilai kepala unit saja
				if (in_array($row->child, (array) array_column($jabatanStrukturalUserPenilai, 'id_unit'))) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else if ($row->is_child_assess) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_jabatan')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('nama_jabatan', 'Tendik')->havingIn('id_pegawai', array_column($dinilai2, 'id_pegawai'))->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		} else {
			foreach ($unitRelations as $row) {
				$dinilai1 = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				$idPegawaiStruktual = array_merge($idPegawaiStruktual, array_column($dinilai1, 'id_pegawai'));
			}
			foreach ($unitRelations as $row) {
				// in case fakultas menilai prodi
				if ($row->is_child_assess && $row->depth > 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama_jabatan_struktural')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan'])->get()->getResultArray();
				}
				// in case upt menilai/bagian
				else if ($row->is_child_assess && $row->depth == 0) {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				} else {
					$dinilai2 =	$this->db->table('pegawai_jabatan_u_view')->select('id_pegawai')->where('id_pegawai !=', $userPenilai->id_pegawai)->where('id_unit', $row->child)->get()->getResultArray();
				}
				$idPegawaiBiasa = array_merge($idPegawaiBiasa, array_column($dinilai2, 'id_pegawai'));
			}
		}

		// check if user assessing their own subordinate
		if ($jenis == 'pejabat-struktural') {
			$jenisDB = 'Pejabat Struktural';
			if (!\in_array($id, $idPegawaiStruktual)) {
				$this->session->setFlashdata('warning', 'Data tidak ditemukan');
				return redirect()->to('/penilaian');
			}
		} else {
			$jenisDB = 'Karyawan';
			if (!\in_array($id, $idPegawaiBiasa)) {
				$this->session->setFlashdata('warning', 'Data tidak ditemukan');
				return redirect()->to('/penilaian');
			}
		}

		$today = date('Y-m-d');
		$settingPenilaian = $this->db->query("SELECT * FROM setting_penilaian WHERE jenis = '$jenisDB' AND '$today' BETWEEN setting_penilaian.tanggal_mulai AND setting_penilaian.tanggal_selesai")->getRow();

		// validate and set error message
		if (!$this->validate($this->PenilaianModel->rules($settingPenilaian))) {
			return redirect()->to("/penilaian/$id/$jenis")->withInput();
		}

		// set input data
		$input = (object) $this->request->getPost();

		// cek penilai sudah menilai
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$check = $this->db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $id)->where('jenis', $jenisDB)->countAllResults();
		if ($check > 0) {
			$this->session->setFlashdata('warning', 'Pegawai ini sudah anda nilai');
			return redirect()->to('/penilaian');
		}

		// set input data array
		$input = (object) $this->request->getPost();

		// insert penilaian
		$dataPenilaian = [
			'id_setting_penilaian' => $settingPenilaian->id,
			'id_pegawai_penilai' => $userPenilai->id_pegawai,
			'id_pegawai_dinilai' => $id,
			'jenis' => $jenisDB,
		];
		$this->db->table('penilaian')->insert($dataPenilaian);
		$idPenilaian = $this->db->insertID();

		// insert penilaian_detail
		$no = 0;
		$dataPenilaianDetail = [];
		$arrData = [];
		foreach ($input->id_pertanyaan as $row) {
			$no++;
			$nilai = 'nilai' . $row;
			$keterangan = 'keterangan' . $row;
			$arrData = [
				'id_penilaian' => $idPenilaian,
				'id_pertanyaan' => $row,
				'nilai' => $input->$nilai,
				'keterangan' => $input->$keterangan
			];
			array_push($dataPenilaianDetail, $arrData);
		}
		$this->db->table('penilaian_detail')->insertBatch($dataPenilaianDetail);

		// menghitung nilai hasil
		$pertanyaanKategori = $this->db->table('pertanyaan_kategori')->select('id, kategori, nilai_min, nilai_max')->get()->getResult();
		$nilaiJumlah = 0;
		$nilai = [];
		foreach ($pertanyaanKategori as $row) {
			if (isset($input->kategori[$row->kategori])) {
				$maxTotalNilai = $row->nilai_max * sizeof($input->kategori[$row->kategori]);
				$nilaiJumlah = 0;
				foreach ($input->kategori[$row->kategori] as $value) {
					$nameNilai = 'nilai' . $value;
					$idPertanyaan = $value;
					$pertanyaanValue = $this->db->table('pertanyaan_value')->where('id_pertanyaan', $idPertanyaan)->where('nilai', $input->$nameNilai)->get()->getRow();
					if ($pertanyaanValue) {
						$valueJawaban = $pertanyaanValue->value;
					} else {
						$valueJawaban = $input->$nameNilai;
					}
					$nilaiJumlah += $valueJawaban;
				}
				$nilai[] = ($nilaiJumlah / $maxTotalNilai) * 100;
			}
		}
		$nilaiHasil = round(array_sum($nilai) / sizeof($nilai), 2);
		$this->db->table('penilaian')->where('id', $idPenilaian)->update(['nilai_hasil' => $nilaiHasil]);

		// menghitung nilai akhir
		// jumlah penilai
		$jumlahPenilai = $this->db->table('penilaian')->where('id_pegawai_dinilai', $id)->where('jenis', $jenisDB)->countAllResults();
		// sum nilai
		$jumlahNilai = $this->db->table('penilaian')->selectSum('nilai_hasil')->where('id_pegawai_dinilai', $id)->where('jenis', $jenisDB)->get()->getRow()->nilai_hasil;
		// update data 
		$nilaiAkhir = round($jumlahNilai / $jumlahPenilai, 2);
		$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai', $id)->where('jenis', $jenisDB);
		if ($penilaianHasil->countAllResults() <= 0) {
			$data = [
				'id_pegawai' => $id,
				'jenis' => $jenisDB,
				'nilai_akhir' => $nilaiAkhir,
			];
			$this->db->table('penilaian_hasil')->insert($data);
		} else {
			$data = [
				'nilai_akhir' => $nilaiAkhir,
			];
			$this->db->table('penilaian_hasil')->where('id_pegawai', $id)->update($data);
		}

		$this->session->setFlashdata('success', 'Pegawai berhasil dinilai');
		return redirect()->to("/penilaian");
		//--------------------------------------------------------------------
	}
}
