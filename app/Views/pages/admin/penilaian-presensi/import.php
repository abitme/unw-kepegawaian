<?= $this->extend('layouts/admin'); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 text-gray-800 mb-4"><?= $title ?></h1>

<!-- alert -->
<?= $this->include('includes/_alertTemp') ?>
<?= $this->include('includes/_alert') ?>

<!-- Page Content -->
<div class="row">
	<div class="col-md-12">
		<a href="<?= base_url("assets/files/penilaian-presensi/template-penilaian-presensi.xls"); ?>" class="btn btn-sm btn-success mb-3">
			<i class="fas fa-file-excel"></i> Download Format excel
		</a>

		<form method="post" action="<?= $form_action ?>" enctype="multipart/form-data" class="form-inline">
			<div class="form-group col-md-5 pl-0">
				<div class="custom-file ">
					<input type="file" class="custom-file-input <?= ($validation->hasError('excel')) ? 'is-invalid' : '' ?>" id="upload_file" name="excel" onchange="urlBrowse()">
					<label class="custom-file-label d-inline" style="min-width: 100px" for="file">Choose file</label>
					<div class="invalid-feedback"><?= $validation->getError('excel') ?></div>
				</div>
			</div>
			<button type="submit" name="preview" class="ml-2 btn btn-primary" style="z-index: 3;">Preview</button>
		</form>

		<?php if (isset($preview)) : ?>
			<div class="card shadow mb-3 mt-5">
				<div class="card-header text-center">
					<h6 class="m-0 font-weight-bold text-primary">Preview</h6>
				</div>
				<div class="card-body table-bordered table-responsive">
					<?php foreach ($error as $row) : ?>
						<small class="form-text text-danger"><?= $row ?></small>
					<?php endforeach ?>
					<table class="table table-hover" style="overflow-x:auto;">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">NIK</th>
								<th scope="col">Nama</th>
								<th scope="col">Cuti</th>
								<th scope="col">Alpha</th>
								<th scope="col">Total Cuti</th>
								<th scope="col">Terlambat</th>
							</tr>
						</thead>
						<tbody>
							<?= $preview ?>
						</tbody>
					</table>
					<?php if (empty($error)) : ?>
						<form method="post" action="<?= $form_action ?>">
							<a href="<?= base_url('penilaian-presensi') ?>" class="btn btn-light">Cancel</a>
							<button type="submit" name="import" value="import" class="btn btn-success">Import</button>
						</form>
					<?php endif ?>
				</div>
			</div>
		<?php endif ?>
	</div>
</div>

<?= $this->endSection(); ?>