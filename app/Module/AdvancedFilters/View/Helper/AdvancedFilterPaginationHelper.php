<?php
App::uses('AppHelper', 'View/Helper');

class AdvancedFilterPaginationHelper extends AppHelper
{
	public $helpers = ['Html'];

	public function renderPageLink($filterId, $i, $idx, $active = false, $label = null)
	{
		if ($label == null) {
			$label = $i;
		}

		$class = 'paginate_button';
		if ($active) {
			$class .= ' current';
		}

		$options = [
			'class' => $class,
			'aria-controls' => 'datatable-' . $filterId,
			'data-dt-idx' => $idx,
			'tabindex' => '0'
		];

		if (!$active) {
			$options = array_merge($options, [
				'data-yjs-request' => 'index-filters/setPage/page::' . $i . '/id::' . $filterId,
				'data-yjs-target' => 'none',
				'data-yjs-event-on' => 'click',
				'data-yjs-use-loader' => 'false'
			]);
		}

		$link = $this->Html->tag('a', $i, $options);

		return $link;
	}
}