<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

<div class="row">
  <div class="col-md">
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary shadow-sm mb-3">
      <i class="fas fa-plus-circle"></i>
      <span><?= lang('Auth.index_create_user_link') ?></span>
    </a>
    <a href="<?= base_url('users/import') ?>" class="btn btn-success shadow-sm mb-3">
      <i class="fas fa-plus-circle"></i>
      <span>Import Excel</span>
    </a>
  </div>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Content -->
<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-3">

      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo lang('Auth.index_heading'); ?> List</h6>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-hover" id="myTable">
          <thead>
            <tr>
              <th><?php echo lang('Auth.index_name_th'); ?></th>
              <th>Username</th>
              <th><?php echo lang('Auth.index_email_th'); ?></th>
              <th><?php echo lang('Auth.index_groups_th'); ?></th>
              <th><?php echo lang('Auth.index_status_th'); ?></th>
              <th><?php echo lang('Auth.index_action_th'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user) : ?>
              <tr>
                <td><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <?php foreach ($user->groups as $group) : ?>
                    <?= $group->name ?>
                  <?php endforeach ?>
                </td>
                <td><?php echo ($user->active) ? anchor('auth/deactivate/' . $user->id, lang('Auth.index_active_link')) : anchor("auth/activate/" . $user->id, lang('Auth.index_inactive_link')); ?></td>
                <td>
                  <?php echo anchor("users/edit/" . $user->id, 'Edit', ['class' => 'btn btn-sm btn-success',]); ?>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection(); ?>