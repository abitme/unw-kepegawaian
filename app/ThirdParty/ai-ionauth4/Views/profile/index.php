<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="row no-gutters">
	<div class="col-md-12 mx-auto">

		<!-- Page Heading -->
		<div class="d-sm-flex align-items-center justify-content-between mb-3">
			<h1 class="h3 text-gray-800"><?= $title ?></h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
				</ol>
			</nav>
		</div>

		<!-- alert -->
		<?= $this->include('includes/_alert') ?>

		<!-- Page Content -->
		<div class="card">
			<div class="row">

				<div class="col-md-2">
					<div class="card-body text-center">
						<img src="<?= $user->image ? base_url('assets/img/users') . '/' . $user->image : base_url('assets/img/users/default.jpg') ?>" class="card-img" alt="profile image">
					</div>
				</div>
				<div class="col-md-10">
					<div class="card-body">
						<table class="table table-borderless mb-0">
							<tr class="d-flex">
								<td class="col-3">Username</td>
								<td>: &nbsp; <?= $user->username ?></td>
							</tr>
							<tr class="d-flex">
								<td class="col-3">Email</td>
								<td>: &nbsp; <?= $user->email ?></td>
							</tr>
							<tr class="d-flex">
								<td class="col-3">Nama</td>
								<td>: &nbsp; <?= $pegawai->nama ?? $user->name ?></td>
							</tr>
							<?php if ($pegawai) : ?>
								<tr class="d-flex">
									<td class="col-3">NIK</td>
									<td>: &nbsp; <?= $pegawai->nik ?? '-' ?></td>
								</tr>
								<tr class="d-flex">
									<td class="col-3">Tempat Tanggal Lahir</td>
									<?php $tempatLahir = $tempatLahir = $pegawai->tempat_lahir ? "$pegawai->tempat_lahir," : '-'; ?>
									<?php $tanggalLahir = isset($pegawai->tanggal_lahir) ? date('d F Y', strtotime($pegawai->tanggal_lahir)) : '' ?>
									<?php $ttl = "$tempatLahir $tanggalLahir" ?>
									<td>: &nbsp; <?= $ttl ?></td>
								</tr>
								<tr class="d-flex">
									<td class="col-3">Jenis Kelamin</td>
									<td>: &nbsp; <?= $pegawai->jenis_kelamin ?? '-' ?></td>
								</tr>
								<tr class="d-flex">
									<td class="col-3">Alamat</td>
									<td>: &nbsp; <?= $pegawai->alamat ?? '-' ?></td>
								</tr>
								<tr class="d-flex">
									<td class="col-3">Agama</td>
									<td>: &nbsp; <?= $pegawai->agama ?? '-' ?></td>
								</tr>
								<tr class="d-flex">
									<td class="col-3">Pendidikan</td>
									<td>: &nbsp; <?= $pegawai->pendidikan ?? '-' ?></td>
								</tr>
							<?php endif ?>
						</table>
						<a href="<?= base_url("profile/edit") ?>" class="btn btn-primary">Edit Profile</a>
						<a href="<?= base_url("auth/change_password") ?>" class="btn btn-light">Change Password</a>
					</div>
				</div>

			</div>
		</div>
	</div>

</div>
<?= $this->endSection(); ?>