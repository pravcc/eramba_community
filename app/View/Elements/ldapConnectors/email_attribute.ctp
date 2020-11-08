<div class="form-group email-attribute-field">
	<label class="col-md-2 control-label"><?php echo __('Email Attribute'); ?>:</label>
	<div class="col-md-10">
		<?php
		echo $this->Form->input('LdapConnector.ldap_group_email_attribute', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control'
		));
		?>
		<span class="help-block"><?php echo __('If you can pull from your directory the email of each account that is member on each group then set it here. In Active Directory this is typically "mail". The returned value must be the email and nothing else.') ?></span>
	</div>
</div>