<?php
App::uses('AppHelper', 'View/Helper');

class TooltipsCrudHelper extends AppHelper
{
	public $helpers = array('Html', 'Form');

	public function setupTooltip($type = 'initial')
	{
		$Tooltips = $this->_View->get('Tooltips');

		$tooltipInit = '';
		if ($type === 'initial') {
			$tooltipInit = $this->Html->tag('div', '', [
				'data-yjs-request' => "crud/load",
				'data-yjs-datasource-url' => "get::tooltips/tooltips/tooltip/" . $this->_View->get('modelAlias'),
				'data-yjs-target' => "modal",
				'data-yjs-event-on' => "init"
			]);
		}

		return $tooltipInit;
	}
}
