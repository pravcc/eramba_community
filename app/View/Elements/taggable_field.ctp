<?php
if (!isset($model)) {
	trigger_error(__('Variable "model" should be defined!'));
	$model = $this->Form->model();
}

$labels = null;
if (isset($this->request->data['Tag'][0])) {
	$labelsArr = array();
	foreach ($this->request->data['Tag'] as $item) {
		$labelsArr[] = $item['title'];
	}

	$labels = implode(',', $labelsArr);
}

if (!empty($labels)) {
	$this->request->data[$model]['Tag'] = $labels;
}

if (!isset($placeholder)) {
	$placeholder = __('Add a tag');
}

echo $this->Form->input($model . '.Tag', array(
	'type' => 'hidden',
	'label' => false,
	'div' => false,
	'class' => 'taggable-tags col-md-12',
	'multiple' => true,
	'data-placeholder' => $placeholder
));
?>

<script type="text/javascript">
jQuery(function($) {
	<?php if (isset($tags) && !empty($tags)) : ?>
		var obj = $.parseJSON('<?php echo $this->Eramba->jsonEncode(array_values($tags)); ?>');
	<?php else : ?>
		var obj = $.parseJSON('<?php echo $this->Eramba->jsonEncode(array()); ?>');
	<?php endif; ?>

	$('.taggable-tags').select2({
		tags: obj
	});
})
</script>