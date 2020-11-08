<dl class="dl-horizontal">
	<dt><?php echo __('Item ID'); ?></dt>
	<dd>
		<?php
		echo $this->Content->text($data['CompliancePackageItem']['item_id']);
		?>
	</dd>
	<dt><?php echo __('Item Name'); ?></dt>
	<dd>
		<?php
		echo $this->Content->text($data['CompliancePackageItem']['name']);
		?>
	</dd>
	<dt><?php echo __('Item Description'); ?></dt>
	<dd>
		<?php
		echo $this->Content->text($data['CompliancePackageItem']['description']);
		?>
	</dd>
	<dt><?php echo __('Item Details'); ?></dt>
	<dd>
		<?php
		echo $this->Content->text($data['CompliancePackageItem']['audit_questionaire']);
		?>
	</dd>
</dl>