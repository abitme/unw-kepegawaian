<?php

$success    = session()->getFlashdata('success');
$error      = session()->getFlashdata('error');
$danger     = session()->getFlashdata('danger');
$warning    = session()->getFlashdata('warning');

if ($success) {
    $alert_status   = 'alert-success';
    $status         = 'Success';
    $message        = $success;
}

if ($error) {
    $alert_status   = 'alert-danger';
    $status         = 'Error';
    $message        = $error;
}

if ($danger) {
    $alert_status   = 'alert-danger';
    $status         = '';
    $message        = $danger;
}

if ($warning) {
    $alert_status   = 'alert-warning';
    $status         = 'Warning';
    $message        = $warning;
}

?>

<?php if ($success || $error || $warning || $danger) : ?>
    <div class="flashdata" data-status="<?= $status ?>" data-message="<?= $message ?>"></div>
    <?php if ($error) : ?>
        <!-- <script>
            $(document).ready(function() {
                var aiModal = new bootstrap.Modal(document.getElementById('aiModal'), {
                    keyboard: false
                })
                aiModal.show(aiModal)
            });
        </script> -->
    <?php endif ?>

<?php endif; ?>