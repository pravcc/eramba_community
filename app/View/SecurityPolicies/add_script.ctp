<script type="text/javascript">
jQuery(function($) {
	$("#use-attachments").on("change", function(e) {
		var $selected = $(this).find("option:selected").val();

		// if ($selected != currentContentType) {
		// 	var $contentTypeWrappper = $("[data-content-type='" + $selected + "']");
		// }

		if ($selected == "<?php echo SECURITY_POLICY_USE_CONTENT; ?>") {
			$("#tinymce-wrapper").show();
		}
		else {
			$("#tinymce-wrapper").hide();
		}

		if ($selected == "<?php echo SECURITY_POLICY_USE_URL; ?>") {
			$("#url-wrapper").show();
		}
		else {
			$("#url-wrapper").hide();
		}

		if ($selected == "<?php echo SECURITY_POLICY_USE_ATTACHMENTS; ?>") {
			$("#attachments-wrapper").show();
		}
		else {
			$("#attachments-wrapper").hide();
		}
	}).trigger("change");
});
</script>