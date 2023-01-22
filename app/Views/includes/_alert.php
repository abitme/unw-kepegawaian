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
        $status         = 'Warning!';
        $message        = $warning;
    }

?>

<?php if ($success || $error || $warning || $danger) : ?>
<div class="row">
    <div class="col-md-12"> 
        <div class="alert <?= $alert_status; ?> alert-dismissible fade show" role="alert">
        <strong><?= $status; ?></strong> <?= $message; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
    </div>
</div>
<?php endif; ?>