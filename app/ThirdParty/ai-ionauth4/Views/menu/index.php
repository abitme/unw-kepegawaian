<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 text-gray-800 mb-4"><?= $title ?></h1>

<!-- <div class="row">
  <div class="col-md">
    <a href="<?= base_url('users/new') ?>" class="btn btn-primary mb-3 mr-2">
      <i class="fas fa-plus-circle"></i>
      <span>Create a New Menu</span>
    </a>
  </div>
</div> -->

<!-- Content -->
<div class="row">
  <div class="col-lg">

    <div class="row">
      <div class="col-lg-6 message">
        <!-- alert -->
        <?= $this->include('includes/_alert') ?>
      </div>
    </div>

    <input type="hidden" class="form-control" id="idDelete" name="idDelete">

    <a href="" class="btn btn-primary mb-3 modalAdd" data-toggle="modal" data-target="#menuModal">Add Menu</a>
    <button class="btn mb-3 btn-success sort-menu">Sort Menu</button>

    <form action="<?= base_url('menus/sortMenu') ?>" method="post" id="form-nestable-output">
      <textarea name="nestable-output" id="nestable-output" cols="70" rows="3"></textarea>
      <input type="submit" style="display:none" />
    </form>

    <div class="row">
      <div class="col-lg">
        <div class="dd" id="nestable">

          <!-- looping menu -->
          <?php

          function get_menu($items, $class = 'dd-list')
          {

            $html = "<ol class=\"" . $class . "\" id=\"menu-id\">";

            foreach ($items as $key => $value) {

              $html .= '<li class="dd-item dd3-item" data-id="' . $value['id'] . '" >
                  <div class="dd-handle dd3-handle"></div>
                  <div class="dd3-content"><span id="label_show' . $value['id'] . '">' . $value['label'] . '</span> 
                  <span class="span-right">/<span id="link_show' . $value['id'] . '">' . $value['link'] . '</span> &nbsp;&nbsp;';

              // if menu super admin dont show edit & delete button
              if ($value['id'] != 1 && $value['parent'] != 1) {
                $html .=   '<a class="edit-button modalEdit" id="' . $value['id'] . '" label="' . $value['label'] . '" link="' . $value['link'] . '" icon="' . $value['icon'] . '" data-toggle="modal" data-target="#menuModal"><i class="fas fa-edit text-success"></i></a>
                              <a class="pl-2 del-button" id="' . $value['id'] . '" ><i class="fas fa-trash text-secondary"></i></a>';
              }

              $html .= '</span>
                  </div>';
              if (array_key_exists('child', $value)) {
                $html .= get_menu($value['child'], 'child');
              }
              $html .= "</li>";
            }


            $html .= "</ol>";
            return $html;
          }

          $items = dataMenu();
          print get_menu($items);

          ?>

        </div> <!-- .dd -->
      </div> <!-- div col lg -->
    </div> <!-- div row  -->

  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="menuModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" id="form-menus">
        <div class="modal-body">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="label">Label</label>
            <input type="text" class="form-control" id="label" name="label" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="link">Slug</label>
            <input type="text" class="form-control" id="link" name="link" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="icon">Icon</label>
            <div class="input-group">
              <input data-placement="bottomLeft" class="form-control icp icp-auto" value="" type="text" id="icon" name="icon">
              <div class="input-group-append" id>
                <span class="input-group-text"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="access">Menus Access</label>
            <div class="form-check">
              <label class="form-check-label" for="insert">
                <input type="checkbox" class="form-check-input" id="insert" value="1" name="insert">
                Insert
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label" for="update">
                <input type="checkbox" class="form-check-input" id="update" value="1" name="update">
                Update
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label" for="delete">
                <input type="checkbox" class="form-check-input" id="delete" value="1" name="delete">
                Delete
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label" for="validate">
                <input type="checkbox" class="form-check-input" id="validate" value="1" name="validate">
                Validate
              </label>
            </div>
          </div>
          <div class="form-group">
            <label for="access">User Access</label>            
            <?= form_dropdown('role[]', getDropdownGroups('groups', ['id', 'name'], '', '- No Option -', 'id'), '', ['class' => 'access form-control', 'id' => 'id_groups', 'multiple' => 'multiple', 'style' => 'width:100%',]) ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
  $(document).ready(function() {

    $('.access').select2({
      // placeholder: "User access",
      // closeOnSelect: false,
      // allowClear: true,
    });

    // Nestable
    var updateOutput = function(e) {
      var list = e.length ? e : $(e.target),
        output = list.data('output');
      if (window.JSON) {
        output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
      } else {
        output.val('JSON browser support required for this demo.');
      }
    };

    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
      })
      .on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));

    // expand and collapse all (not used)
    $('#nestable-menu').on('click', function(e) {
      let target = $(e.target),
        action = target.data('action');
      if (action === 'expand-all') {
        $('.dd').nestable('expandAll');
      }
      if (action === 'collapse-all') {
        $('.dd').nestable('collapseAll');
      }
    });

    // submit form if there's changed data 
    let nest = $('#nestable-output').val();
    $('.sort-menu').on('click', function() {
      if (nest == $('#nestable-output').val()) {
        alert('Nothing changed');
      } else {
        $('#form-nestable-output').submit();
      }
    });

    // set url form
    let url;

    // function submit form
    function submit_form(method, id = null) {

      // remove form error message
      $('.form-text').remove();

      // set url
      if (method == 'insert') {
        url = <?= json_encode(base_url('menus/create')) ?>;
      }
      if (method == 'update') {
        url = <?= json_encode(base_url()) ?> + `/menus/edit/${id}`;
      }

    }

    // handle submit form
    $('#form-menus').submit(function(e) {

      e.preventDefault();

      $.ajax({
        method: "POST",
        url: url,
        data: $(this).serialize(),
        cache: false,
        success: function(data) {
          // show form error message if exist
          if (data != '') {
            console.log(data);
            let obj = $.parseJSON(data);

            // remove error if exist
            $('input[name=label] + small').remove();
            $('input[name=link] + small').remove();

            // show error
            let labelError = `<small class="form-text text-danger">${obj['label']}</small>`
            let linkError = `<small class="form-text text-danger">${obj['link']}</small>`
            $('input[name=label]').after(labelError);
            $('input[name=link]').after(linkError);
          } else {
            window.location.href = <?= json_encode(base_url('menus')) ?>;
          }

        },
      });

    });

    // Modal Add
    $('.modalAdd').on('click', function() {

      // change text
      $('#menuModalLabel').html('Add Menu');
      $('.modal-footer button[type=submit]').html('Add');

      // change value to empty
      $('#id').val('');
      $('#label').val('');
      $('#link').val('#');
      $('.access').val(null).trigger('change');

      // set icon
      $.iconpicker.batch('.icp.iconpicker-element', 'destroy');
      $('.icp-auto').iconpicker({
        selected: 'no-icon',
      });

      submit_form('insert');
    });

    // Modal Edit
    $('.modalEdit').on('click', function() {

      // change text
      $('#menuModalLabel').html('Edit Menu');
      $('.modal-footer button[type=submit]').html('Edit');

      // repopulate form
      let id = $(this).attr('id');
      let label = $(this).attr('label');
      let link = $(this).attr('link');
      let icon = $(this).attr('icon');
      if (icon == '') {
        icon = 'no-icon';
      }
      $("#id").val(id);
      $("#label").val(label);
      $("#link").val(link);

      // set icon based on database
      $.iconpicker.batch('.icp.iconpicker-element', 'destroy');
      $('.icp-auto').iconpicker({
        selected: icon,
      });

      $.ajax({
        url: <?= json_encode(base_url()) ?> + `/menus/groupSelected/${id}`,
        method: 'GET',
        cache: false,
        success: function(data) {
          data = JSON.parse(data);

          var arr = [];
          data.forEach(function(obj) {
            arr.push(obj.group_id);
          });
          $('.access').val(arr);
          $('.access').trigger('change');
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });

      $.ajax({
        url: <?= json_encode(base_url()) ?> + `/menus/crudSelected/${id}`,
        method: 'GET',
        cache: false,
        success: function(data) {
          data = JSON.parse(data);

          if (data != null) {
            if (data.insert == 1) {
              $("input[name=insert]").prop("checked", true);
            } else {
              $("input[name=insert]").prop("checked", false);
            }
            if (data.update == 1) {
              $("input[name=update]").prop("checked", true);
            } else {
              $("input[name=update]").prop("checked", false);
            }
            if (data.delete == 1) {
              $("input[name=delete]").prop("checked", true);
            } else {
              $("input[name=delete]").prop("checked", false);
            }
            if (data.validate == 1) {
              $("input[name=validate]").prop("checked", true);
            } else {
              $("input[name=validate]").prop("checked", false);
            }
          } else {
            $("input[name=read]").prop("checked", false);
            $("input[name=insert]").prop("checked", false);
            $("input[name=update]").prop("checked", false);
            $("input[name=delete]").prop("checked", false);
            $("input[name=validate]").prop("checked", false);
          }

        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });

      submit_form('update', id);
    });

    // delete menu
    $('.del-button').on('click', function() {
      let x = confirm('Delete this menu?');
      const menuId = $(this).attr('id');

      if (x) {
        $.ajax({
          type: "POST",
          url: <?= json_encode(base_url()) ?> + `/menus/delete/${menuId}`,
          data: {
            menuId: menuId,
          },
          cache: false,
          success: function(data) {
            $("li[data-id='" + id + "']").remove();

            window.location.href = <?= json_encode(base_url('menus')) ?>;
          },
          error: function(xhr, status, error) {
            alert(error);
          },
        });
      }
    });
  });
</script>
<?= $this->endSection(); ?>