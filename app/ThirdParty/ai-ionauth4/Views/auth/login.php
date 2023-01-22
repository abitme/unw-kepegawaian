<?= $this->extend('layouts/auth'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Outer Row -->
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-6">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Kepegawaian</h1>
              </div>

              <!-- load alert -->
              <?= $this->include('includes/_alert') ?>

              <?php echo form_open('/login', ['class' => 'user']); ?>

              <!-- email -->
              <div class="form-group">
                <?php echo form_input($identity, set_value('identity'), ['class' => 'form-control form-control-user', 'placeholder' => 'Enter Username or Email Address...']); ?>
                <?php if (!empty($validation->hasError('identity'))) : ?>
                  <small class="text-danger ml-3"><?= $validation->getError('identity') ?></small>
                <?php endif ?>
              </div>

              <!-- password -->
              <div class="form-group">
                <?php echo form_input($password, '', ['class' => 'form-control form-control-user', 'placeholder' => 'Password...']); ?>
                <?php if (!empty($validation->hasError('password'))) : ?>
                  <small class="text-danger ml-3"><?= $validation->getError('password') ?></small>
                <?php endif ?>
              </div>

              <!-- remember me -->
              <div class="form-group">
                <div class="custom-control custom-checkbox small">
                  <?php echo form_checkbox('remember', '1', TRUE, 'id="remember" class="custom-control-input"'); ?>
                  <?php echo form_label(lang('Auth.login_remember_label'), 'remember', ['for' => 'customCheck', 'class' => 'custom-control-label',]); ?>
                </div>
              </div>

              <!-- submit -->
              <button type="submit" class="btn btn-primary btn-user btn-block"><?= lang('Auth.login_submit_btn') ?></button>

              <?php echo form_close(); ?>

              <!-- <hr> -->
              <!-- <div class="text-center">
                <a class="small" href="<?= base_url('auth/forgot_password') ?>">Forgot Password?</a>
              </div> -->
              <!-- <div class="text-center">
                <a class="small" href="<?= base_url('register') ?>">Create an Account!</a>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>
