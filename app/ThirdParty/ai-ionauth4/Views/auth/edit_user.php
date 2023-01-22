<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-md-7 mx-auto">

    <!-- Page Heading -->
    <!-- <h1 class="h3 text-gray-800"><?= $title ?></h1> -->
    <!-- <p><?php echo lang('Auth.edit_user_subheading'); ?></p> -->

    <!-- alert -->
    <?= $this->include('includes/_alert') ?>

    <!-- Page Content -->
    <div class="card shadow mb-4">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Form <?= lang('Auth.edit_user_heading') ?></h6>
      </div>
      <div class="card-body">
        <?php echo form_open(uri_string()); ?>

        <div class="form-group">
          <?php echo form_label(lang('Auth.edit_user_name_label'), 'name'); ?> <br />
          <?php echo form_input($name); ?>
          <small class="text-danger"><?= $validation->getError('name') ?></small>
        </div>

        <?php if ($identity_column !== 'email') : ?>
          <div class="form-group">
            <?php echo form_label(lang('Auth.create_user_identity_label'), 'identity'); ?>
            <?php echo $validation->getError('identity'); ?>
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
          <?php echo form_label(lang('Auth.edit_user_password_label'), 'password'); ?>
          <?php echo form_input($password); ?>
          <small class="text-danger"><?= $validation->getError('password') ?></small>
        </div>

        <div class="form-group">
          <?php echo form_label(lang('Auth.edit_user_password_confirm_label'), 'password_confirm'); ?>
          <?php echo form_input($password_confirm); ?>
          <small class="text-danger"><?= $validation->getError('password_confirm') ?></small>
        </div>

        <?php if ($ionAuth->isAdmin()) : ?>

          <div class="form-group">
            <label for=""><?= lang('Auth.edit_user_groups_heading') ?></label><br />
            <?php foreach ($groups as $group) : ?>
              <label class="checkbox">
                <?php
                $gID = $group['id'];
                $checked = null;
                $item = null;
                foreach ($currentGroups as $grp) {
                  if ($gID == $grp->id) {
                    $checked = ' checked="checked"';
                    break;
                  }
                }
                ?>
                <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
              </label>
            <?php endforeach ?>
          </div>

        <?php endif ?>

        <?php echo form_hidden('id', $user->id); ?>

        <div class="form-group id_pegawai">
          <label for="id_pegawai">Pegawai</label>
          <?= form_dropdown('id_pegawai', getDropdownList('pegawai', ['id', 'nama'], '', '- Pilih Pegawai -', 'id', 'asc', ''), set_value('id_pegawai', isset($id_pegawai) ? $id_pegawai : ''), ['class' => 'form-control access', 'id' => 'id_pegawai', 'style' => 'width:100%']) ?>
        </div>

        <div class="float-left">
          <a href="<?= base_url('users') ?>" class="btn btn-back">Cancel</a>
          <button type="submit" class="btn btn-primary btn-user"><?= lang('Auth.edit_user_submit_btn') ?></button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>