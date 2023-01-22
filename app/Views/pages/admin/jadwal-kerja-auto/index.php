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
      <li class="breadcrumb-item"><a href="#">Master Data</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- Create Button -->
<div class="row">
  <div class="col-md">
    <a href="javascript:void(0)" class="btn btn-primary shadow-sm mb-3" onclick="createData()">
      <i class=" fas fa-plus-circle"></i>
      <span>Tambah Jadwal Kerja Auto</span>
    </a>
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
              <th scope="col">#</th>
              <th scope="col">Jadwal Kerja Auto</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal View -->
<div class="modal fade" id="aiModalView" tabindex="-1" role="dialog" aria-labelledby="aiModalViewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalViewLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tr class="d-flex">
            <td class="col-3">Nama Jadwal Kerja Auto</td>
            <td id="nama-jadwal-kerja-auto">: &nbsp; </td>
          </tr>
        </table>
        <table id="tableJamKerja" class="table">
          <thead>
            <tr>
              <th>Jam Kerja</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="aiModalForm" tabindex="-1" role="dialog" aria-labelledby="aiModalFormLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalFormLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form" action="" method="post" style="overflow-y: auto;">
        <div class="modal-body form-row m-0">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="nama_jadwal_kerja">Nama Jadwal Kerja</label>
            <input type="text" class="form-control" id="nama_jadwal_kerja" name="nama_jadwal_kerja" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="jam_kerja">Jam Kerja</label>
            <select name="id_jam_kerja[0]" id="id_jam_kerja[0]" class="form-control">
              <?php
              foreach ($optionsJamKerja as $k => $v) {
                echo '<option value="' . $k . '">' . $v . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="container-box col-12">
          </div>
          <button class="btn btn-sm btn-info add-button">Tambah Jam Kerja</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function() {
    // datatable
    table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url('jadwal-kerja-auto/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [100, 150, 200],
        [100, 150, 200]
      ],
      columnDefs: [{
        targets: [0, 1, 2],
        orderable: false,
      }, ],
      // fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      //   if (aData[2].search("updateDataRelations") != -1) {
      //     $(nRow).find('td').eq(1).css('padding-left', '3rem');
      //   }
      // },
    });
    // // select2
    // https://stackoverflow.com/questions/14853779/adding-input-elements-dynamically-to-form
  });

  let max_fields = 100;
  let wrapper = $(".container-box");
  let x = 1;
  let y = 0;
  const optionsJamKerja = `
      <?php
      foreach ($optionsJamKerja as $k => $v) {
        echo '<option value="' . $k . '">' . $v . '</option>';
      }
      ?>
    `;
  $('.add-button').on('click', function(e) {
    e.preventDefault();
    if (x < max_fields) {
      x++;
      y++;
      $(wrapper).append(
        `<div class="container-input form-row">
            <div class="form-group">
              <label for="id_jam_kerja">Jam Kerja</label>
              <select name="id_jam_kerja[${y}]" id="id_jam_kerja[${y}]" class="form-control">
              ${optionsJamKerja}
              </select>
            </div>
            <a href="#" class="delete-button mb-3 ml-2">Hapus</a>
          </div>`
      );
    } else {
      alert('You Reached the limits')
    }
  });

  $(wrapper).on("click", ".delete-button", function(e) {
    e.preventDefault();
    $(this).parent('div').remove();
    x--;
  })

  // ajax view
  function viewData(id) {
    removeValidation()

    // change text
    $('#aiModalViewLabel').html('Detail Jadwal Kerja Auto');
    $('#aiModalView').modal('show');
    $.ajax({
      method: 'GET',
      url: <?= json_encode(base_url()) ?> + `/jadwal-kerja-auto/${id}`,
      cache: false,
      success: function(data) {
        obj = JSON.parse(data);
        data = obj.data;
        item = data['item'];
        itemDetail = data['itemDetail'];
        $('#aiModalView #nama-jadwal-kerja-auto').html(`: &nbsp; ${item.nama_jadwal_kerja}`);
        let wrapper = $("#tableJamKerja tbody");
        $.each(itemDetail, function(index, value) {
          $(wrapper).append(
            `<tr>
              <td>${value.nama_jam_kerja}</td>
            </tr>`
          );
        });
        $('#aiModalView').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });
  }

  var url;

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('jadwal-kerja-auto/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/jadwal-kerja-auto/update/${id}`;
    }

  }

  $('#form').submit(function(e) {

    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      method: "POST",
      url: url,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(data) {
        let obj = $.parseJSON(data);

        if (obj.status == 200) {
          $('#aiModalForm').modal('hide')
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['nama_jadwal_kerja']) {
            $('input[name=nama_jadwal_kerja]').addClass('is-invalid');
            $('input[name=nama_jadwal_kerja]').after(`<div class="invalid-feedback">${error['nama_jadwal_kerja']}</div>`);
          }
          $.each(obj.data.input.id_jam_kerja, function(index, value) {
            if (error[`id_jam_kerja.${index}`]) {
              $(`select[name="id_jam_kerja[${index}]"`).addClass(`is-invalid`);
              $(`select[name="id_jam_kerja[${index}]"`).after(`<div class="invalid-feedback">${error[`id_jam_kerja.${index}`]}</div>`);
            }
          });
        }

      },
    });

  });

  // ajax Create
  function createData() {
    removeValidation()
    $('#form')[0].reset();
    $('#id').val('');
    for (let i = 0; i <= 6; i++) {
      $(`#aiModalForm input[name='id_jadwal_kerja_detail[${i}]']`).val('')
    }

    $('.container-box').empty();
    y = 1;

    // change text
    $('#aiModalFormLabel').html('Tambah Jadwal Kerja Auto');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalForm').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalFormLabel').html('Edit Jadwal Kerja Auto');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/jadwal-kerja-auto/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        item = data['item'];
        itemDetail = data['itemDetail'];
        $('#aiModalForm input[name=id]').val(item.id);
        $('#aiModalForm input[name=nama_jadwal_kerja]').val(item.nama_jadwal_kerja);
        $('#aiModalForm select[name=nama_jadwal_kerja]').val(item.nama_jadwal_kerja);
        $('.container-box').empty();
        $.each(itemDetail, function(index, value) {
          $(`select[name='id_jam_kerja[${index}]']`).val(value.id_jam_kerja);

          const optionsJamKerjaJS = JSON.parse(`<?= json_encode($optionsJamKerja) ?>`);
          let optionsJamKerja = `<option value="">- Pilih Jam Kerja</option>`;
          for (let [id, val] of Object.entries(optionsJamKerjaJS)) {
            if (id != '') {
              selected = value.id_jam_kerja == id ? 'selected' : "";
              optionsJamKerja +=
                `<option value="${id}" ${selected}>${val}</option>`
            }
          }
          if (index > 0) {
            y = index;
            let wrapper = $(".container-box");
            $(wrapper).append(
              `<div class="container-input form-row">
                <div class="form-group">
                  <label for="id_jam_kerja">Jam Kerja</label>
                  <select name="id_jam_kerja[${index}]" id="id_jam_kerja[${index}]" class="form-control">
                  ${optionsJamKerja}
                  </select>
                </div>
                <a href="#" class="delete-button mb-3 ml-2">Hapus</a>
              </div>`
            );
          }
        });
        $('#aiModalForm').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('update', id);
  }

  // ajax Delete
  function destroyData(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/jadwal-kerja-auto/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  // set focus on shown modal 
  $('#aiModalForm').on('shown.bs.modal', function() {
    $('input[name=nama_jadwal_kerja]').trigger('focus')
  })

  function removeValidation() {
    $('.modal').find('.is-invalid').removeClass('is-invalid');
    $('.modal').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>