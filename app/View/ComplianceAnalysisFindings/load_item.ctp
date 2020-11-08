<div class="form-group">
	<label class="col-md-2 control-label">
        <?php echo $packages[$cpID]; ?>:
    </label>
	<div class="col-md-10">
		<?php
        echo $this->Form->input('ComplianceAnalysisFinding.CompliancePackageItem', array(
            'options' => $packageItemsList[$cpID],
            'hiddenField' => $hiddenField,
            'label' => false,
            'div' => false,
            'class' => 'select2 col-md-12 full-width-fix select2-offscreen package-item-field',
            'multiple' => true,
            'errorMessage' => $hiddenField
        ));
        ?>
		<!-- <span class="help-block"><?php echo ''; ?></span> -->
	</div>
</div>