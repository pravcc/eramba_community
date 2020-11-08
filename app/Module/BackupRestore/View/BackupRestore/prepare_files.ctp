<div class="pl-10 pr-10">
    <?= $this->Alerts->info(__('Here you can download all backup files one by one. We need to split it to multiple files to avoid long time downloading of one big file.')); ?>
    <hr>
    <h5><?= __('Prepared backup files ready for download') . ':'; ?></h5>
    <div class="table-responsive mb-20">
        <table class="table table-striped table-bordered table-xs table-condensed">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">#</th>
                    <th style="width: 70%"><?= __('Filename'); ?></th>
                    <th style="width: 20%"><?= __('Size'); ?></th>
                    <th class="text-center" style="width: 5%"><?= __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backupFileParts as $key => $backupFilePart): ?>
                <tr>
                    <td class="text-center"><?= $key + 1; ?></td>
                    <td><?= __('Files backup part %s', $key + 1) ?></td>
                    <td><?= $backupFilePart['sizeFriendly']; ?></td>
                    <td class="text-center"><a class="btn btn-primary btn-xs" href="<?= Router::url(['plugin' => 'backup_restore', 'controller' => 'BackupRestore', 'action' => 'downloadFile', $key]); ?>"><?= __('Download'); ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>