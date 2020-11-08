<?php
App::uses('TooltipBuilder', 'Module/Tooltips/Lib');
App::uses('TooltipInterface', 'Module/Tooltips/Lib');

class Tooltip3 extends TooltipBuilder implements TooltipInterface
{
	public function init()
	{
		$this->setTemplate('tooltip_tpl_3');
		$this->setHeader(__('Tooltip heading'));
		$this->addParagraph(__('Heading of paragraph'), __('Text of paragraph'));
		$this->addVideo('default.mp4', 'mp4');
	}
}
