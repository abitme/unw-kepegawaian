<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Role Access</h1>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<h5>Role : <?= $group->name ?></h5>
<form action="<?= base_url('auth/change_access') ?>" method="post">
    <table class="table table-hover">
        <thead>
            <tr>
                <!-- <th scope="col">#</th> -->
                <th scope="col">Menu</th>
                <th scope="col" class="text-center">Access Menu</th>
                <th scope="col" class="text-center">Edit</th>
            </tr>
        </thead>
        <tbody>

            <?php

            function get_menusAccess($items, $groups_access, $group)
            {
                $html = '';
                foreach ($items as $key => $value) {

                    if ($value['parent'] == 0) {
                        $html .=
                            '<tr>
                            <td> ' . $value['label'] . ' </td>';

                        $html .=
                            '<td>
                                <div class="form-check text-center align-middle">';
                        if (in_array_r($value['id'], $groups_access)) {

                            $html .=
                                '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $value['id'] . '" value = \'1\' checked> 
                                        <input type="hidden" name="group_id[]" value="' . $group->id . '">
                                        <input type="hidden" name="menu_id[]" value="' . $value['id'] . '">';
                        } else {
                            $html .=
                                '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $value['id'] . '" value = \'1\' ';
                        }

                        $html .=
                            '</div>
                            </td>
                            </tr>';
                    }

                    if ($value['parent'] == 0 && isset($value['child'])) {
                        foreach ($value['child'] as $child) {

                            $html .=
                                '<tr>
                                <td style="text-indent: 1.5rem;"> ' . $child['label'] . ' </td>';

                            $html .=
                                '<td>
                                    <div class="form-check text-center align-middle">';
                            if (in_array_r($child['id'], $groups_access)) {

                                $html .=
                                    '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $child['id'] . '" value = \'1\' checked> 
                                            <input type="hidden" name="group_id[]" value="' . $group->id . '">
                                            <input type="hidden" name="menu_id[]" value="' . $child['id'] . '">';
                            } else {
                                $html .=
                                    '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $child['id'] . '" value = \'1\' ';
                            }
                            $html .= '</td><td>';

                            if (!isset($child['child'])) {
                                $html .=
                                    '<div class="form-check text-center align-middle">
                                        <a class="btn btn-sm edit-button" data-group = "' . $group->id . '" data-menu = "' . $child['id'] . '" data-label= "' . $child['label'] . '" data-toggle="modal" data-target="#accessModal">
                                            <i class="fas fa-edit text-info"></i>
                                        </a>
                                    </div>
                                    </td>';
                            } else {
                                $html .= '</td>';
                            }

                            $html .=
                                '</div>
                                </tr>';

                            if (isset($child['child'])) {
                                foreach ($child['child'] as $subChild) {
                                    $html .=
                                        '<tr>
                                        <td style="text-indent: 3rem;"> ' . $subChild['label'] . ' </td>';

                                    $html .=
                                        '<td>
                                            <div class="form-check text-center align-middle">';
                                    if (in_array_r($subChild['id'], $groups_access)) {

                                        $html .=
                                            '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $subChild['id'] . '" value = \'1\' checked>
                                                    <input type="hidden" name="group_id[]" value="' . $group->id . '">
                                                    <input type="hidden" name="menu_id[]" value="' . $subChild['id'] . '">';
                                    } else {
                                        $html .=
                                            '<input type="checkbox" class="form-check-input show" data-group = ' . $group->id . ' data-menu="' . $subChild['id'] . '" value = \'1\' ';
                                    }
                                    $html .= '</td><td>';

                                    $html .=
                                        '<div class="form-check text-center align-middle">
                                            <a class="btn btn-sm edit-button" data-group = "' . $group->id . '" data-menu = "' . $subChild['id'] . '" data-label= "' . $subChild['label'] . '" data-toggle="modal" data-target="#accessModal">
                                                <i class="fas fa-edit text-info"></i>
                                            </a>
                                        </div>
                                        </td>';

                                    $html .=
                                        '</div>
                                        </td>
                                        </tr>';
                                }
                            }
                        }
                    }

                    if (array_key_exists('child', $value)) {
                        $html .= get_menusAccess($value['child'], $groups_access, $group);
                    }
                }

                return $html;
            }

            $items = dataMenu(1);
            // var_dump($items);
            print get_menusAccess($items, $groups_access, $group);

            ?>

        </tbody>
    </table>
    <a href="<?= base_url('groups') ?>" class="btn btn-back">Cancel</a>
</form>

<!-- Modal -->
<div class="modal fade" id="accessModal" tabindex="-1" role="dialog" aria-labelledby="accessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessModalLabel">Edit Access: <?= $group->name ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('auth/change_crud_access') ?>" method="post">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Menu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="label"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
    $(document).ready(function() {

        // change groups access 
        $(".show").click(function() {
            const roleId = $(this).data('group');
            const menuId = $(this).data('menu');
            $.ajax({
                url: <?= json_encode(base_url('auth/change_access/')) ?>,
                type: 'POST',
                data: {
                    roleId: roleId,
                    menuId: menuId
                },
                success: function(data) {

                }
            });
        });

        // change groups access of crud (insert, update, delete, validate)
        $('.edit-button').on('click', function() {

            const label = $(this).data('label');
            const roleId = $(this).data('group');
            const menuId = $(this).data('menu');

            // create menus access (crud form) to dom 
            $.ajax({
                url: <?= json_encode(base_url()) ?> + '/auth/menu_access/' + menuId,
                method: 'POST',
                data: {
                    menuId: menuId
                },
                cache: false,
                success: function(data) {
                    data = JSON.parse(data);

                    if (data.validate == 1) {
                        $('.modal thead tr th:first-child').after(
                            `<th scope="col" class="text-center">Validate</th>`);
                        $('.modal tbody tr td:first-child').after(
                            `<td>
                            <div class="form-check text-center align-middle">
                                <input type="checkbox" class="form-check-input" value='1' name="validate">
                            </div>
                        </td>`);
                    }
                    if (data.delete == 1) {
                        $('.modal thead tr th:first-child').after(
                            `<th scope="col" class="text-center">Delete</th>`);
                        $('.modal tbody tr td:first-child').after(
                            `<td>
                            <div class="form-check text-center align-middle">
                                <input type="checkbox" class="form-check-input" value='1' name="delete">
                            </div>
                        </td>`);
                    }
                    if (data.update == 1) {
                        $('.modal thead tr th:first-child').after(
                            `<th scope="col" class="text-center">Update</th>`);
                        $('.modal tbody tr td:first-child').after(
                            `<td>
                            <div class="form-check text-center align-middle">
                                <input type="checkbox" class="form-check-input" value='1' name="update">
                            </div>
                        </td>`);
                    }
                    if (data.insert == 1) {

                        $('.modal thead tr th:first-child').after(
                            `<th scope="col" class="text-center">Insert</th>`);
                        $('.modal tbody tr td:first-child').after(
                            `<td>
                            <div class="form-check text-center align-middle">
                                <input type="checkbox" class="form-check-input" value='1' name="insert">
                            </div>
                        </td>`);
                    }

                    if (data.insert == 1 || data.update == 1 || data.delete == 1 || data.validate == 1) {
                        $('.modal table').after('<button type="submit" class="btn btn-primary">Save</button>');
                    } else {
                        $('.modal thead tr th:first-child').after(`<td></td>`);
                        $('.modal tbody tr td:first-child').after(`<td>Read Only</td>`);
                    }

                    $('.modal tbody tr td:first-child').after(`<input type="hidden" name="group_id" value="${roleId}">`);
                    $('.modal tbody tr td:first-child').after(`<input type="hidden" name="menu_id" value="${menuId}">`);

                    // get selected groups access (crud access) from database
                    $.ajax({
                        url: <?= json_encode(base_url()) ?> + '/auth/groups_access/' + roleId + '/' + menuId,
                        data: {
                            roleId: roleId,
                            menuId: menuId
                        },
                        method: 'POST',
                        cache: false,
                        success: function(data) {
                            $('.label').html(label);

                            data = JSON.parse(data);

                            if (data != null) {
                                if (data.read == 1) {
                                    $("input[name=read]").prop("checked", true);
                                } else {
                                    $("input[name=read]").prop("checked", false);
                                }
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
                },
                error: function(xhr, status, error) {
                    alert(error);
                },
            });

        });

        // clear modal form after closed
        $('#accessModal').on('hide.bs.modal', function(e) {
            $('.form-check-input').prevAll('input').remove();
            $('.modal thead tr th:first-child').nextAll().remove();
            $('.modal tbody tr td:first-child').nextAll().remove();
            $('.modal table').nextAll().remove();
        })

    });
</script>
<?= $this->endSection(); ?>