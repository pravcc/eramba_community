<div class="pl-10 pr-10">
    <?php
    echo $this->Alerts->info(__('Keep in mind backups only include the system database - you must backup attachments separately (from app/webroot/files/ directory) from your operating system.'));

    echo $this->Form->create($brFormName, [
        'url' => ['controller' => 'backupRestore', 'action' => 'index'],
        'class' => '',
        'type' => 'file',
        'id' => 'backup-restore-form',
        'data-yjs-form' => $brFormName
    ]);
    ?>

    <div class="form-group">
        <label class="control-label"><?php echo __( 'Restore Database' ); ?>:</label>
        <?php echo $this->Form->input('ZipFile', array(
            'type' => 'file',
            'label' => false,
            'div' => false,
            'class' => 'form-control file-styled',
            'data-style' => 'fileinput',
            'required' => false
        )); ?>
        <span class="help-block">
            <?php echo __( 'Upload your ZIP file here.' ); ?><br />
            <?php echo __('Maximum filesize for upload is configured to %s.', ini_get('post_max_size')); ?>
        </span>
    </div>

    <?php echo $this->Form->end(); ?>
</div>