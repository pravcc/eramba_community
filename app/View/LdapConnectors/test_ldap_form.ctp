<div class="pl-10 pr-10">
    <?php
    echo $this->Form->create($testLdapFormName, [
        'class' => '',
        'data-yjs-form' => $testLdapFormName
    ]);
    ?>

    <div class="form-group">
        <label class="control-label"><?= $fieldFriendlyName ?>:</label>
        <input type="text" name="<?= $fieldName ?>" class="form-control">
    </div>

    <?php echo $this->Form->end(); ?>
</div>