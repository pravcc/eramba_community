<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('Translation', 'Translations.Model');
App::uses('CakeEvent', 'Event');

class SectionCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar', 'LimitlessTheme.PageToolbar', 'LimitlessTheme.ItemDropdown', 'LimitlessTheme.Labels', 'AclCheck'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
            'PageToolbar.beforeRender' => ['callable' => 'beforePageToolbarRender', 'priority' => 50],
            'ItemDropdown.beforeRender' => ['callable' => 'itemDropdownBeforeRender', 'priority' => 10],
        ];
    }

	public function beforeRender($viewFile)
	{
		$this->Section = $this->_View->get('Section');
	}

	public function beforePageToolbarRender(CakeEvent $event)
	{
		$this->_handlePageToolbar();
	}

	public function beforeLayoutToolbarRender(CakeEvent $event)
	{
		$this->Section = $this->_View->get('Section');

		$this->_handleLayoutToolbar();
	}

	public function itemDropdownBeforeRender(CakeEvent $event)
    {
    	$Item = $event->data;
		$Model = $Item->getModel();
		$id = $Item->getPrimary();

		$Trash = $event->subject->view->get('Trash');
		$portalAccess = $event->subject->view->get('portalAccess');
		$logged = $event->subject->view->get('logged');

		// edit
		$edit = true;

		$notEditable = [
			'AssetMediaType',
			'SecurityPolicyDocumentType',
			'DataAssetInstance',
			'VendorAssessmentFeedback',
			'VendorAssessmentSystemLog',
			'AccountReviewPullSystemLog',
			'AccountReviewPull',
			'AccountReviewFeedback',
			'Cron',
			'AwarenessProgramActiveUser',
			'AwarenessProgramIgnoredUser',
			'AwarenessProgramCompliantUser',
			'AwarenessProgramNotCompliantUser',
			'AwarenessReminder',
			'Queue',
			'UserSystemLog',
			'LdapSynchronizationSystemLog'
		];

		if ((!empty($Trash) && $Trash->isTrash())
			|| in_array($Model->alias, $notEditable)
			|| ($Model->alias == 'VendorAssessmentFinding' && !$Item->VendorAssessment->isAuditor($logged) && !empty($portalAccess))
			|| ($Model->alias == 'Translation' && $Item->getPrimary() == Translation::DEFAULT_TRANSLATION_ID)
			|| ($Model->alias == 'User' && $Item->ldap_sync)
		) {
			$edit = false;
		}

		if ($edit) {
			$this->ItemDropdown->addItem(__('Edit'), '#', [
				'icon' => 'pencil',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-datasource-url' => Router::url([
					'action' => 'edit',
					$id
				]),
				'data-yjs-event-on' => 'click',
				'data-yjs-on-modal-failure' => 'close',
			]);
		}

		// history
		$history = true;

		$notAuditable = [
			'AssetMediaType',
			'SecurityPolicyDocumentType',
			'DataAssetInstance',
			'VendorAssessmentFeedback',
			'VendorAssessmentSystemLog',
			'AccountReviewPullSystemLog',
			'AccountReviewPull',
			'AccountReviewFeedback',
			'Cron',
			'AwarenessProgramActiveUser',
			'AwarenessProgramIgnoredUser',
			'AwarenessProgramCompliantUser',
			'AwarenessProgramNotCompliantUser',
			'AwarenessReminder',
			'Queue',
			'UserSystemLog',
			'LdapSynchronizationSystemLog',
			'User',
			'Queue',
			'Translation',
		];

		$isTrash = !empty($Trash) && $Trash->isTrash();

		if (!$isTrash && (
			in_array($Model->alias, $notAuditable)
			|| $portalAccess
			|| ($Model->alias == 'VendorAssessmentFinding' && !$Item->VendorAssessment->isAuditor($logged))
		)) {
			$history = false;
		}

		if ($history) {
			$this->ItemDropdown->addItem(__('History'), '#', [
				'icon' => 'history',
				'data-yjs-request' => 'crud/load',
				'data-yjs-target' => 'modal',
				'data-yjs-datasource-url' => Router::url([
					'action' => 'history',
					$id
				]),
				'data-yjs-event-on' => 'click',
				'data-yjs-modal-size-width' => 80,
			]);
		}

		// delete
		$delete = true;

		$notDeletable = [
			'AssetMediaType',
			'SecurityPolicyDocumentType',
			'DataAssetInstance',
			'VendorAssessmentFeedback',
			'VendorAssessmentSystemLog',
			'AccountReviewPullSystemLog',
			'AccountReviewPull',
			'AccountReviewFeedback',
			'Cron',
			'AwarenessProgramActiveUser',
			'AwarenessProgramIgnoredUser',
			'AwarenessProgramCompliantUser',
			'AwarenessProgramNotCompliantUser',
			'AwarenessReminder',
			'UserSystemLog',
			'LdapSynchronizationSystemLog',
			'ComplianceManagement'
		];

		if ((!empty($Trash) && $Trash->isTrash())
			|| in_array($Model->alias, $notDeletable)
			|| ($Model->alias == 'VendorAssessmentFinding' && !$Item->VendorAssessment->isAuditor($logged) && !empty($portalAccess))
			|| ($Model->alias == 'Translation' && $Item->type == Translation::TYPE_SYSTEM)
			|| ($Model->alias == 'User' && ($Item->ldap_sync || $Item->id == $logged['id'] || $Item->id == ADMIN_ID))
		) {
			$delete = false;
		}

		if ($delete) {
			$this->ItemDropdown->addItem(__('Delete'), '#', [
				'icon' => 'trash',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-datasource-url' => Router::url([
					'action' => 'delete',
					$id
				]),
				'data-yjs-event-on' => 'click',
				'data-yjs-on-modal-failure' => 'close',
			]);
		}
    }

	/**
	 * Handle buttons inside page toolbar on the top which includes main sections and its subsections.
	 * 
	 * @return void
	 */
	protected function _handlePageToolbar()
	{
		$sections = $this->Section->getSections();
		$currentModel = $this->Section->getSubject()->model->alias;

		// special case to not show any header sections for compliance analysis at all
		if ($currentModel == 'ComplianceManagement') {
			return;
		}

		if (in_array($currentModel, [
			'VendorAssessment',
			'VendorAssessmentFeedback',
			'VendorAssessmentFinding',
			'VendorAssessmentSystemLog'
		])) {
			$sections = [
				'VendorAssessment',
				'VendorAssessmentFeedback',
				'VendorAssessmentFinding',
				'VendorAssessmentSystemLog'
			];
		}
		elseif (in_array($currentModel, [
			'AccountReview',
			'AccountReviewPull',
			'AccountReviewFeedback',
			'AccountReviewFinding',
			'AccountReviewPullSystemLog'
		])) {
			$sections = [
				'AccountReview',
				'AccountReviewPull',
				'AccountReviewFeedback',
				'AccountReviewFinding',
				'AccountReviewPullSystemLog'
			];
		}
		elseif (in_array($currentModel, [
			'User',
			'UserSystemLog',
			'LdapSynchronizationSystemLog'
		])) {
			$sections = [
				'User',
				'UserSystemLog',
				'LdapSynchronizationSystemLog'
			];
		}

		// if there is more than 1 section available within current request
		if (count($sections) > 1) {
			foreach ($sections as $model) {
				$SectionModel = ClassRegistry::init($model);

				$liOptions = [];
				if ($currentModel == $model) {
					$liOptions['class'] = 'active';
				}

				$url = null;
				if (method_exists($SectionModel, 'getMappedRoute')) {
					$url = $SectionModel->getMappedRoute();
				}
				else {
					$url = [
						'plugin' => null,
						'controller' => $SectionModel->getMappedController(),
						'action' => 'index'
					];
				}

				$section = [
					$SectionModel->label(),
					$url,
					[
						'icon' => 'stack3',
						'li' => $liOptions
					]
				];

				$this->PageToolbar->addItem(...$section);
			}
		}

		// special case hardcoded for Community handling of Mapping module
		if (!AppModule::loaded('Mapping') && in_array($currentModel, ['CompliancePackageRegulator', 'CompliancePackageItem'])) {
			$url = 'https://www.eramba.org/services';

			$options = [
				'icon' => 'stack3',
				'target' => '_blank',
				'li' => [
					'class' => 'page-toolbar-enterprise-item'
				]
			];

			$this->PageToolbar->addItem(__('Compliance Mappings') . $this->Labels->danger('Enterprise'), $url, $options);
		}
	}

	public function setAddAction()
	{
		$addAction = [__('Add'), '#', [
			'icon' => 'plus2',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-on-modal-failure' => 'close',
			'data-yjs-datasource-url' =>  Router::url([
				'action' => 'add'
			]),
		]];

		$this->LayoutToolbar->addItem(__('Actions'), '#', [/*'icon' => 'power2'*/], [
			$addAction
		]);
	}

	protected function _handleLayoutToolbar()
	{
		$Section = $this->_View->get('Section');
		$currentModel = $this->Section->getSubject()->model->alias;

		$skipAddActionModels = [
			// 'SecurityServiceAudit',
			// 'SecurityServiceMaintenance',
			// 'AssetReview',
			// 'RiskReview',
			// 'SecurityPolicyReview',
			// 'ThirdPartyReview',
			// 'BusinessContinuityReview',
			'Issue',
			// 'SecurityServiceIssue',
			// 'Process',
			'Report',
			'VendorAssessmentFeedback',
			'VendorAssessmentFinding',
			'ComplianceManagement',
			'AwarenessProgramActiveUser',
			'AwarenessProgramIgnoredUser',
			'AwarenessProgramCompliantUser',
			'AwarenessProgramNotCompliantUser',
			'AwarenessReminder',
			// 'AwarenessTraining',
			'AccountReviewFinding',
			'AccountReviewFeedback',
			'AccountReviewPull',
			'AccountReviewPullSystemLog',
			'Queue',
			// 'DataAsset',
			'DataAssetInstance',
			// 'ProjectAchievement',
			// 'ProjectExpense',
			'Cron',
			// 'GoalAudit',
			'SecurityIncidentStagesSecurityIncident',
			'DashboardKpi',
			'UserSystemLog',
			'DashboardCalendarEvent',
			'LdapSynchronizationSystemLog',
			'VisualisationSetting'
		];

		// this will probably end up just with a condition if a section implements InheritanceInterface
		if (!in_array($currentModel, $skipAddActionModels)) {
			$this->setAddAction();
		}
	}
}
