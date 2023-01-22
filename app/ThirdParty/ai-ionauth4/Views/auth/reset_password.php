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
                <h1 class="h4 text-gray-900 mb-4"><?php echo lang('Auth.reset_password_heading'); ?></h1>
              </div>

              <!-- load alert -->
              <?= $this->include('includes/_alert') ?>

              <?php echo form_open("auth/reset_password/$code", ['class' => 'user']); ?>

              <div class="form-group">
                <!-- <label for="new_password"><?php echo sprintf(lang('Auth.reset_password_new_password_label'), $minPasswordLength); ?></label> -->
                <?php echo form_input($new_password); ?>
                <?php if (!empty($validation->hasError('new'))) : ?>
                  <small class="text-danger ml-3"><?= $validation->getError('new') ?></small>
                <?php endif ?>
              </div>
              
              <div class="form-group">
                <!-- <?php echo form_label(lang('Auth.reset_password_new_password_confirm_label'), 'new_password_confirm'); ?> -->
                <?php echo form_input($new_password_confirm);?>
                <?php if (!empty($validation->hasError('new_confirm'))) : ?>
                  <small class="text-danger ml-3"><?= $validation->getError('new_confirm') ?></small>
                <?php endif ?>
              </div>

              <?php echo form_input($user_id); ?>

              <button type="submit" class="btn btn-primary btn-user btn-block">
                <?= lang('Auth.reset_password_submit_btn') ?>
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