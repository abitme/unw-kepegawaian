<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Content -->
<div class="row">
  <div class="col-md-12">
    <p><?php echo sprintf(lang('Auth.deactivate_subheading'), $user->email); ?></p>

    <?php $uri = service('uri');
    if ($uri->getSegment(1) == 'url') {
      echo form_open('url/deactivate/' . $user->id);
    } else {
      echo form_open('auth/deactivate/' . $user->id);
    }
    ?>

    <p>
      <?php echo form_label(lang('Auth.deactivate_confirm_y_label'), 'confirm'); ?>
      <input type="radio" name="confirm" value="yes" checked="checked" />
      <?php echo form_label(lang('Auth.deactivate_confirm_n_label'), 'confirm'); ?>
      <input type="radio" name="confirm" value="no" />
    </p>

    <?php echo form_hidden('id', $user->id); ?>

    <p><?php echo form_submit('submit', lang('Auth.deactivate_submit_btn'), ['class' => 'btn btn-primary']); ?></p>

    <?php echo form_close(); ?>
    <?= $this->extend('layouts/admin'); ?>
  </div>
</div>

<?= $this->endSection(); ?>