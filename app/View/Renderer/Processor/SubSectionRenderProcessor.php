<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class SubSectionRenderProcessor extends RenderProcessor
{	
	public function render(OutputBuilder $output, $subject)
	{
		list($plugin, $name) = pluginSplit($subject->childModel);
		$uniqueKey = $name . '_counter';

		$value = (isset($subject->value)) ? $subject->value : $subject->item->{$name}->count();
		$output->label([$uniqueKey => $value]);

		$Model = $subject->item->getModel();
		$SubModel = $Model->{$name};
		$mappedRoute = $SubModel->getMappedRoute();
		$assoc = $SubModel->getAssociated($Model->alias);

		if (isset($subject->showUrl)) {
			$showUrl = $subject->showUrl;
		}
		else {
			$showParams = [
				$assoc['foreignKey'] => $subject->item->getPrimary()
			];

			if ($SubModel->alias == 'AccountReviewFeedback' || $SubModel->alias == 'AccountReviewFinding') {
				$showParams = [
					'AccountReviewPull-hash' => $subject->item->hash
				];
			}
			elseif ($SubModel->alias == 'DataAsset') {
				$showParams = [
					'DataAssetInstance-asset_id' => $subject->item->asset_id
				];
			} 

			if ($SubModel->alias == 'CompliancePackage') {
				$showParams = [
					'CompliancePackage-compliance_package_regulator_id' => $showParams['compliance_package_regulator_id']
				];

				$mappedRoute['controller'] = 'compliancePackageItems';
				if ($subject->item->CompliancePackage->CompliancePackageItem !== null) {
					$output->label([$uniqueKey => $subject->item->CompliancePackage->CompliancePackageItem->count()]);
				} else {
					$output->label([$uniqueKey => 0]);
				}
			}

			$showUrl = $subject->view->AdvancedFilters->filterUrl($mappedRoute['controller'], $showParams, [
				'plugin' => $mappedRoute['plugin']
			]);
		}

		$output->itemAction([
			$uniqueKey => [
				'label' => __('Show'),
				'url' => $showUrl
			]
		]);

		$flowsDisabled = $SubModel->alias == 'DataAsset' && !$subject->item->isFlowsEnabled();
		if ($flowsDisabled) {
			$output->itemTemplate([
				$uniqueKey => $subject->view->Popovers->auto(OutputBuilder::CONTENT, __('Flows are not enabled'))
			]);
		}

		$skipAddNew = $SubModel->alias == 'SecurityIncidentStagesSecurityIncident';
		$skipAddNew = $skipAddNew || $SubModel->alias == 'AccountReviewPull';
		// $skipAddNew = $skipAddNew || $SubModel->alias == 'AccountReviewFinding';
		$skipAddNew = $skipAddNew || $SubModel->alias == 'AccountReviewFeedback';
		$skipAddNew = $skipAddNew || $SubModel->alias == 'CompliancePackage';
		$skipAddNew = $skipAddNew || $SubModel->alias == 'VendorAssessmentFeedback';
		$skipAddNew = $skipAddNew || $flowsDisabled;
		if (!$skipAddNew) {
			if (isset($subject->addUrl)) {
				$datasourceUrl = $subject->addUrl;
			}
			else {
				$datasourceUrl = $SubModel->getMappedRoute([
					'action' => 'add',
					$subject->item->getPrimary()
				]);
			}

			$output->itemAction([
				$uniqueKey => [
					'label' => __('Add New'),
					'url' => '#',
					'options' => [
						'data-yjs-request' => 'crud/showForm',
						'data-yjs-target' => 'modal',
						'data-yjs-event-on' => 'click',
						'data-yjs-on-modal-failure' => 'close',
						'data-yjs-datasource-url' => Router::url($datasourceUrl)
					]
				]
			]);
		}
	}

}