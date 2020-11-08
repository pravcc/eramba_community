<?php
App::uses('TooltipBuilder', 'Module/Tooltips/Lib');
App::uses('TooltipInterface', 'Module/Tooltips/Lib');

class TooltipLocal1 extends TooltipBuilder implements TooltipInterface
{
	public function init()
	{
		$this->setHeader(__('Small Tooltip Heading'));
		$this->addParagraph(__('Heading of first paragraph'), __('Example text of first paragraph'));
		$this->setShowMoreBtn();
	}
}
