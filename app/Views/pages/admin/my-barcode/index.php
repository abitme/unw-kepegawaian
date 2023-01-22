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
      <li class="breadcrumb-item active" aria-current="page">My Barcode</li>
    </ol>
  </nav>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12 d-flex justify-content-center">
    <?php if ($barcode) : ?>
    <img width="300px" height="auto" src="https://image-charts.com/chart?chs=300x300&cht=qr&chl=<?= $barcode ?>&choe=UTF-8">
    <?php endif ?>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
  $(document).ready(function() {

  });
</script>
<?= $this->endSection(); ?>