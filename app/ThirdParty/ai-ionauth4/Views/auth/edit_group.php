<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-md-7 mx-auto">

    <!-- Page Heading -->
    <!-- <h1 class="h3 text-gray-800"><?= $title ?></h1> -->
    <!-- <p><?php echo lang('Auth.edit_group_subheading'); ?></p> -->

    <!-- alert -->
    <?= $this->include('includes/_alert') ?>

    <!-- Page Content -->
    <div class="card shadow mb-4">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Form <?= lang('Auth.edit_group_heading') ?></h6>
      </div>
      <div class="card-body">
        <?php echo form_open(uri_string()); ?>

        <div class="form-group">
          <?php echo form_label(lang('Auth.edit_group_name_label'), 'group_name'); ?>
          <?php echo form_input($group_name); ?>
          <small class="text-danger"><?= $validation->getError('group_name') ?></small>
        </div>

        <div class="form-group">
          <?php echo form_label(lang('Auth.edit_group_desc_label'), 'description'); ?>
          <?php echo form_input($group_description); ?>
          <small class="text-danger"><?= $validation->getError('description') ?></small>
        </div>

        <div class="float-left">
          <a href="<?= base_url('groups') ?>" class="btn btn-back">Cancel</a>
          <button type="submit" class="btn btn-primary btn-group"><?= lang('Auth.edit_group_submit_btn') ?></button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>