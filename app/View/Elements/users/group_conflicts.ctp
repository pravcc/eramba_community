<?php
$alert = false;
if (!empty($conflicts)) {
	$conflictText = '<br />';
	$conflictText .= implode('<br />', $conflicts);
	$message = __('<strong>Groups that you have selected have conflicting permissions.<br />The following actions are allowed in some groups and in others denied.<br />By default we will allow these actions:</strong> %s', $conflictText);

	$alert = $this->Ux->getAlert($message, [
		'type' => 'danger',
		'htmlentities' => false
	]);
}
?>

<?php if ($alert !== false) : ?>
<div class="form-group form-group-first">
	<div class="col-md-10 col-md-offset-2" id="group-conflicts">
		<?= $alert ?>
	</div>
</div>
<?php endif; ?>