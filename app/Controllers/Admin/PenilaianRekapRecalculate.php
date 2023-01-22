<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianRekapRecalculateModel;

class PenilaianRekapRecalculate extends AdminBaseController
{
	// protected $PenilaianRekapRecalculateModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		// $this->PenilaianRekapRecalculateModel = new PenilaianRekapRecalculateModel();

		$this->menuSlug = 'penilaian-rekap-recalculate';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function calc($idSettingPenilaian)
	{
		$settingPenilaian = $this->db->table('setting_penilaian')->where('id', $idSettingPenilaian)->get()->getRow();
		if (!$settingPenilaian) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to("/penilaian-rekap/$idSettingPenilaian");
		}
		$penilaian = $this->db->table('view_penilaian')->where('jenis', $settingPenilaian->jenis)->get()->getResult();
		if (!$penilaian) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to("/penilaian-rekap/$idSettingPenilaian");
		}
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

			// menghitung nilai akhir
			// jumlah penilai
			$jumlahPenilai = $this->db->table('penilaian')->where('id_pegawai_dinilai',  $v->id_pegawai_dinilai)->where('jenis', $settingPenilaian->jenis)->countAllResults();
			// sum nilai
			$jumlahNilai = $this->db->table('penilaian')->selectSum('nilai_hasil')->where('id_pegawai_dinilai',  $v->id_pegawai_dinilai)->where('jenis', $settingPenilaian->jenis)->get()->getRow()->nilai_hasil;
			// update data 
			$nilaiAkhir = round($jumlahNilai / $jumlahPenilai, 2);
			$penilaianHasil = $this->db->table('penilaian_hasil')->where('id_pegawai',  $v->id_pegawai_dinilai)->where('jenis', $settingPenilaian->jenis);
			if ($penilaianHasil->countAllResults() <= 0) {
				$data = [
					'id_pegawai' =>  $v->id_pegawai_dinilai,
					'jenis' => $settingPenilaian->jenis,
					'nilai_akhir' => $nilaiAkhir,
				];
				$this->db->table('penilaian_hasil')->insert($data);
			} else {
				$data = [
					'nilai_akhir' => $nilaiAkhir,
				];
				$this->db->table('penilaian_hasil')->where('id_pegawai',  $v->id_pegawai_dinilai)->where('jenis', $settingPenilaian->jenis)->update($data);
			}
		}
		$this->session->setFlashdata('success', 'Recalculate Success');
		return redirect()->to("/penilaian-rekap/$idSettingPenilaian");
	}
}
