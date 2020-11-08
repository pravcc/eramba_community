<?php
if (isset($edit) || empty($ldapAuth['LdapConnectorAuthentication']['auth_users'])) {
	return false;
}

if ($ldapConnection !== true) {
	echo $this->element('not_found', array(
		'message' => $ldapConnection
	));

	return false;
}
?>

<div class="form-group">
	<label class="col-md-2 control-label"><?php echo __( 'LDAP User' ); ?>:</label>
	<div class="col-md-10">
		<?php
		echo $this->Form->input('User.ldap_user', array(
			'type' => 'hidden',
			'label' => false,
			'div' => false,
			'class' => 'select2 full-width-fix select2-offscreen',
			'id' => 'ldap-user-field',
			'default' => ''
		));
		?>
		<span class="help-block"><?php echo __('System is configured to authenticate users using LDAP. You can select an LDAP user to load data and autocomplete available form fields below.'); ?></span>
	</div>
</div>



<script type="text/javascript">
jQuery(function($) {
	$("#ldap-user-field").select2({
		placeholder: "<?php echo __('Choose an LDAP user'); ?>",
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: "/users/searchLdapUsers",
			dataType: 'json',
			quietMillis: 550,
			data: function (term, page) {
				return {
					q: term, // search term
				};
			},
			results: function (data, page) { // parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter the remote JSON data
				if (typeof data.success != "undefined" && !data.success) {
					bootbox.alert({
						title: "<?php echo __('Error occured'); ?>",
						message: data.message
					});

					return {results: []};
				}

				return { results: data };
			},
			cache: true
		},
		initSelection: function(element, callback) {
			// the input tag has a value attribute preloaded that points to a preselected repository's id
			// this function resolves that id attribute to an object that select2 can render
			// using its formatResult renderer - that way the repository name is shown preselected
			var id = $(element).val();
			if (id !== "") {
				callback({
					id: id,
					text: id
				});
			}
		},
		// formatResult: repoFormatResult, // omitted for brevity, see the source of this page
		// formatSelection: repoFormatSelection,  // omitted for brevity, see the source of this page
		dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
		escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
	})
	.on("change", function(e) {
		var $this = $(this);
		var ldapUser = $this.val();

		if (ldapUser) {
			Eramba.Ajax.blockEle($(".modal-content"));
			$.ajax({
				type: "GET",
				dataType: "HTML",
				url: "/users/chooseLdapUser/" + ldapUser
			}).done(function(data) {
				$("#user-add-form").html(data);
				$("#local-account").uniform();

				Eramba.Ajax.unblockEle($(".modal-content"));
			}).fail(function() {
				$this.select2("val", null);
			});
		}
	});
});
</script>