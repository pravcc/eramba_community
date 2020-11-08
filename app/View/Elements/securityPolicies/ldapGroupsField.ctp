<!-- <br /> -->
<?php
if ($ldapConnection !== true) {
	echo $this->element('not_found', array(
		'message' => $ldapConnection
	));

	return false;
}
?>
<?php
$selected = array();
if (isset($this->request->data['SecurityPolicyLdapGroup'])) {
	foreach ($this->request->data['SecurityPolicyLdapGroup'] as $entry) {
		$selected[] = $entry['id'];
	}
}

if (isset($this->request->data['SecurityPolicy']['ldap_groups']) && is_array($this->request->data['SecurityPolicy']['ldap_groups'])) {
	foreach ($this->request->data['SecurityPolicy']['ldap_groups'] as $entry) {
		$selected[] = $entry;
	}
}

if (isset($this->request->data['groups']) && is_array($this->request->data['groups'])) {
	foreach ($this->request->data['groups'] as $entry) {
		$selected[] = $entry;
	}
}

?>
<?php
echo $this->Form->input('SecurityPolicy.ldap_groups', array(
	'options' => $groups,
	'label' => false,
	'div' => false,
	'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
	'id' => 'ldap-groups-field',
	'multiple' => true,
	'selected' => $selected
));
?>
<span class="help-block"><?php echo __('Choose one or more LDAP groups.'); ?></span>

<script type="text/javascript">
jQuery(function($) {
	$("#ldap-groups-field").select2({
		minimumInputLength: 2
	});
});
</script>