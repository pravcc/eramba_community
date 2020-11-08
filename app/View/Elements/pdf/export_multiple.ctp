<?php
if (!empty($items)) {
	foreach ($items as $item) {
		echo $this->element($singleElementToRender, array(
			'item' => $item
		));

		echo $this->Html->tag('pagebreak');
	}
}
else {
	echo $this->Ux->getAlert(__('No items were found.'), array(
		'type' => 'info'
	));
}
?>