<div class="form-group">
	<button class="btn btn-default" data-yjs-request="crud/showForm" data-yjs-datasource-url="/settings/testMailConnection" data-yjs-target="modal" data-yjs-event-on="click" data-yjs-forms="<?= $formName; ?>">
		<?= __('Test connection'); ?>
	</button>
	<span class="help-block"><?php echo __('You may test your SMTP settings by clicking on this button. You will be asked an email to which eramba will try to send an email using the settings defined in this form.') ?></span>
</div>

<script type="text/javascript">
jQuery(function($) {
	var $smtpEle = $("#SettingSMTPHOST, #SettingSMTPUSER, #SettingSMTPPWD, #SettingSMTPTIMEOUT, #SettingSMTPPORT, #SettingUSESSL");
	$("#SettingSMTPUSE").on("change", function(e) {
		if ($(this).val() == "1") {
			$smtpEle.prop("readonly", false);
		}
		else {
			$smtpEle.prop("readonly", true);
		}
	}).trigger("change");
});
</script>