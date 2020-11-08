<?php
if (empty($ldapConnectorId) || empty($groups)) {
	return false;
}

if ($ldapConnection !== true) {
	echo $this->element('not_found', array(
		'message' => $ldapConnection
	));

	return false;
}
?>

<?php
echo $this->FieldData->input($FieldDataCollection->AwarenessProgramIgnoredUser, [
	'type' => 'select',
	'multiple' => true
]);

return;
?>
<?php
// $selected = array();
// if (isset($this->request->data['AwarenessProgramIgnoredUser'])) {
// 	foreach ($this->request->data['AwarenessProgramIgnoredUser'] as $entry) {
// 		$selected[] = $entry['id'];
// 	}
// }

// if (isset($this->request->data['AwarenessProgram']['ignored_users_uid']) && is_array($this->request->data['AwarenessProgram']['ignored_users_uid'])) {
// 	foreach ($this->request->data['AwarenessProgram']['ignored_users_uid'] as $entry) {
// 		$selected[] = $entry;
// 	}
// }

// if (isset($this->request->data['selectedUsers']) && is_array($this->request->data['selectedUsers'])) {
// 	foreach ($this->request->data['selectedUsers'] as $entry) {
// 		$selected[] = $entry;
// 	}
// }

?>
<?php
// echo $this->Form->input('AwarenessProgram.ignored_users_uid', array(
// 	'options' => $users,
// 	'label' => false,
// 	'div' => false,
// 	'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
// 	'id' => 'ldap-ignored-users-field',
// 	'multiple' => true,
// 	'selected' => $selected,
// 	'disabled' => !empty($edit) ? true : false
// ));
?>
<!-- <span class="help-block"><?php echo __('Choose one or more users that should not supposed to be bothered with the training'); ?></span> -->

<script type="text/javascript">
// jQuery(function($) {
// 	$("#ldap-ignored-users-field").select2();
// });
</script>