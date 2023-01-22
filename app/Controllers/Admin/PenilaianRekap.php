<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianRekapModel;

class PenilaianRekap extends AdminBaseController
{
	protected $PenilaianRekapModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PenilaianRekapModel = new PenilaianRekapModel();

		$this->menuSlug = 'penilaian-rekap';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PenilaianRekapModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = date('d/m/Y', strtotime($list->periode_penilaian_awal)) . ' - ' . date('d/m/Y', strtotime($list->periode_penilaian_akhir));
				$row[] = $list->jenis;
				$row[] = '
						<a href="' . base_url("penilaian-rekap/$list->id") . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="View"">
							<i class="fas fa-eye"></i>
						</a>
					';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PenilaianRekapModel->count_all(),
				"recordsFiltered" => $this->PenilaianRekapModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function ajax_list_show($idSettingPenilaian)
	{
		$dataDb = $this->db->table('setting_penilaian')->where('id', $idSettingPenilaian)->get()->getRow();
		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return;
		}

		$settingPenilaian = $dataDb;

		if ($settingPenilaian->jenis == 'Karyawan') {
			$this->PenilaianRekapModel->table = 'pegawai_jabatan_u_view';
		} else {
			$this->PenilaianRekapModel->table = 'pegawai_jabatan_struktural_u_view';
		}
		$this->PenilaianRekapModel->column_order = array('id', 'nama');
		$this->PenilaianRekapModel->column_search = array('nama', 'nama_unit');
		$this->PenilaianRekapModel->order = array('nama_unit' => 'asc', 'nama' => 'asc');
		$this->PenilaianRekapModel->groupBy = 'id_pegawai';

		if ($this->request->getMethod(true) == 'POST') {

			$lists = $this->PenilaianRekapModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				if ($settingPenilaian->jenis == 'Karyawan') {
					$pegawaiJabatan = $this->db->table('pegawai_jabatan_u_view')->select('nama_unit, nama_jabatan')->where('id_pegawai', $list->id_pegawai)->get()->getRow();
				} else {
					$pegawaiJabatan = $this->db->table('pegawai_jabatan_struktural_u_view')->select('nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $list->id_pegawai)->get()->getRow();
				}

				$namaJabatan = $pegawaiJabatan->nama_jabatan ?? '';
				$namaUnit = $pegawaiJabatan->nama_unit ?? '';
				$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai', $list->id_pegawai)->get()->getRow();
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] =  "$namaJabatan - $namaUnit";
				$row[] = $penilaianHasil->nilai_akhir ?? '-';
				$row[] = '
						<a href="' . base_url("penilaian-rekap/$idSettingPenilaian/$list->id") . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Detail"">
							<i class="fas fa-eye"></i>
						</a>
					';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PenilaianRekapModel->count_all(),
				"recordsFiltered" => $this->PenilaianRekapModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Rekap Penilaian',
			'content' => $this->db->table('periode')->get()->getResult(),
		];

		return $this->view('pages/admin/penilaian-rekap/index', $data);
	}

	public function show($idSettingPenilaian)
	{
		$dataDb = $this->db->table('setting_penilaian')->where('id', $idSettingPenilaian)->get()->getRow();
		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/penilaian-rekap');
		}
		$settingPenilaian = $dataDb;

		$input = (object) $this->request->getGet();
		if (isset($input->id_unit) && !empty($input->id_unit)) {
			if ($settingPenilaian->jenis == 'Karyawan') {
				$pegawai = $this->db->table('pegawai_jabatan_u_view')->where('id_unit', $input->id_unit)->groupBy('id_pegawai')->orderBy('nama')->get()->getResult();
			} else {
				$pegawai = $this->db->table('pegawai_jabatan_struktural_u_view')->where('id_unit', $input->id_unit)->groupBy('id_pegawai')->orderBy('nama')->get()->getResult();
			}
		} else {
			if ($settingPenilaian->jenis == 'Karyawan') {
				$pegawai = $this->db->table('pegawai_jabatan_u_view')->groupBy('id_pegawai')->orderBy('nama')->get()->getResult();
			} else {
				$pegawai = $this->db->table('pegawai_jabatan_struktural_u_view')->groupBy('id_pegawai')->orderBy('nama')->get()->getResult();
			}
		}

		$data = [
			'title' => 'Rekap Penilaian',
			'settingPenilaian' => $settingPenilaian,
			'pegawai' => $pegawai,
			'input' => $input,
		];

		return $this->view('pages/admin/penilaian-rekap/show', $data);
	}

	public function showList($idSettingPenilaian, $idPegawaiDinilai)
	{
		$settingPenilaian = $this->db->table('setting_penilaian')->where('id', $idSettingPenilaian)->get()->getRow();
		if (!$settingPenilaian) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to("/penilaian-rekap/$idSettingPenilaian");
		}
		$pertanyaan = $this->db->table('setting_penilaian_detail_view')->where('id_setting_penilaian', $settingPenilaian->id)->get()->getResult();

		$pegawaiDinilai = $this->db->table('pegawai')->where('id', $idPegawaiDinilai)->get()->getRow();
		$pegawaiDinilaiPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $idPegawaiDinilai)->get()->getRow();
		if (!$pegawaiDinilai) {
			$this->session->setFlashdata('warning', 'Data Data tidak ditemukan');
			return redirect()->to("/penilaian-rekap/$idSettingPenilaian");
		}
		if ($settingPenilaian->jenis == 'Pejabat Struktural') {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $idPegawaiDinilai)->get()->getRow();
		} else {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan')->where('id_pegawai', $idPegawaiDinilai)->get()->getRow();
		}
		if (!$jabatanPegawaiDinilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian');
		}

		$penilaian = $this->db->table('view_penilaian')->where('id_pegawai_dinilai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis)->get()->getResult();
		foreach ($penilaian as $k => $v) {
			$penilaianDetail = $this->db->table('view_penilaian_detail')->where('id_penilaian', $v->id)->get()->getResult();
			$penilaian[$k]->penilaianDetail =  $penilaianDetail;
			// recalculate
			// menghitung nilai hasil
			$pertanyaanKategori = $this->db->table('pertanyaan_kategori')->select('id, kategori, nilai_min, nilai_max')->get()->getResult();
			$nilaiJumlah = 0;
			$nilai = [];
			foreach ($pertanyaanKategori as $row) {
				$penilaianDetailNilai = $this->db->table('view_penilaian_detail')->where('id_penilaian', $v->id)->where('id_pertanyaan_kategori', $row->id)->get()->getResult();

				$maxTotalNilai = $row->nilai_max * sizeof($penilaianDetailNilai);
				$nilaiJumlah = 0;
				foreach ($penilaianDetailNilai as $row2) {
					$idPertanyaan = $row2->id_pertanyaan;
					$pertanyaanValue = $this->db->table('pertanyaan_value')->where('id_pertanyaan', $idPertanyaan)->where('nilai', $row2->nilai)->get()->getRow();
					if ($pertanyaanValue) {
						$valueJawaban = $pertanyaanValue->value;
					} else {
						$valueJawaban = $row2->nilai;
					}
					$nilaiJumlah += $valueJawaban;
				}
				if ($nilaiJumlah) {
					$nilai[] = ($nilaiJumlah / $maxTotalNilai) * 100;
				}
			}
			$nilaiHasil = round(array_sum($nilai) / sizeof($nilai), 2);
			$this->db->table('penilaian')->where('id', $v->id)->where('jenis', $settingPenilaian->jenis)->update(['nilai_hasil' => $nilaiHasil]);
			$penilaian[$k]->nilai_hasil = $nilaiHasil;

			// menghitung nilai akhir
			// jumlah penilai
			$jumlahPenilai = $this->db->table('penilaian')->where('id_pegawai_dinilai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis)->countAllResults();
			// sum nilai
			$jumlahNilai = $this->db->table('penilaian')->selectSum('nilai_hasil')->where('id_pegawai_dinilai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis)->get()->getRow()->nilai_hasil;
			// update data 
			$nilaiAkhir = round($jumlahNilai / $jumlahPenilai, 2);
			$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis);
			if ($penilaianHasil->countAllResults() <= 0) {
				$data = [
					'id_pegawai' => $idPegawaiDinilai,
					'jenis' => $settingPenilaian->jenis,
					'nilai_akhir' => $nilaiAkhir,
				];
				$this->db->table('penilaian_hasil')->insert($data);
			} else {
				$data = [
					'nilai_akhir' => $nilaiAkhir,
				];
				$this->db->table('penilaian_hasil')->where('id_pegawai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis)->update($data);
			}
		}

		$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai', $idPegawaiDinilai)->where('jenis', $settingPenilaian->jenis)->get()->getRow();
		$data = [
			'title' => 'Detail Rekap Penilaian',
			'settingPenilaian' => $settingPenilaian,
			'pertanyaan' => $pertanyaan,
			'pegawaiDinilai' => $pegawaiDinilai,
			'pegawaiDinilaiPresensi' => $pegawaiDinilaiPresensi,
			'jabatanPegawaiDinilai' => $jabatanPegawaiDinilai,
			'penilaian' => $penilaian,
			'penilaianHasil' => $penilaianHasil,
		];

		return $this->view('pages/admin/penilaian-rekap/show-list', $data);
	}

	public function print($idPenilaian)
	{
		$penilaian = $this->db->table('view_penilaian')->where('id', $idPenilaian)->get()->getRow();
		$settingPenilaian = $this->db->table('setting_penilaian')->where('id', $penilaian->id_setting_penilaian)->get()->getRow();
		$pertanyaan = $this->db->table('setting_penilaian_detail_view')->where('id_setting_penilaian', $penilaian->id_setting_penilaian)->get()->getResult();
		$pegawaiDinilai = $this->db->table('pegawai')->where('id', $penilaian->id_pegawai_dinilai)->get()->getRow();
		$pegawaiDinilaiPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $penilaian->id_pegawai_dinilai)->get()->getRow();
		if ($penilaian->jenis == 'Pejabat Struktural') {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $penilaian->id_pegawai_dinilai)->get()->getRow();
		} else {
			$jabatanPegawaiDinilai = $this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama, nama_unit, nama_jabatan')->where('id_pegawai', $penilaian->id_pegawai_dinilai)->get()->getRow();
		}

		$penilaianDetail = $this->db->table('view_penilaian_detail')->where('id_penilaian', $penilaian->id)->get()->getResult();
		$penilaianKategori = $this->db->table('view_penilaian_detail')->select('kategori, nilai_min, nilai_max, range_desc')->where('id_penilaian', $penilaian->id)->groupBy('kategori')->get()->getResult();
		$penilaian->penilaianDetail =  $penilaianDetail;
		$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai', $penilaian->id_pegawai_dinilai)->where('jenis', $penilaian->jenis)->get()->getRow();
		$semuaPenilai =  $this->db->table('view_penilaian')->where('id_pegawai_dinilai', $penilaian->id_pegawai_dinilai)->where('jenis', $settingPenilaian->jenis)->get()->getResult();;

		$data = [
			'title' => 'Detail Rekap Penilaian',
			'settingPenilaian' => $settingPenilaian,
			'pertanyaan' => $pertanyaan,
			'pegawaiDinilai' => $pegawaiDinilai,
			'pegawaiDinilaiPresensi' => $pegawaiDinilaiPresensi,
			'jabatanPegawaiDinilai' => $jabatanPegawaiDinilai,
			'penilaian' => $penilaian,
			'penilaianKategori' => $penilaianKategori,
			'penilaianHasil' => $penilaianHasil,
			'semuaPenilai' => $semuaPenilai,
		];

		return $this->view('pages/admin/penilaian-rekap/print', $data);
	}
}
