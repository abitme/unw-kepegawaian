<?= $this->extend('layouts/auth'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Outer Row -->
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-7">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Create Account!</h1>
              </div>

              <!-- load alert -->
              <?= $this->include('includes/_alert') ?>

              <?php echo form_open('/register', ['class' => 'user']); ?>
              <p style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Data Akun :</p>

              <?php if ($identity_column !== 'email') : ?>
                <div class="form-group">
                  <?php echo form_label(lang('Auth.create_user_identity_label'), 'identity'); ?>
                  <?php echo form_input($identity, set_value('identity'), ['class' => 'form-control']); ?>
                  <small class="text-danger"><?= $validation->getError('identity') ?></small>
                </div>
              <?php endif ?>

              <div class="form-group">
                <?php echo form_label(lang('Auth.create_user_email_label'), 'email'); ?>
                <?php echo form_input($email, set_value('email'), ['class' => 'form-control']); ?>
                <small class="text-danger"><?= $validation->getError('email') ?></small>
              </div>

              <div class="form-group">
                <?php echo form_label(lang('Auth.create_user_password_label'), 'password'); ?>
                <?php echo form_input($password, '', ['class' => 'form-control']); ?>
                <small class="text-danger"><?= $validation->getError('password') ?></small>
              </div>

              <div class="form-group">
                <?php echo form_label(lang('Auth.create_user_password_confirm_label'), 'password_confirm'); ?>
                <?php echo form_input($password_confirm, '', ['class' => 'form-control']); ?>
                <small class="text-danger"><?= $validation->getError('password_confirm') ?></small>
              </div>

              <p class="mt-4" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">Data Pegawai :</p>

              <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : '' ?>" id="title" name="nik" value="<?= set_value('nik') ?>" placeholder="Nomer induk Kepegawaian">
                <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
              </div>

              <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" class="form-control <?= ($validation->hasError('name')) ? 'is-invalid' : '' ?>" id="title" name="name" value="<?= set_value('name') ?>" placeholder="Nama lengkap">
                <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
              </div>

              <div class="form-row">
                <div class="col-md-6 mb-3">
                  <label for="tempat_lahir">Tempat Tanggal Lahir</label>
                  <input type="text" class="form-control <?= ($validation->hasError('tempat_lahir')) ? 'is-invalid' : '' ?>" id="tempat_lahir" name="tempat_lahir" value="<?= set_value('tempat_lahir') ?>" placeholder="Tempat lahir">
                  <div class="invalid-feedback"><?= $validation->getError('tempat_lahir') ?></div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="tanggal_lahir" style="visibility: hidden;">Tanggal Lahir</label>
                  <input type="date" class="form-control <?= ($validation->hasError('tanggal_lahir')) ? 'is-invalid' : '' ?>" id="tanggal_lahir" name="tanggal_lahir" value="<?= set_value('tanggal_lahir') ?>">
                  <div class="invalid-feedback"><?= $validation->getError('tanggal_lahir') ?></div>
                </div>
              </div>

              <div class="form-group">
                <label for="">Jenis Kelamin</label>
                <br>
                <div class="custom-control custom-radio">
                  <input class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" type="radio" name="jenis_kelamin" id="Laki-Laki" value="Laki-Laki" <?= set_radio('jenis_kelamin', 'Laki-Laki') ?>>
                  <label class="custom-control-label" for="Laki-Laki">
                    Laki-Laki
                  </label>
                </div>
                <div class="custom-control custom-radio">
                  <input class="custom-control-input <?= ($validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" type="radio" name="jenis_kelamin" id="Perempuan" value="Perempuan" <?= set_radio('jenis_kelamin', 'Perempuan') ?>>
                  <label class="custom-control-label" for="Perempuan">
                    Perempuan
                  </label>
                  <div class="invalid-feedback d-block"><?= $validation->getError('jenis_kelamin') ?></div>
                </div>
              </div>

              <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : '' ?>" id="title" name="alamat" value="<?= set_value('alamat') ?>" placeholder="Alamat">
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

              <!-- submit -->
              <button type="submit" class="btn btn-primary btn-user btn-block">Register</button>

              <?php echo form_close(); ?>

              <hr>
              <div class="text-center small">
                Have an account?
                <a href="<?= base_url('login') ?>">Login Here</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>