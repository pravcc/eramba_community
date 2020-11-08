<?php
if (empty($ldapConnectorId)) {
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
// $selected = array();
// if (isset($this->request->data['AwarenessProgramLdapGroup'])) {
// 	foreach ($this->request->data['AwarenessProgramLdapGroup'] as $entry) {
// 		$selected[] = $entry['id'];
// 	}
// }

// if (isset($this->request->data['AwarenessProgram']['ldap_groups']) && is_array($this->request->data['AwarenessProgram']['ldap_groups'])) {
// 	foreach ($this->request->data['AwarenessProgram']['ldap_groups'] as $entry) {
// 		$selected[] = $entry;
// 	}
// }

// if (isset($this->request->data['groups']) && is_array($this->request->data['groups'])) {
// 	foreach ($this->request->data['groups'] as $entry) {
// 		$selected[] = $entry;
// 	}
// }

?>
<?php
$options = [
	'type' => 'select',
	'multiple' => true
];

// if (isset($selected)) {
// 	$options['selected'] = $selected;
// }

echo $this->FieldData->input($FieldDataCollection->AwarenessProgramLdapGroup, $options);

// echo $this->Form->input('AwarenessProgram.ldap_groups', array(
// 	'options' => $groups,
// 	'label' => false,
// 	'div' => false,
// 	'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
// 	'id' => 'ldap-groups-field',
// 	'multiple' => true,
// 	'selected' => $selected,
// 	'disabled' => !empty($edit) ? true : false
// ));
?>
<!-- <span class="help-block"><?php echo __('Choose one or more LDAP groups'); ?></span> -->

<script type="text/javascript">
/*jQuery(function($) {
	$("#ldap-groups-field").off("change").on("change", function(e){
		var groups = $(this).val();

		if (groups != null && groups.length > 0) {
			// blockWizard();

			$.ajax({
				type: "POST",
				dataType: "HTML",
				url: "/awarenessPrograms/ldapIgnoredUsers/" + $("#ldap-connector-select option:selected").val(),
				data: {
					groups: groups,
					// selectedUsers: $.unique($.merge(selectedUsers, $("#ldap-ignored-users-field").select2('val'))),
					edit: <?php echo !empty($edit) ? '1' : '0'; ?>
				}
			}).done(function(data) {
				$("#ldap-ignored-wrapper").html(data);
				$("#ldap-ignored-select").show();

				unblockWizard();
				$("#ldap-check-modal-btn").removeAttr("disabled");
			});
		}
		else {
			$("#ldap-ignored-select").hide();
			$("#ldap-ignored-wrapper").empty();
			$("#ldap-check-modal-btn").attr("disabled", true);
		}
	}).trigger("change");
	
	// $("#ldap-groups-field").select2({
	// 	minimumInputLength: 2
	// });
});*/
</script>