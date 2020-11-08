<?php
/**
 * @deprecated  For the new logic use taggable_field and Taggable behavior.
 */

$labels = null;
if (isset($this->request->data['Tag'][0])) {
	$labelsArr = array();
	foreach ($this->request->data['Tag'] as $item) {
		$labelsArr[] = $item['title'];
	}

	$labels = implode(',', $labelsArr);
}

if (!empty($labels)) {
	$this->request->data['Tag']['tags'] = $labels;
}

if (!isset($placeholder)) {
	$placeholder = __('Add a tag');
}

echo $this->Form->input('Tag.tags', array(
	'type' => 'hidden',
	'label' => false,
	'div' => false,
	'class' => 'tags-tags col-md-12 full-width-fix',
	'multiple' => true,
	'data-placeholder' => $placeholder
));
?>

<script type="text/javascript">
jQuery(function($) {
	<?php if (isset($tags) && !empty($tags)) : ?>
		var obj = $.parseJSON('<?php echo $this->Eramba->jsonEncode($tags); ?>');
	<?php else : ?>
		var obj = $.parseJSON('<?php echo $this->Eramba->jsonEncode(array()); ?>');
	<?php endif; ?>

	$('.tags-tags').select2({
		tags: obj
	});
})
</script>