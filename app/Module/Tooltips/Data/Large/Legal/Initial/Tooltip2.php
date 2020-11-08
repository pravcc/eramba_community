<?php
App::uses('TooltipBuilder', 'Module/Tooltips/Lib');
App::uses('TooltipInterface', 'Module/Tooltips/Lib');

class Tooltip2 extends TooltipBuilder implements TooltipInterface
{
	public function init()
	{
		$this->setTemplate('tooltip_tpl_2');
		$this->setHeader(__('Tooltip heading'));
		$this->addParagraph(__('Heading of paragraph'), __('Text of paragraph'));
		$this->addImage('tooltip_img.jpg');
	}
}
