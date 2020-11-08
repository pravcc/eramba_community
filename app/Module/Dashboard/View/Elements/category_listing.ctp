<?php
if (empty($categories)) {
	echo '<div style="padding: 10px 20px">';
	echo $this->Alerts->info(__('We found no data for you to show.'));
	echo '</div>';
	return;
}
$i=0;foreach ($categories as $category => $items) :
	$primaryContent = true;
	if ($i > 0) {
		$primaryContent = false;
	}

	$class = 'widget-content';
	if (!$primaryContent) {
		$class .= ' widget-deeper';
	}

	// lets check if we got a custom element to display kpis for a category and type
	$checkElement = 'Dashboard.Categories/category_' . $type . '_' . $category;
	if ($this->elementExists($checkElement)) {
		$listingElement = $checkElement;
	}
	// this is default listing element
	else {
		$listingElement = 'Dashboard.list_items';
	}

	$widgetContent = $this->element($listingElement, [
		'category' => $category,
		'items' => $items,
		'model' => $model
	]);

	if (!$primaryContent) {
		echo '<div class="divider"></div>';
	}

	echo $this->Html->div($class, $widgetContent, [
		'escape' => false
	]);

$i++;endforeach;
?>