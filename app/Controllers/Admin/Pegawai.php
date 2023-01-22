<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PegawaiModel;

class Pegawai extends AdminBaseController
{
	protected $PegawaiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PegawaiModel = new PegawaiModel();

		$this->menuSlug = 'pegawai';
	}

	public function import()
	{
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$data = [
			'title' => 'Import Excel Pegawai',
			'validation' => \Config\Services::validation(),
			'user_id' => $this->user_id,
			'form_action' => base_url("pegawai/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->view('pages/admin/pegawai/import', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->PegawaiModel->getrulesImport()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/pegawai/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->PegawaiModel->import($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/pegawai/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/pegawai');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->view('pages/admin/pegawai/import', $data);
	}

	public function ajax_list()
	{
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$this->PegawaiModel->table = 'pegawai_list_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PegawaiModel->get_datatables();
			$countAll = $this->PegawaiModel->count_all();
			$countFiltered = $this->PegawaiModel->count_filtered();

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$user = $this->db->table('users')->select('id, image')->where('id_pegawai', $list->id)->get()->getRow();

				$no++;
				$row = [];
				$row[] = $no;
				if (!empty($user->image) && file_exists("assets/img/users/{$user->image}")) {
					$row[] = '<img src="' . base_url() . '/assets/img/users/thumbnail/' . $user->image . '" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				} else {
					$row[] = '<img src="' . base_url() . '/assets/img/users/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				}
				$row[] = $list->nik;
				$row[] = $list->nama;
				if (is_allow('insert', $this->menuSlug) && is_allow('update', $this->menuSlug) && is_allow('delete', $this->menuSlug)) {
					$tempatLahir = $list->tempat_lahir ? "$list->tempat_lahir," : '-';
					$tanggalLahir = isset($list->tanggal_lahir) ? date('d F Y', strtotime($list->tanggal_lahir)) : '';
					$row[] = "$tempatLahir $tanggalLahir";
					$row[] = $list->jenis_kelamin;
					$row[] = $list->jabatan;

					if (\checkGroupUser(1) || ($user && $user->id != $this->user_id)) {
						$row[] = '
					<a href="' . base_url("pegawai/$list->id") . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="View"">
						<i class="fas fa-eye"></i>
					</a>
					<a href="' . base_url("pegawai/$list->id/edit") . '" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit"">
						<i class="fas fa-edit"></i>
					</a>
					<form action="' . base_url("pegawai/$list->id/delete") . '" method="POST" class="d-inline form-delete">
						' . csrf_field() . '
						<input type="hidden" name="_method" value="DELETE" />
						<button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm(`Apakah anda yakin menghapus data?`)">
							<i class="fas fa-trash"></i>
						</button>
					</form>
				';
					} else {
						$row[] = '
					<a href="' . base_url("pegawai/$list->id") . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="View"">
						<i class="fas fa-eye"></i>
					</a>
					<a href="' . base_url("pegawai/$list->id/edit") . '" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit"">
						<i class="fas fa-edit"></i>
					</a>
				';
					}
				}
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $countAll,
				"recordsFiltered" => $countFiltered,
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		// $this->ionAuthModel     = new \IonAuth\Models\IonAuthModel();
		// $pegawai = $this->db->table('pegawai')->get()->getResult();
		// foreach ($pegawai as $row) {
		// 	$this->db->table('users')->where('id_pegawai', $row->id)->update(['password' => $this->ionAuthModel->hashPassword($row->nik)]);
		// }
		$data = [
			'title' => 'Pegawai',
		];

		return $this->view('pages/admin/pegawai/index', $data);
	}

	public function show($id)
	{
		if (!is_allow('insert', $this->menuSlug) && is_allow('update', $this->menuSlug) && is_allow('delete', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$dataDb = $this->db->table('pegawai')->where('id', $id)->get()->getRow();
		$user = $this->db->table('users')->select('username, email, image')->where('id_pegawai', $id)->get()->getRow();
		$usersGroups  = $this->db->table('users_groups')->select('group_id')->where('user_id', $id)->get()->getRow();

		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/pegawai');
		}

		$data = [
			'title' => 'Detail Pegawai',
			'pegawai' => $dataDb,
			'user' => $user,
		];

		return $this->view('pages/admin/pegawai/show', $data);
	}

	public function new()
	{
		if (!is_allow('insert', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$data = [
			'title' => 'Tambah Pegawai',
			'validation' => \Config\Services::validation(),
			'form_action' => base_url('pegawai/create'),
		];

		return $this->view('pages/admin/pegawai/create', $data);
	}

	public function create()
	{
		if (!is_allow('insert', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		// validate and set error message
		if (!$this->validate($this->PegawaiModel->getValidationRules())) {
			return redirect()->to("/pegawai/new")->withInput();
		}

		// set input data
		$input = (object) $this->request->getPost();

		// get uploaded file
		$image = $this->request->getFile('image');

		if ($image->getError() == 4) {
			$fileName = '';
		} else {
			$fileName = url_title("$input->nik-$input->nama", '-', true) . '.' . $image->getExtension();
		}

		$data = [
			'nik' => $input->nik,
			'nama' => $input->nama,
			'tempat_lahir' => $input->tempat_lahir,
			'tanggal_lahir' => isset($input->tanggal_lahir) && !empty($input->tanggal_lahir) ? $input->tanggal_lahir : null,
			'jenis_kelamin' => $input->jenis_kelamin ?? '',
			'alamat' => $input->alamat ?? '',
			'agama' => $input->agama ?? '',
			'pendidikan' => $input->pendidikan ?? '',
		];
		$this->db->table('pegawai')->insert($data);
		$pegawaiId = $this->db->insertID();

		// move file to public
		if ($image->getError() != 4) {
			// fit image
			if (!file_exists('assets/img/users')) {
				mkdir('assets/img/users', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(600, 600, 'center')
				->save("assets/img/users/{$fileName}");

			// create thumbnail image
			if (!file_exists('assets/img/users/thumbnail')) {
				mkdir('assets/img/users/thumbnail', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(300, 300, 'center')
				->save("assets/img/users/thumbnail/{$fileName}");

			// $image->move("assets/img/users", $filenameGambar);
		}

		// insert users
		$input->fileName = $fileName;
		if (!$this->PegawaiModel->insertUser($input, $pegawaiId)) {
			return redirect()->to('/pegawai');
		}

		$this->session->setFlashdata('success', 'Data berhasil ditambahkan');
		return redirect()->to('/pegawai');
	}

	public function edit($id)
	{
		if (!is_allow('update', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$dataDb = $this->db->table('pegawai')->where('id', $id)->get()->getRowArray();
		$user = $this->db->table('users')->select('id, username, email, image')->where('id_pegawai', $id)->get()->getRowArray();
		$usersGroups  = $this->db->table('users_groups')->select('group_id')->where('user_id', $user['id'])->get()->getRowArray();

		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/pegawai');
		}

		$data = [
			'title' => 'Edit Pegawai',
			'content' => (object) array_merge($dataDb, $user, $usersGroups),
			'user' => (object) $user,
			'validation' => \Config\Services::validation(),
			'form_action' => base_url("pegawai/$id/update"),
		];

		if (!$_POST) {
			// repoulate form
			$data['input'] = $data['content'];
			$data['input']->id_pegawai = $id;
		}
		return $this->view('pages/admin/pegawai/edit', $data);
	}

	public function update($id)
	{
		if (!is_allow('update', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$dataDb = $this->db->table('pegawai')->where('id', $id)->get()->getRow();
		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/pegawai');
		}

		// set method to post, to make validation work correctly
		if ($this->request->getMethod() == 'put') {
			$this->request->setMethod('post');
		}

		// set input data
		$input = (object) $this->request->getPost();

		// validate and set error message
		if (!empty($input->password)) {
			if (!$this->validate($this->PegawaiModel->getValidationRules())) {
				return redirect()->to("/pegawai/$id/edit")->withInput();
			}
		} else {
			if (!$this->validate($this->PegawaiModel->getValidationWithoutPass())) {
				return redirect()->to("/pegawai/$id/edit")->withInput();
			}
		}

		// get uploaded file
		$image = $this->request->getFile('image');

		if ($image->getError() == 4) {
			$fileName = $input->fileLama;
		} else {
			$fileName = url_title("$input->nik-$input->nama", '-', true) . '.' . $image->getExtension();
		}

		$data = [
			'nik' => $input->nik,
			'nama' => $input->nama,
			'tempat_lahir' => $input->tempat_lahir,
			'tanggal_lahir' => isset($input->tanggal_lahir) && !empty($input->tanggal_lahir) ? $input->tanggal_lahir : null,
			'jenis_kelamin' => $input->jenis_kelamin ?? '',
			'alamat' => $input->alamat ?? '',
			'agama' => $input->agama ?? '',
			'pendidikan' => $input->pendidikan ?? '',
		];
		$this->db->table('pegawai')->where('id', $id)->update($data);

		// move file to public
		if ($image->getError() != 4) {
			// delete old file
			if (!empty($input->fileLama) && file_exists("assets/img/users/{$input->fileLama}")) {
				unlink("assets/img/users/{$input->fileLama}");
			}
			if (!empty($input->fileLama) && file_exists("assets/img/users/thumbnail/{$input->fileLama}")) {
				unlink("assets/img/users/thumbnail/{$input->fileLama}");
			}

			// fit image
			if (!file_exists('assets/img/users')) {
				mkdir('assets/img/users', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(600, 600, 'center')
				->save("assets/img/users/{$fileName}");

			// create thumbnail image
			if (!file_exists('assets/img/users/thumbnail')) {
				mkdir('assets/img/users/thumbnail', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(300, 300, 'center')
				->save("assets/img/users/thumbnail/{$fileName}");

			// $image->move("assets/img/users", $fileName);
		}

		// update users
		$user = $this->db->table('users')->select('id')->where('id_pegawai', $id)->get()->getRow();
		$input->fileName = $fileName;
		if (!$this->PegawaiModel->updateUser($input, $user->id)) {
			return redirect()->to('/pegawai');
		}

		$this->session->setFlashdata('success', 'Data berhasil diubah');
		return redirect()->to('/pegawai');
	}

	public function delete($id)
	{
		if (!is_allow('delete', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$dataDb = $this->db->table('pegawai')->where('id', $id)->get()->getRow();
		$user = $this->db->table('users')->select('image')->where('id_pegawai', $id)->get()->getRow();
		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/pegawai');
		}

		// delete file
		if (!empty($user->image) && file_exists("assets/img/users/{$user->image}")) {
			unlink("assets/img/users/$user->image");
		}
		if (!empty($user->image) && file_exists("assets/img/users/thumbnail/{$user->image}")) {
			unlink("assets/img/users/thumbnail/$user->image");
		}

		// delete data
		$this->db->table('pegawai')->where('id', $id)->delete();
		$this->db->table('users')->where('id_pegawai', $id)->delete();

		$this->session->setFlashdata('success', 'Data berhasil dihapus');
		return redirect()->to('/pegawai');
	}

	function ajax_select2()
	{
		if (!isset($_POST['searchTerm'])) {
			$builder = $this->db->table('pegawai')->orderBy('nama', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('pegawai')->like('nama', $search)->orderBy('nama', 'asc');
		}

		$query    = $builder->select('id, nama')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Pegawai -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->nama,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Pegawai -',
		];
		echo json_encode($options);
		return;
	}

	public function spreadsheet()
	{
		if (!is_allow('')) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$input = (object) $this->request->getGet();

		$db = $this->db->table('pegawai_list_view');
		// datatable search column
		$i = 0;
		foreach ($this->PegawaiModel->column_search as $item) {
			if ($input->searchValue) {
				if ($i === 0) {
					$db->groupStart();
					$db->like($item, $input->searchValue);
				} else {
					$db->orLike($item, $input->searchValue);
				}
				if (count($this->PegawaiModel->column_search) - 1 == $i)
					$db->groupEnd();
			}
			$i++;
		}

		$data = $db->get()->getResult();

		$html = "
	<table>
		<tr>
			<th>No</th>
			<th>NIK</th>
			<th>Nama</th>
			<th>Tempat Lahir</th>
			<th>Tanggal Lahir</th>
			<th>Jenis Kelamin</th>
			<th>Alamat</th>
			<th>Jabatan</th>
			<th>Unit</th>
		</tr>";
		$no = 0;
		foreach ($data as $row) :
			$no++;
			$tanggalLahir = strftime('%d/%m/%Y', strtotime($row->tanggal_lahir));
			$alamat = \htmlspecialchars($row->alamat);
			$pegawaiJabatanU = $this->db->table('pegawai_jabatan_u_view')->select('nama_jabatan, nama_unit')->where('id_Pegawai', $row->id)->get()->getRow();
			$namaJabatan = $pegawaiJabatanU->nama_jabatan ?? '';
			$namaUnit = $pegawaiJabatanU->nama_unit ?? '';
			$html .= "
		<tr>
			<td>$no</td>
			<td>$row->nik</td>
			<td>$row->nama</td>
			<td>$row->tempat_lahir</td>
			<td>$tanggalLahir</td>
			<td>$row->jenis_kelamin</td>
			<td>$alamat</td>
			<td>$namaJabatan</td>
			<td>$namaUnit</td>
		</tr>";
		endforeach;
		$html .=
			"</table>";

		// ini_set('xdebug.var_display_max_depth', -1);
		// ini_set('xdebug.var_display_max_children', -1);
		// ini_set('xdebug.var_display_max_data', -1);
		// \var_dump($html);
		// die;

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($html);

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

		$fileName = "Laporan";

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $fileName . '.xls');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');

		// to prevent corrupt xlsx file due to phpspreadsheet 
		// https://stackoverflow.com/questions/46092318/phpspreadsheet-is-corrupting-files
		die;
	}

	// search aset detail
	public function searchNik()
	{
		$term = $this->request->getGet('term');
		$data = null;
		if (\strlen($term) >= 3) {
			$data  = $this->db->table('pegawai')->like('nik', $term, 'after')->get()->getResult();
		}

		echo json_encode($data);
	}
	//--------------------------------------------------------------------

}
