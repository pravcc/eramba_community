<?php
App::uses('TooltipBuilder', 'Module/Tooltips/Lib');
App::uses('TooltipInterface', 'Module/Tooltips/Lib');

class Tooltip1 extends TooltipBuilder implements TooltipInterface
{
	public function init()
	{
		$this->setTemplate('tooltip_tpl_1');
		$this->setHeader(__('Tooltip heading'));
		$this->addParagraph(__('Heading of first paragraph'), __('Example text of first paragraph'));
		$this->addParagraph(__('Heading of second paragraph'), __('Example text of second paragrapth.<br>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.'));
	}
}
