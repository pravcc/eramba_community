<?php
echo $this->Form->input($fieldName, array(
	'type' => 'textarea',
	'label' => false,
	'div' => false,
	'class' => 'form-control',
	'id' => 'policy-description',
	'default' => isset($default) ? $default : null,
	'disabled' => !empty($disabled) ? $disabled : false
));
?>
<script type="text/javascript">
jQuery(function($) {
	tinymce.init({
		selector: "textarea#policy-description",
		height: 400,
		plugins: "table textcolor",
		tools: "inserttable",
		toolbar: "insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | print preview media fullpage | forecolor backcolor emoticons",
		relative_urls: false,
		readonly: ($("#policy-description").prop("disabled") ? true : false),
		font_formats :  "Andale Mono=andale mono,times;"+
				        "Arial=arial,helvetica,sans-serif;"+
				        "Arial Black=arial black,avant garde;"+
				        "Book Antiqua=book antiqua,palatino;"+
				        "Comic Sans MS=comic sans ms,sans-serif;"+
				        "Courier New=courier new,courier;"+
				        "Georgia=georgia,palatino;"+
				        "Helvetica=helvetica;"+
				        "Impact=impact,chicago;"+
				        "Tahoma=tahoma,arial,helvetica,sans-serif;"+
				        "Times New Roman=times new roman,times;"+
				        "Trebuchet MS=trebuchet ms,geneva;"+
				        "Verdana=verdana,geneva;"
	});
});
</script>