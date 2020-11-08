<?php
App::uses('Router', 'Routing');
App::uses('SystemHealthLib', 'Lib');

$backupsWritable = SystemHealthLib::writeBackups();

$systemHealthIssue = false;
?>
<?php if (empty($backupsWritable)) : ?>
    <?php
    $systemHealthIssue = true;

    $systemHealthLink = $this->Html->link(__('System Health'), '#', [
        'data-yjs-request' => 'crud/showForm',
        'data-yjs-target' => 'modal',
        'data-yjs-event-on' => 'click',
        'data-yjs-modal-size-width' => '80',
        'data-yjs-datasource-url' => Router::url(['controller' => 'settings', 'action' => 'systemHealth'])
    ]);

    echo $this->Alerts->danger(__('Your backups folder is not accessible or writable by the system! Please go to %s and fix this issue.', $systemHealthLink));
    ?>
<?php endif; ?>

<?= $this->element('settings/edit/form') ?>

<script type="text/javascript">
    jQuery(function($) {
        var $backupEnableToggle = $("#SettingBACKUPSENABLED");

        <?php if ($systemHealthIssue): ?>

            // we disable the "Enable backups" checbkox in case the system health has issues
            $backupEnableToggle.prop("disabled", true);
            $.uniform.update($backupEnableToggle);

        <?php endif; ?>

        $("#SettingEditForm").find("select").removeClass("form-control").addClass("col-md-12").select2({
            minimumResultsForSearch: -1
        });
    });
</script>