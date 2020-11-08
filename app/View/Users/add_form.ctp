<?php 
$statuses = array(
	USER_ACTIVE 	=> __('Active'),
	USER_NOTACTIVE 	=> __('Inactive')
);
$readonly = false;
if (isset($edit)) {
	if (!$isUserAdmin) {
		$readonly = true;
	}
}

$conds = !isset($edit);
$conds = $conds	|| $isUserAdmin;
?>

<?php echo $this->element('users/ldap_users_field'); ?>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Name' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.name', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'error' => array(
				'notEmpty' 	=> __('Name is required.'),
			),
			'readonly' => $readonly
		) ); ?>
		<span class="help-block"><?php echo __( 'First Name' ); ?></span>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Surname' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.surname', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'readonly' => $readonly
		) ); ?>
		<span class="help-block"><?php echo __( 'Surname' ); ?></span>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Email' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.email', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'error' => array(
				'email' 	=> __('Email address is in wrong format.'),
				'notEmpty' 	=> __('Email address is required.'),
			),
			'readonly' => $readonly
		) ); ?>
		<span class="help-block"><?php echo __( 'eramba sends emails for notifications and password resets.' ); ?></span>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Login Name' ); ?>:</label>
	<div class="col-md-10">
		<?php
		$tmpReadonly = false;
		if ((isset($id) && $id == ADMIN_ID) || $readonly) {
			$tmpReadonly = true;
		}
		?>
		<?php echo $this->Form->input( 'User.login', array(
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'error' => array(
				'notEmpty' 	=> __('Login is required.'),
				'unique' => __('Same login already exists.')
			),
			'readonly' => $tmpReadonly
		) ); ?>
		<span class="help-block"><?php echo __( 'You will use this login name to get access to eramba. If you enabled LDAP authentication (System / Settings / Authentication) you need to make sure the login you enter here is the same as your LDAP login (AD login).' ); ?></span>
	</div>
</div>

<?php if (!empty($ldapAuth['LdapConnectorAuthentication']['auth_users']) || !empty($ldapAuth['LdapConnectorAuthentication']['oauth_google'])) : ?>

	<div class="form-group">
		<label class="col-md-2 control-label"><?php echo __('Local Account'); ?>:</label>
		<div class="col-md-10">
			<label class="checkbox">
				<?php echo $this->Form->input('User.local_account', array(
					'type' => 'checkbox',
					'label' => false,
					'div' => false,
					'class' => 'uniform',
					'id' => 'local-account',
					'disabled' => !$conds
				)); ?>
				<?php echo __('Yes'); ?>
			</label>
			<span class="help-block"><?php echo __('If you enable the checkbox this user account will have a password stored on eramba. If you uncheck this box, the password will be authenticated against LDAP or OAuth (if you enabled LDAP or OAuth on System / Setings / Authentication).') ?></span>
		</div>
	</div>

<?php else : ?>
	<?php
	/*echo $this->Form->input('User.local_account', array(
		'type' => 'hidden',
		'value' => 1
	));*/
	?>
<?php endif; ?>

<?php
$disablePassword = false;
if (!empty($ldapAuth['LdapConnectorAuthentication']['auth_users'])) {
	$disablePassword = true;
}
?>
<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Password' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.pass', array(
			'type' => 'password',
			'label' => false,
			'div' => false,
			'required' => false,
			'class' => 'form-control',
			// 'error' => array(
			// 	'between' => __('Passwords must be between 8 and 30 characters long.'),
			// 	'compare' => __('Password and verify password must be same.')
			// ),
			'disabled' => $disablePassword,
			'id' => 'user-password'
		) ); ?>
		<span class="help-block"><?php echo __( 'Set your new password.' ); ?></span>
		<?php
		echo $this->Users->passwordPolicyAlert();
		?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Verify your new password' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.pass2', array(
			'type' => 'password',
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'disabled' => $disablePassword,
			'id' => 'user-verify-password'
		) ); ?>
		<span class="help-block"><?php echo __( 'Type your new password again.' ); ?></span>
	</div>
</div>

<?php
if (!isset($edit) || $this->request->data['User']['id'] != ADMIN_ID) {
	echo $this->FieldData->inputs([
		$FieldDataCollection->Portal
	], [
		'data-select2-readonly' => !$conds
	]);
}
?>

<?= $this->FieldData->inputs([
	$FieldDataCollection->Group
], [
	'data-select2-readonly' => !$conds
]); ?>
<div id="group-conflicts"></div>

<script type="text/javascript">
	jQuery(function($) {
		$("[data-select2-readonly=1]").select2().select2('readonly', true);

		$("#UserGroup").on("change", function(e) {
			var groups = [];
			$.each($("#UserGroup option:selected"), function(i, e) {
				groups.push($(e).val());
			});

			$.ajax({
				type: "GET",
				url: "<?= Router::url(['controller' => 'users', 'action' => 'checkConflicts']) ?>",
				data: {
					groups: groups
				}
			})
			.done(function(data) {
				$("#group-conflicts").html(data);
			})
		});
	});
</script>


<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Language' ); ?>:</label>
	<div class="col-md-10">
		<?php echo $this->Form->input( 'User.language', array(
			'options' => availableLangs(),
			'label' => false,
			'div' => false,
			'class' => 'form-control'
		) ); ?>
		<span class="help-block"><?php echo __( 'Select desired language.' ); ?></span>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'Status' ); ?>:</label>
	<div class="col-md-10">
		<?php
		$disabled = false;
		if (isset($id) && $id == 1) {
			$disabled = true;
		}
		?>
		<?php echo $this->Form->input( 'User.status', array(
			'options' => $statuses,
			'label' => false,
			'div' => false,
			'default' => USER_ACTIVE,
			'class' => 'form-control',
			'error' => array(
				'notEmpty' 	=> __('Status is required.'),
			),
			'disabled' => $disabled,
			'readonly' => $readonly,
			'id' => 'user-status'
		) ); ?>
		<span class="help-block"><?php echo __( 'Select user status. If LDAP is the authenticator accounts are managed by the remote directory, not eramba.' ); ?></span>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __('REST APIs'); ?>:</label>
	<div class="col-md-10">
		<label class="checkbox">
			<?php echo $this->Form->input('User.api_allow', array(
				'type' => 'checkbox',
				'label' => false,
				'div' => false,
				'class' => 'uniform'
			)); ?>
			<?php echo __('Allow'); ?>
		</label>
		<span class="help-block"><?php echo __('Check to allow the use of REST APIs for this user account.') ?></span>
	</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		FormComponents.init()
		
		$("#local-account").on("change", function(e) {
			if ($(this).is(":checked")) {
				$("#user-password, #user-verify-password<?= ((isset($id) && $id == 1) || !empty($ldapAuth['LdapConnectorAuthentication']['oauth_google'])) ? '' : ', #user-status' ?>").removeAttr("disabled");
			}
			else {
				$("#user-password, #user-verify-password<?= ((isset($id) && $id == 1) || !empty($ldapAuth['LdapConnectorAuthentication']['oauth_google'])) ? '' : ', #user-status' ?>").attr("disabled", "disabled");
			}
		}).trigger("change");
	});
</script>