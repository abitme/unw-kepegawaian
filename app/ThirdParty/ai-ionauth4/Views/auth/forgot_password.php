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
                <h1 class="h4 text-gray-900 mb-4"><?php echo lang('Auth.forgot_password_heading'); ?></h1>
              </div>

              <!-- load alert -->
              <?= $this->include('includes/_alert') ?>

              <?php echo form_open("auth/forgot_password", ['class' => 'user']); ?>

              <div class="form-group">
                <!-- <label for="identity"><?php echo (($type === 'email') ? sprintf(lang('Auth.forgot_password_email_label'), $identity_label) : sprintf(lang('Auth.forgot_password_identity_label'), $identity_label)); ?></label> -->
                <?php echo form_input($identity, '', ['class' => 'form-control form-control-user', 'placeholder' => 'Enter Email Address...']); ?>
                <?php if (!empty($validation->hasError('identity'))) : ?>
                  <small class="text-danger ml-3"><?= $validation->getError('identity') ?></small>
                <?php endif ?>
              </div>

              <button type="submit" class="btn btn-primary btn-user btn-block">
                <?= lang('Auth.forgot_password_submit_btn') ?>
              </button>

              <?php echo form_close(); ?>

              <hr>
              <div class="text-center">
                <a class="small" href="<?= base_url('login') ?>">Back to login page!</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>