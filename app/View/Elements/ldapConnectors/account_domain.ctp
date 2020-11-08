<div class="form-group account-domain-field">
	<label class="col-md-2 control-label"><?php echo __('Mail Domain'); ?>:</label>
	<div class="col-md-10">
		<?php
		echo $this->Form->input('LdapConnector.ldap_group_mail_domain', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control'
		));
		?>
		<span class="help-block"><?php echo __('Eramba will use the return value from the attribute "Account Attribute" and add this domain in order to have a complete email address.') ?></span>
	</div>
</div>