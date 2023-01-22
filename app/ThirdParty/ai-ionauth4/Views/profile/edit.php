<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="row">
	<div class="col-lg-8 mx-auto">

		<!-- Page Heading -->
		<!-- <h1 class="h3 text-gray-800 text-center"><?= $title ?></h1> -->

		<!-- Page Content -->
		<div class="card shadow mb-4">
			<div class="card-header text-center">
				<h6 class="m-0 font-weight-bold text-primary">Form Edit Profile</h6>
			</div>
			<div class="card-body">
				<form action="<?= base_url() ?>/profile/update" method="POST" enctype="multipart/form-data">
					<?= csrf_field() ?>
					<input type="hidden" name="_method" value="PUT" />
					<input type="hidden" name="id" value="<?= $user->id ?>" />
					<input type="hidden" name="id_pegawai" value="<?= $pegawai->id ?? '' ?>" />
					<input type="hidden" name="fileLama" value="<?= $user->image ?>" />

					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username') ? old('username') : $user->username ?>">
						<div class="invalid-feedback"><?= $validation->getError('username') ?></div>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="text" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ? old('email') : $user->email ?>">
						<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
					</div>
					<div class="form-group">
						<label for="name">Nama</label>
						<input type="text" class="form-control <?= ($validation->hasError('name')) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= set_value('name', $user->name) ?>" placeholder="Nama lengkap">
						<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
					</div>
					<div class="form-group">
						<label for="image">Image</label>
						<div class="row">
							<div class="col">
								<img src="<?= $user->image ? base_url('assets/img/users') . '/' . $user->image : base_url('assets/img/users/default.jpg') ?>" alt="" class="img-preview img-preview-profile mb-2" id="img-preview">
							</div>
						</div>
						<div class="custom-file">
							<input type="file" class="custom-file-input <?= ($validation->hasError('image')) ? 'is-invalid' : '' ?>" id="image" name="image" onchange="previewImage()">
							<label class="custom-file-label" for="image"><?= $user->image ?></label>
							<div class="invalid-feedback"><?= $validation->getError('image') ?></div>
						</div>
					</div>
					<?php if ($pegawai) : ?>
						<div class="form-group">
							<label for="nik">NIK</label>
							<input type="text" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : '' ?>" id="nik" name="nik" value="<?= set_value('nik', $pegawai->nik) ?>" placeholder="Nomer induk kepegawaian">
							<div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
						</div>
						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="tempat_lahir">Tempat Tanggal Lahir</label>
								<input type="text" class="form-control <?= ($validation->hasError('tempat_lahir')) ? 'is-invalid' : '' ?>" id="tempat_lahir" name="tempat_lahir" value="<?= set_value('tempat_lahir', $pegawai->tempat_lahir) ?>" placeholder="Tempat lahir">
								<div class="invalid-feedback"><?= $validation->getError('tempat_lahir') ?></div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="tanggal_lahir" style="visibility: hidden;">Tanggal Lahir</label>
								<input type="date" class="form-control <?= ($validation->hasError('tanggal_lahir')) ? 'is-invalid' : '' ?>" id="tanggal_lahir" name="tanggal_lahir" value="<?= set_value('tanggal_lahir', $pegawai->tanggal_lahir) ?>">
								<div class="invalid-feedback"><?= $validation->getError('tanggal_lahir') ?></div>
							</div>
						</div>
						<div class="form-group">
							<label for="">Jenis Kelamin</label>
							<br>
							<div class="custom-control custom-radio">
								<input class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" type="radio" name="jenis_kelamin" id="Laki-Laki" value="Laki-Laki" <?= set_radio('jenis_kelamin', 'Laki-Laki', $pegawai->jenis_kelamin == 'Laki-Laki' && empty(old('jenis_kelamin'))) ?>>
								<label class="custom-control-label" for="Laki-Laki">
									Laki-Laki
								</label>
							</div>
							<div class="custom-control custom-radio">
								<input class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" type="radio" name="jenis_kelamin" id="Perempuan" value="Perempuan" <?= set_radio('jenis_kelamin', 'Perempuan', $pegawai->jenis_kelamin == 'Perempuan' && empty(old('jenis_kelamin'))) ?>>
								<label class="custom-control-label" for="Perempuan">
									Perempuan
								</label>
								<div class="invalid-feedback d-block"><?= $validation->getError('jenis_kelamin') ?></div>
							</div>
						</div>
						<div class="form-group">
							<label for="alamat">Alamat</label>
							<input type="text" class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : '' ?>" id="alamat" name="alamat" value="<?= set_value('alamat', $pegawai->alamat) ?>" placeholder="Alamat">
							<div class="invalid-feedback"><?= $validation->getError('alamat') ?></div>
						</div>
						<div class="form-group">
							<label for="agama">Agama</label>
							<select class="form-control  <?= ($validation->hasError('agama')) ? 'is-invalid' : '' ?>" name="agama" id="agama">
								<option value="" <?= set_select('agama', '', $pegawai->agama == '' && empty(old('agama'))) ?>>- Pilih Agama -</option>
								<option value="Islam" <?= set_select('agama', 'Islam', $pegawai->agama == 'Islam' && empty(old('agama'))) ?>>Islam</option>
								<option value="Hindu" <?= set_select('agama', 'Hindu', $pegawai->agama == 'Hindu' && empty(old('agama'))) ?>>Hindu</option>
								<option value="Budha" <?= set_select('agama', 'Budha', $pegawai->agama == 'Budha' && empty(old('agama'))) ?>>Budha</option>
								<option value="Protestan" <?= set_select('agama', 'Protestan', $pegawai->agama == 'Protestan' && empty(old('agama'))) ?>>Protestan</option>
								<option value="Katolik" <?= set_select('agama', 'Katolik', $pegawai->agama == 'Katolik' && empty(old('agama'))) ?>>Katolik</option>
								<option value="Konghucu" <?= set_select('agama', 'Konghucu', $pegawai->agama == 'Konghucu' && empty(old('agama'))) ?>>Konghucu</option>
							</select>
							<div class="invalid-feedback"><?= $validation->getError('agama') ?></div>
						</div>
						<div class="form-group">
							<label for="pendidikan">Pendidikan</label>
							<select class="form-control  <?= ($validation->hasError('pendidikan')) ? 'is-invalid' : '' ?>" name="pendidikan" id="pendidikan">
								<option value="" <?= set_select('pendidikan', '', $pegawai->pendidikan == '' && empty(old('pendidikan'))) ?>>- Pilih pendidikan -</option>
								<option value="Sekolah Dasar / Setara" <?= set_select('pendidikan', 'Sekolah Dasar / Setara', $pegawai->pendidikan == 'Sekolah Dasar / Setara' && empty(old('pendidikan'))) ?>>Sekolah Dasar / Setara</option>
								<option value="Sekolah Lanjutan Tingkat Pertama / Setara" <?= set_select('pendidikan', 'Sekolah Lanjutan Tingkat Pertama / Setara', $pegawai->pendidikan == 'Sekolah Lanjutan Tingkat Pertama / Setara' && empty(old('pendidikan'))) ?>>Sekolah Lanjutan Tingkat Pertama / Setara</option>
								<option value="Sekolah Lanjutan Tingkat Atas / Setara" <?= set_select('pendidikan', 'Sekolah Lanjutan Tingkat Atas / Setara', $pegawai->pendidikan == 'Sekolah Lanjutan Tingkat Atas / Setara' && empty(old('pendidikan'))) ?>>Sekolah Lanjutan Tingkat Atas / Setara</option>
								<option value="Diploma I" <?= set_select('pendidikan', 'Diploma I', $pegawai->pendidikan == 'Diploma I' && empty(old('pendidikan'))) ?>>Diploma I</option>
								<option value="Diploma II" <?= set_select('pendidikan', 'Diploma II', $pegawai->pendidikan == 'Diploma II' && empty(old('pendidikan'))) ?>>Diploma II</option>
								<option value="Diploma III" <?= set_select('pendidikan', 'Diploma III', $pegawai->pendidikan == 'Diploma III' && empty(old('pendidikan'))) ?>>Diploma III</option>
								<option value="Diploma IV" <?= set_select('pendidikan', 'Diploma IV', $pegawai->pendidikan == 'Diploma IV' && empty(old('pendidikan'))) ?>>Diploma IV</option>
								<option value="Sarjana (S1)" <?= set_select('pendidikan', 'Sarjana (S1)', $pegawai->pendidikan == 'Sarjana (S1)' && empty(old('pendidikan'))) ?>>Sarjana (S1)</option>
								<option value="Magister (S2)" <?= set_select('pendidikan', 'Magister (S2)', $pegawai->pendidikan == 'Magister (S2)' && empty(old('pendidikan'))) ?>>Magister (S2)</option>
								<option value="Doktoral (S3)" <?= set_select('pendidikan', 'Doktoral (S3)', $pegawai->pendidikan == 'Doktoral (S3)' && empty(old('pendidikan'))) ?>>Doktoral (S3)</option>
							</select>
							<div class="invalid-feedback"><?= $validation->getError('pendidikan') ?></div>
						</div>
					<?php endif ?>

					<a href="<?= base_url('profile') ?>" class="btn btn-light">Cancel</a>
					<button type="submit" class="btn btn-primary">Update</button>
				</form>
			</div>
		</div>
	</div>

</div>
<?= $this->endSection(); ?>