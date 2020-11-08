<?php
App::uses('AppHelper', 'View/Helper');

class AdvancedFilterRendererHelper extends AppHelper
{
	public $helpers = ['ObjectRenderer.ObjectRenderer', 'Html'];
	
	public function renderCell($Item, $FilterField)
	{
		$content = $this->getContent($Item, $FilterField);
		$plainContent = $this->getPlainContent($Item, $FilterField);

		$contentWrapper = $this->Html->tag('div', $content, [
			'class' => 'datatable-cell-content-wrapper'
		]);
		return $this->Html->tag('td', $contentWrapper, [
			'class' => ['field-cell', 'exportable-cell'],
			'data-order' => $plainContent,
			'data-search' => $plainContent,
			'data-e-column-slug' => $FilterField->getFieldName()
		]);
	}

	public function getPlainContent($Item, $FilterField)
	{
		$TraverserData = traverser($Item, $FilterField);

		$plainContent = '';

		if (!empty($TraverserData['ItemDataEntity'])) {
			$params = [
				'item' => $TraverserData['ItemDataEntity'],
				'field' => $TraverserData['FieldDataEntity'],
			];

			$plainOutput = $this->ObjectRenderer->getOutput('AdvancedFilters.Cell', $params, [
				'Text',
				'RiskScore',
				'CustomFields.CustomFields',
				$this->ObjectRenderer->getSectionProcessor($TraverserData['ItemDataEntity']->getModel())
			]);
			$plainOutput->setRenderScope(['text']);
			$plainContent = $plainOutput->render();

			//remove html tags
			$plainContent = preg_replace('/<[^>]+>/', '', $plainContent);
		}

		return $plainContent;
	}

	public function getContent($Item, $FilterField)
	{
		if (isset($this->_View->viewVars['Trash'])) {
			$Trash = $this->_View->viewVars['Trash'];
		}

		$TraverserData = traverser($Item, $FilterField);

		$content = '';

		if (!empty($TraverserData['ItemDataEntity'])) {
			$inlineEditToggle = false;

			if ($Item->getModel()->alias == 'CompliancePackageItem' && $TraverserData['FieldDataEntity']->getModelName() == 'CompliancePackage') {
				$inlineEditToggle = true;
			}

			$trashCond = (isset($Trash) && !$Trash->isTrash()) || !isset($Trash);
			if ($Item->getModel()->alias == $TraverserData['ItemDataEntity']->getModel()->alias && $trashCond) {
				$inlineEditToggle = true;
			}

			$processors = [
				'Default',
				'InlineEdit.InlineEdit' => $inlineEditToggle,
				'AdvancedFilters.FilterItem',
				'ObjectStatus.AssociatedObjectStatus',
				'Utils.SoftDelete',
				'CustomFields.CustomFields',
				'RiskScore',
				$this->ObjectRenderer->getSectionProcessor($TraverserData['ItemDataEntity']->getModel())
			];

			if ($Item->getModel()->alias == 'SecurityPolicyReview') {
				$processors[] = 'SecurityPolicyReview';
			}
			
			$params = [
				'item' => $TraverserData['ItemDataEntity'],
				'field' => $TraverserData['FieldDataEntity'],
			];

			$content = $this->ObjectRenderer->render('AdvancedFilters.Cell', $params, $processors);
		}
	
		return $content;
	}

}