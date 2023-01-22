<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 text-gray-800 mb-1"><?= $title ?></h1>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
</div>

<!-- row card -->
<div class="row">
  <!-- Card -->
  <div class="col-md-4 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pegawai</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($pegawai, 0, ',', '.') ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-4 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Unit Kerja</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($unit, 0, ',', '.') ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-building fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-4 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jabatan Struktural</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($jabatanStrukturalUnit, 0, ',', '.') ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-table fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Row chart -->
<div class="row">
  <!-- Area Chart -->
  <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"></h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="chart-area">
          <canvas id="myAreaChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pendidikan Pegawai</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="chart-pie">
          <canvas id="myPieChart"></canvas>
        </div>
        <div class="mt-4 text-center small">
        </div>
        <!-- <div class="d-flex justify-content-between">
          <ul>
            <li>
              <span class="mr-2">
                <i class="fas fa-circle text-success"></i> SD
              </span>
            </li>
            <li>
              <span class="mr-2">
                <i class="fas fa-circle text-success"></i> SLTP
              </span>
            </li>
          </ul>
          <ul>
            <li>
              <span class="mr-2">
                <i class="fas fa-circle text-success"></i> SD
              </span>
            </li>
          </ul>
        </div> -->
      </div>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- chart js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/chart.js/Chart.min.js"></script>

<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function() {
    $('#form').on('change', function() {
      $('#form').submit();
    });
    // select2
    $('.access').select2();

    // chart js
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Pie Chart 
    const SD = <?= json_encode($pegawaiSD) ?>;
    const SLTP = <?= json_encode($pegawaiSLTP) ?>;
    const SLTA = <?= json_encode($pegawaiSLTA) ?>;
    const DI = <?= json_encode($pegawaiDI) ?>;
    const DII = <?= json_encode($pegawaiDII) ?>;
    const DIII = <?= json_encode($pegawaiDIII) ?>;
    const DIV = <?= json_encode($pegawaiDIV) ?>;
    const S1 = <?= json_encode($pegawaiS1) ?>;
    const S2 = <?= json_encode($pegawaiS2) ?>;
    const S3 = <?= json_encode($pegawaiS3) ?>;

    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
      // plugins: [{
      //   beforeInit: function(chart, options) {
      //     chart.legend.afterFit = function() {
      //       this.height = this.height + 26;
      //     };
      //   }
      // }],
      type: 'doughnut',
      data: {
        labels: ["SD", "SLTP", 'SLTA', 'DI', 'DII', 'DIII', 'DIV', 'S1', 'S2', 'S3'],
        datasets: [{
          data: [SD, SLTP, SLTA, DI, DII, DIII, DIV, S1, S2, S3],
          backgroundColor: ['#012a4a', '#013a63', '#01497c', '#014f86', '#2a6f97', '#2c7da0', '#468faf', '#61a5c2', '#89c2d9', '#a9d6e5'],
          hoverBackgroundColor: ['#012a4a', '#013a63', '#01497c', '#014f86', '#2a6f97', '#2c7da0', '#468faf', '#61a5c2', '#89c2d9', '#a9d6e5'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: true
          // position: 'top',
        },
        cutoutPercentage: 40,
        tooltips: {
          enabled: true,
          mode: 'single',
        }
      },

    });
  });
</script>
<?= $this->endSection(); ?>