<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-md-7 mx-auto">

    <!-- Page Heading -->
    <!-- <h1 class="h3 text-gray-800"><?= $title ?></h1> -->
    <!-- <p><?php echo lang('Auth.create_user_subheading'); ?></p> -->

    <!-- Page Content -->
    <div class="card shadow mb-4">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Form <?= lang('Auth.change_password_heading') ?></h6>
      </div>
      <div class="card-body">
        <?php echo form_open('auth/change_password'); ?>

        <div class="form-group">
          <?php echo form_label(lang('Auth.change_password_old_password_label'), 'old_password'); ?> <br />
          <?php echo form_input($old_password, '', ['class' => 'form-control']); ?>
          <small class="text-danger"><?= $validation->getError('old') ?></small>
        </div>
        <div class="form-group">
          <label for="new_password"><?php echo sprintf(lang('Auth.change_password_new_password_label'), $minPasswordLength); ?></label> <br />
          <?php echo form_input($new_password, '', ['class' => 'form-control']); ?>
          <small class="text-danger"><?= $validation->getError('new') ?></small>
        </div>
        <div class="form-group">
          <?php echo form_label(lang('Auth.change_password_new_password_confirm_label'), 'new_password_confirm'); ?> <br />
          <?php echo form_input($new_password_confirm, '', ['class' => 'form-control']); ?>
          <small class="text-danger"><?= $validation->getError('new_confirm') ?></small>
        </div>

        <?php echo form_input($user_id); ?>

        <div class="float-left">
          <a href="<?= base_url('profile') ?>" class="btn btn-back">Cancel</a>
          <button type="submit" class="btn btn-primary btn-user"><?= lang('Auth.change_password_submit_btn') ?></button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>