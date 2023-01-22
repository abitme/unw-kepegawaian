<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 text-gray-800"><?= $title ?></h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">SOP</li>
    </ol>
  </nav>
</div>

<!-- download Button -->
<div class="row">
  <div class="col-md">
    <a href="<?= base_url() ?>/assets/files/dokumen-sop/sop.pdf" class="btn btn-info shadow-sm mb-3" download>
      <i class=" fas fa-file-download"></i>
      <span>Download SOP</span>
    </a>
  </div>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12">
  <iframe src="<?= base_url("assets/files/dokumen-sop/sop.pdf") ?>" type="application/pdf" style="width: 100%;height:100vh;border: none;"></iframe>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
  $(document).ready(function() {

  });
</script>
<?= $this->endSection(); ?>