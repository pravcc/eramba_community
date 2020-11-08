<?php
App::uses('AppController', 'Controller');
App::uses('ThirdPartyAuditsModule', 'ThirdPartyAudits.Lib');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');
App::uses('AccountReviewsModule', 'AccountReviews.Lib');

class AjaxController extends AppController
{
	public $helpers = array('Html', 'Form', 'NotificationObjects');
	public $components = array('Session', 'Ajax', 'RequestHandler', 'Paginator');

	protected $_appControllerConfig = [
		'components' => [
			'Ajax' => false
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		parent::beforeFilter();

		//allows action if session key for that authentication is set
		ThirdPartyAuditsModule::allowAction($this);
		VendorAssessmentsModule::allowAction($this);
		AccountReviewsModule::allowAction($this);
	}

	public function modalSidebarWidget($model, $foreign_key, $activeModule = 'comments') {
		$this->allowOnlyAjax();

		$this->loadModel($model);
		$itemTitle = $this->{$model}->getRecordTitle($foreign_key);
		if (empty($itemTitle)) {
			$itemTitle = __('Associated Data');
		}
		else {
			$itemTitle = __('Associated Data for "%s"', $itemTitle);
		}

		if (!empty($this->{$model}->mapping['notificationSystem'])) {
			$this->Ajax->settings['modules'][] = 'notifications';
		}

		$this->set('title_for_layout', $itemTitle);

		$this->Ajax->initSidebarWidget($foreign_key, $model);

		$this->set(array(
			'edit' => 1,
			'model' => $model,
			'id' => $foreign_key,
			'modalPadding' => true,
			'showHeader' => true,
			'modules' => $this->Ajax->settings['modules'],
			'activeModule' => $activeModule,
			'modalSidebarWidget' => true
		));

		if (!empty($this->request->query['enable_module'])) {
			$this->_enableSingleModule($this->request->query['enable_module']);
		}

		$this->render('../Elements/ajax-ui/sidebarWidget');
	}

	private function _enableSingleModule($additionalModules = []) {
		$modules = (array) $additionalModules;

		$enableComments = (in_array('comments', $modules)) ? true : false;
		$enableRecords = (in_array('records', $modules)) ? true : false;
		$enableAttachments = (in_array('attachments', $modules)) ? true : false;
		$enableNotifications = (in_array('notifications', $modules)) ? true : false;

		$this->set(compact('enableComments', 'enableRecords', 'enableAttachments', 'enableNotifications'));
	}
}