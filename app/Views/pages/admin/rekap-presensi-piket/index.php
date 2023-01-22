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
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- filter button -->
<div class="row">
  <div class="col-md">
    <div class="card card-filter shadow mb-3">
      <div class="card-body">
        <form action="<?= base_url('rekap-presensi-piket') ?>" method="GET">
          <div class="form-row align-items-end">
            <div class="form-group mb-0 col-md-3">
              <label for="dates">Tanggal</label>
              <input type="text" class="form-control" id="dates" name="dates" value="<?= $input->dates ?? '' ?>">
            </div>
            <div class="form-group mb-0 col-md-5">
              <label for="unit">Unit</label>
              <?= form_dropdown('id_unit', getDropdownList('unit', ['id', 'nama_unit'], '', '- Semua Unit -', 'nama_unit', 'asc'), set_value('id_unit', $input->id_unit ?? ''), ['class' => 'form-control select2', 'id' => 'id_unit', 'style' => 'width:100%']) ?>
            </div>
            <div class="form-group mb-0 col-md-4">
              <?php if (isset($input->dates)) : ?>
                <a href="<?= base_url('rekap-presensi-piket') ?>" class="btn btn-secondary shadow-sm">Hapus Filter</a>
              <?php endif ?>
              <button type="submit" class="btn btn-primary shadow-sm "><i class="fas fa-filter"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-3">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar <?= $title ?></h6>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-hover" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th>#</th>
              <th>Pegawai</th>
              <th>Hadir</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $db = \Config\Database::connect();
            $no = 0;

            if (isset($input->dates)) {
              $date = explode(" - ", $input->dates);
              $date[0] = date('Y-m-d', strtotime($date[0]));
              $date[1] = date('Y-m-d', strtotime($date[1]));

              $tanggal_awal = $date[0];
              $tanggal_akhir = $date[1];
            } else {
              $tanggal_awal = date('Y-m-d');
              $tanggal_akhir = date('Y-m-d');
            }
            foreach ($pegawai as $p) : $no++ ?>
              <?php
              $hadir = $db->query("SELECT COUNT(*) as hadir FROM view_rekap_presensi_piket WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->hadir;
              ?>
              <tr>
                <td></td>
                <td><?= $p->nama ?></td>
                <td><?= $hadir ?></td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<!-- daterange -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- datatable css -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- daterange -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script>
  $(document).ready(function() {
    // https://stackoverflow.com/questions/35232127/how-to-sum-a-single-table-column-using-javascript
    // datatable
    table = $('#myTable').DataTable({
      paging: false,
      order: [1, 'asc'],
      columnDefs: [{
        targets: [0],
        orderable: false,
      }, ],
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2]
          }
        },
        {
          extend: 'csvHtml5',
          exportOptions: {
            columns: [0, 1, 2]
          }
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          exportOptions: {
            columns: [0, 1, 2]
          }
        },
      ],
    });
    table.on('order.dt search.dt', function() {
      table.column(0, {
        search: 'applied',
        order: 'applied'
      }).nodes().each(function(cell, i) {
        cell.innerHTML = i + 1;
        // https://datatables.net/forums/discussion/32978/how-to-export-index-column-using-datatable-tabletool-plugin
        table.cell(cell).invalidate('dom');
      });
    }).draw();

    // date
    $('#dates').daterangepicker({
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      locale: {
        "format": "DD-MM-YYYY",
      },
      "alwaysShowCalendars": true,
      // "maxDate": moment()
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    // select2
    $('.select2').select2();
  });
</script>
<?= $this->endSection(); ?>