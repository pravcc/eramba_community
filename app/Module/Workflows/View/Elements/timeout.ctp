<?php
if (!$Instance->hasRollback()) {
	echo $this->Ux->getAlert(__('Timeout is not configured.'), [
		'type' => 'info'
	]);

	return true;
}

echo $this->Ux->getAlert(__('After timeout expires the workflow triggers a rollback step automatically.'));

$percentage = $Instance->expiresPercentage();

$progress = [
	25 => 'info',
	50 => 'success',
	75 => 'warning',
	100 => 'danger',
];

$progressClass = reset($progress);
foreach ($progress as $number => $class) {
	if ($percentage <= $number) {
		$progressClass = $class;
		break;
	}
}
?>


<div class="progress progress-striped active"> 
	<div class="progress-bar progress-bar-<?php echo $progressClass; ?>" style="width:<?php echo $percentage; ?>%"></div> 
</div>
<?php
?>
<h3>Expires 
	<?php
	$expiresIn = $Instance->stageExpires();
	echo CakeTime::timeAgoInWords(strtotime("+{$expiresIn} hours"), array(
		'accuracy' => array('hour' => 'hour')
	));
	?>
	<small>(was <?php echo $Instance->getTimeout(); ?> hours)</small>
</h3>
