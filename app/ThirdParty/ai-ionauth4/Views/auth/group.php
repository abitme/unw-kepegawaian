<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

<div class="row">
  <div class="col-md">
    <a href="<?= base_url('groups/new') ?>" class="btn btn-primary shadow-sm mb-3">
      <i class="fas fa-plus-circle"></i>
      <span><?= lang('Auth.index_create_group_link') ?></span>
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
        <h6 class="m-0 font-weight-bold text-primary">Group List</h6>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-hover" id="myTable">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($groups as $group) : ?>
              <tr>
                <td><?php echo htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($group->description, ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <?php if ($group->id != 1) : ?>
                    <?= form_open(base_url("groups/$group->id"), ['method' => 'POST']) ?>
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE" />
                    <?= form_hidden('id', $group->id) ?>

                    <?php echo anchor("groups/access/$group->id", 'Access', ['class' => 'btn btn-sm btn-primary',]); ?>
                    <?php echo anchor("groups/edit/$group->id", 'Edit', ['class' => 'btn btn-sm btn-success mx-1',]); ?>
                    <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Apakah anda yakin menghapus data?')">
                      Delete
                    </button>
                    <?= form_close() ?>
                  <?php endif ?>
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