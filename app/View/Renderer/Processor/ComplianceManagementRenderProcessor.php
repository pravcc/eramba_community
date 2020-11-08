<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');

class ComplianceManagementRenderProcessor extends SectionRenderProcessor
{
    use RelatedProjectStatusesTrait;

    protected $_listMappingItems = null;

    public function mappings(OutputBuilder $output, $subject)
    {
        $MappingRelation = ClassRegistry::init('Mapping.ComplianceManagementMappingRelation');
        $primaryKey = $subject->item->getPrimary();

        $syncIds = $MappingRelation->getSyncData($primaryKey);

        // class local cache
        if ($this->_listMappingItems === null) {
            $this->_listMappingItems = $MappingRelation->getLeftObjects();
        }

        $newTab = $subject->view->Html->tag('span', '<i class="icon-new-tab2"></i>', [
            'class' => 'pull-right new-tab-link',
            'escape' => false,
        ]);

        $mappingSyncObjects = [];

        foreach ($syncIds as $id) {
            $output->itemTemplate([$id => $subject->view->Html->tag('span', OutputBuilder::CONTENT, ['class' => 'content-box', 'escape' => false])]);
            $output->label([$id => $this->_listMappingItems[$id]]);

            $output->itemAction([
                $id => [
                    'label' => __('Show') . $newTab,
                    'url' => $subject->view->AdvancedFilters->filterUrl('complianceManagements', ['id' => $id]),
                    'options' => [
                        'escape' => false
                    ]
                ]
            ]);
        }

        if (count($syncIds) > 1) {
            $output->cellAction([
                'label' => __('Show') . $newTab,
                'url' => $subject->view->AdvancedFilters->filterUrl('complianceManagements', ['id' => $syncIds]),
                'options' => [
                    'escape' => false
                ]
            ]);
        }
    }

	public function itemName(OutputBuilder $output, $subject)
    {
    	$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->name]);
    	}
    }

    public function itemDescription(OutputBuilder $output, $subject)
    {
    	$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->description]);
    	}
    }

    public function itemId(OutputBuilder $output, $subject)
    {
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->item_id]);
    	}
    }

    public function packageName(OutputBuilder $output, $subject)
    {
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->CompliancePackage->name]);
    	}
    }

    public function packageDescription(OutputBuilder $output, $subject)
    {
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->CompliancePackage->description]);
    	}
    }

    public function packageId(OutputBuilder $output, $subject)
    {
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
    	foreach ($FieldsIterator as $key => $value) {
    		$output->label([$key => $value['item']->CompliancePackageItem->CompliancePackage->package_id]);
    	}
    }

    public function compliancePackageRegulatorName(OutputBuilder $output, $subject)
    {
        $FieldsIterator = new FieldsIterator($subject->item, $subject->field);
        foreach ($FieldsIterator as $key => $value) {
            $output->label([$key => $value['item']->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator->name]);
        }
    }

    public function objectStatusSecurityPolicyExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Policies'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityPolicies', [
                'id' => $value['item']->SecurityPolicy->getPrimaryKeys(),
                'ObjectStatus_expired_reviews' => 1
            ]);
        });
    }

    public function objectStatusComplianceExceptionExpired(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Exceptions'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('complianceExceptions', [
                'id' => $value['item']->ComplianceException->getPrimaryKeys(),
                'ObjectStatus_expired' => 1
            ]);
        });
    }

    public function objectStatusSecurityServiceAuditsLastNotPassed(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Controls'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityServices', [
                'id' => $value['item']->SecurityService->getPrimaryKeys(),
                'ObjectStatus_audits_last_not_passed' => 1
            ]);
        });
    }

    public function objectStatusSecurityServiceAuditsLastMissing(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Controls'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityServices', [
                'id' => $value['item']->SecurityService->getPrimaryKeys(),
                'ObjectStatus_audits_last_missing' => 1
            ]);
        });
    }

    public function objectStatusSecurityServiceMaintenancesLastMissing(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Controls'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityServices', [
                'id' => $value['item']->SecurityService->getPrimaryKeys(),
                'ObjectStatus_maintenances_last_missing' => 1
            ]);
        });
    }

    public function objectStatusSecurityServiceMaintenancesLastNotPassed(OutputBuilder $output, $subject)
    {
        $this->_statusFilterAll($output, $subject, __('Show Controls'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityServices', [
                'id' => $value['item']->SecurityService->getPrimaryKeys(),
                'ObjectStatus_maintenances_last_not_passed' => 1
            ]);
        });
    }

    public function objectStatusComplianceAnalysisFindingExpired(OutputBuilder $output, $subject)
    {
        $this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('complianceAnalysisFindings', [
                'id' => $value['item']->ComplianceAnalysisFinding->getPrimaryKeys(),
                'ObjectStatus_expired' => 1
            ]);
        });
    }

    public function objectStatusSecurityServiceControlWithIssues(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Controls'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('securityServices', [
                'id' => $value['item']->SecurityService->getPrimaryKeys(),
                'ObjectStatus_control_with_issues' => 1
            ]);
        });
    }

    public function objectStatusRiskExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Asset Risks'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('risks', [
                'id' => $value['item']->Risk->getPrimaryKeys(),
                'ObjectStatus_expired_reviews' => 1
            ]);
        });
    }

    public function objectStatusThirdPartyRiskExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Third Party Risks'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('thirdPartyRisks', [
                'id' => $value['item']->ThirdPartyRisk->getPrimaryKeys(),
                'ObjectStatus_expired_reviews' => 1
            ]);
        });
    }

    public function objectStatusBusinessContinuityExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show Business Risks'), function ($subject, $value) {
            return $subject->view->AdvancedFilters->filterUrl('businessContinuities', [
                'id' => $value['item']->BusinessContinuity->getPrimaryKeys(),
                'ObjectStatus_expired_reviews' => 1
            ]);
        });
    }
}
