<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 text-gray-800"><?= $title ?></h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/artikel') ?>">Pegawai</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12 mb-card mx-auto">
    <div class="card shadow mb-3">

      <div class="card-body px-5 py-4">
        <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="image">Foto</label>
                <div class="row">
                  <div class="col">
                    <img src="<?= base_url() ?>/assets/img/users/default.jpg" alt="" class="img-preview mb-2" id="img-preview">
                  </div>
                </div>
                <div class="custom-file">
                  <input type="file" class="custom-file-input <?= ($validation->hasError('image')) ? 'is-invalid' : '' ?>" id="image" name="image" onchange="labelImageFileNoRemove()">
                  <label class="custom-file-label" for="image">Choose file</label>
                  <div class="invalid-feedback"><?= $validation->getError('image') ?></div>
                </div>
              </div>
            </div>
            <div class="col-md-9 pl-md-4">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= set_value('username') ?>">
                <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= set_value('email') ?>">
                <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : '' ?>" id="password" name="password">
                <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
              </div>
              <div class="form-group">
                <label for="password_confirm">Konfirmasi Password</label>
                <input type="password" class="form-control <?= ($validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" id="password_confirm" name="password_confirm">
                <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
              </div>
              <!-- <div class="form-group">
                <label for="group_id">Role</label>
                <?php $hasError = $validation->hasError('group_id') ? 'is-invalid' : '' ?>
                <?= form_dropdown('group_id', getDropdownList('groups', ['id', 'name'], '', '- Pilih Role -', 'name', 'asc'), set_value('group_id'), ['class' => "form-control $hasError", 'id' => 'group_id']) ?>
                <div class="invalid-feedback"><?= $validation->getError('group_id') ?></div>
              </div> -->
            </div>
          </div>

          <div class="form-group">
            <label for="nik">NIK</label>
            <input type="text" id="nik" name="nik" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : '' ?>" value="<?= set_value('nik') ?>" autocomplete="off" onkeyup=createSlug()>
            <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
          </div>
          <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control <?= ($validation->hasError('nama')) ? 'is-invalid' : '' ?>" value="<?= set_value('nama') ?>" autocomplete="off" onkeyup=createSlug()>
            <div class="invalid-feedback"><?= $validation->getError('nama') ?></div>
          </div>
          <div class="form-group">
            <label for="tempat_lahir">Tempat Lahir</label>
            <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control <?= ($validation->hasError('tempat_lahir')) ? 'is-invalid' : '' ?>" value="<?= set_value('tempat_lahir') ?>" autocomplete="off">
            <div class="invalid-feedback"><?= $validation->getError('tempat_lahir') ?></div>
          </div>
          <div class="form-group">
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control <?= ($validation->hasError('tanggal_lahir')) ? 'is-invalid' : '' ?>" value="<?= set_value('tanggal_lahir') ?>" autocomplete="off">
            <div class="invalid-feedback"><?= $validation->getError('tanggal_lahir') ?></div>
          </div>
          <div class="form-group">
            <label for="">Jenis Kelamin</label>
            <br>
            <div class="custom-control custom-radio">
              <input type="radio" id="Laki-Laki" name="jenis_kelamin" class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" value="Laki-Laki" <?= set_radio('jenis_kelamin', 'Laki-Laki') ?>>
              <label class="custom-control-label" for="Laki-Laki">
                Laki-Laki
              </label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="Perempuan" name="jenis_kelamin" class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" value="Perempuan" <?= set_radio('jenis_kelamin', 'Perempuan') ?>>
              <label class="custom-control-label" for="Perempuan">
                Perempuan
              </label>
              <div class="invalid-feedback"><?= $validation->getError('jenis_kelamin') ?></div>
            </div>
          </div>
          <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : '' ?>" id="alamat" name="alamat" cols="30" rows="2"><?= set_value('alamat') ?></textarea>
            <div class="invalid-feedback"><?= $validation->getError('alamat') ?></div>
          </div>
          <div class="form-group">
            <label for="agama">Agama</label>
            <select class="form-control  <?= ($validation->hasError('agama')) ? 'is-invalid' : '' ?>" name="agama" id="agama">
              <option value="" <?= set_select('agama', '') ?>>- Pilih Agama -</option>
              <option value="Islam" <?= set_select('agama', 'Islam') ?>>Islam</option>
              <option value="Hindu" <?= set_select('agama', 'Hindu') ?>>Hindu</option>
              <option value="Budha" <?= set_select('agama', 'Budha') ?>>Budha</option>
              <option value="Protestan" <?= set_select('agama', 'Protestan') ?>>Protestan</option>
              <option value="Katolik" <?= set_select('agama', 'Katolik') ?>>Katolik</option>
              <option value="Konghucu" <?= set_select('agama', 'Konghucu') ?>>Konghucu</option>
            </select>
            <div class="invalid-feedback"><?= $validation->getError('agama') ?></div>
          </div>
          <div class="form-group">
            <label for="pendidikan">Pendidikan</label>
            <select class="form-control  <?= ($validation->hasError('pendidikan')) ? 'is-invalid' : '' ?>" name="pendidikan" id="pendidikan">
              <option value="" <?= set_select('pendidikan', '') ?>>- Pilih pendidikan -</option>
              <option value="Sekolah Dasar / Setara" <?= set_select('pendidikan', 'Sekolah Dasar / Setara') ?>>Sekolah Dasar / Setara</option>
              <option value="Sekolah Lanjutan Tingkat Pertama / Setara" <?= set_select('pendidikan', 'Sekolah Lanjutan Tingkat Pertama / Setara') ?>>Sekolah Lanjutan Tingkat Pertama / Setara</option>
              <option value="Sekolah Lanjutan Tingkat Atas / Setara" <?= set_select('pendidikan', 'Sekolah Lanjutan Tingkat Atas / Setara') ?>>Sekolah Lanjutan Tingkat Atas / Setara</option>
              <option value="Diploma I" <?= set_select('pendidikan', 'Diploma I') ?>>Diploma I</option>
              <option value="Diploma II" <?= set_select('pendidikan', 'Diploma II') ?>>Diploma II</option>
              <option value="Diploma III" <?= set_select('pendidikan', 'Diploma III') ?>>Diploma III</option>
              <option value="Diploma IV" <?= set_select('pendidikan', 'Diploma IV') ?>>Diploma IV</option>
              <option value="Sarjana (S1)" <?= set_select('pendidikan', 'Sarjana (S1)') ?>>Sarjana (S1)</option>
              <option value="Magister (S2)" <?= set_select('pendidikan', 'Magister (S2)') ?>>Magister (S2)</option>
              <option value="Doktoral (S3)" <?= set_select('pendidikan', 'Doktoral (S3)') ?>>Doktoral (S3)</option>
            </select>
            <div class="invalid-feedback"><?= $validation->getError('pendidikan') ?></div>
          </div>
          <button type="submit" class="btn btn-primary float-right  ml-2" style="width: 100px;">Simpan</button>
          <a href="<?= base_url('pegawai') ?>" class="btn btn-back float-right ml-2" style="width: 100px;">Kembali</a>
        </form>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
  // select2
  $('.select2').select2();
</script>
<?= $this->endSection(); ?>