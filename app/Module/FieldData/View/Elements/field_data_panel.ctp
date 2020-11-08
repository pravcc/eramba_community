<?php if (!$fieldDataEnabled) : ?>
	<h2><?= __d('field_data', 'FieldData Disabled'); ?></h2>
	
	<?php return; ?>
<?php endif; ?>

<h2><?= __d('field_data', 'FieldDataCollection List (%d)', count($fieldList)); ?></h2>
<?php
echo $this->Toolbar->makeNeatArray($fieldList);
?>
<h2><?= __d('field_data', 'FieldData Form Variables'); ?></h2>
<?php
echo $this->Toolbar->makeNeatArray($formVars);
?>

<div class="debug-info">
	<h2><?php echo __d('field_data', 'Memory'); ?></h2>
	<div class="peak-mem-use">
	<?php
		echo $this->Toolbar->message(__d('field_data', 'Total Memory Use'), $this->Number->toReadableSize($debug['totalMemory'])); ?>
	</div>

	<?php
	$points = $debug;
	unset($points['totalMemory']);

	$headers = array(__d('field_data', 'Message'), __d('field_data', 'Memory use'));

	$rows = array();
	foreach ($points as $key => $value):
		$rows[] = array($key, $this->Number->toReadableSize($value['memory']));
	endforeach;

	echo $this->Toolbar->table($rows, $headers);
	?>
</div>