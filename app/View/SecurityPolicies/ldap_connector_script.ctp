<script type="text/javascript">
jQuery(function($) {
	$("#permission").on("change", function(e) {
		if ($(this).find("option:selected").val() == "<?php echo SECURITY_POLICY_LOGGED; ?>") {
			$("#ldap-connector-select-wrapper").show();
			$("#ldap-connector-select").trigger("change");
		}
		else {
			$("#ldap-connector-select-wrapper").hide();
			$("#ldap-group-select").hide();
			$("#ldap-group-wrapper").empty();
		}
	}).trigger("change");

	var selectedGroups = new Array();
	<?php if (!empty($this->request->data['SecurityPolicyLdapGroup'])) : ?>
		<?php foreach ($this->request->data['SecurityPolicyLdapGroup'] as $group) : ?>
			selectedGroups.push("<?php echo $group['name']; ?>");
		<?php endforeach; ?>
	<?php endif; ?>

	$("#ldap-connector-select").on("change", function(e) {
		if ($(this).find("option:selected").val()) {
			var $blockLoader = $(this).closest(".tab-content");
			$.blockUI($blockLoader);

			$.ajax({
				type: "POST",
				dataType: "HTML",
				url: "/securityPolicies/ldapGroups/" + $("#ldap-connector-select option:selected").val(),
				data: {groups: selectedGroups}
			}).done(function(data) {
				$("#ldap-group-wrapper").html(data);
				$("#ldap-group-select").show();

				$.unblockUI($blockLoader);
			});

		}
		else {
			$("#ldap-group-select").hide();
			$("#ldap-group-wrapper").empty();
		}
	}).trigger("change");
});
</script>