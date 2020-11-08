<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('Review', 'Model');
App::uses('CakeText', 'Utility');
App::uses('Attachment', 'Attachments.Model');

/**
 * ReviewsPlannerListener
 */
class SecurityPolicyReviewsPlannerListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			// 'Crud.startup' => array('callable' => 'startup', 'priority' => 50),
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 49),
			'Crud.afterSave' => array('callable' => 'afterSave', 'priority' => 50)
		);
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$args = $e->subject->controller->listArgs();

		$action = $e->subject->crud->action();
		if ($action instanceof AddCrudAction) {
			$this->_configureFieldData($e);
			$this->_setAttachmentData();
		}

		if ($action instanceof EditCrudAction) {
			$this->_configureFieldData($e);
			$this->_setAttachmentData();
		}
	}

	protected function _setAttachmentData()
	{
		$request = $this->_request();
		$controller = $this->_controller();

		if ($request->is('get') || $controller->Session->read('SecurityPolicyReview.Attachment.hash') === null) {
			$controller->Session->write('SecurityPolicyReview.Attachment.hash', CakeText::uuid());
		}

		$attachmentHash = $controller->Session->read('SecurityPolicyReview.Attachment.hash');
		$controller->set('attachmentHash', $attachmentHash);
	}

	protected function _configureFieldData(CakeEvent $e)
	{
		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$SecurityPolicyReview = ClassRegistry::init('SecurityPolicyReview');

		$version = $SecurityPolicy->getFieldDataEntity('version');
		$config = $version->config();
		$config['description'] = __('Enter the document new version.');
		$config['editable'] = true;
		$config['group'] = 'security-policy';
		$config['renderHelper'] = ['SecurityPolicyReviews', 'versionField'];
		$version = new FieldDataEntity($config, $SecurityPolicyReview);

		$nextReviewDate = $SecurityPolicy->getFieldDataEntity('next_review_date');
		$config = $nextReviewDate->config();
		$config['description'] = __('Enter the date in which this document should be reviewed again. Based on this date you enter here another row will be included on the system for review (you can later remove them if needed).');
		$config['editable'] = true;
		$config['group'] = 'security-policy';
		$config['renderHelper'] = ['Reviews', 'nextReviewDateField'];
		$nextReviewDate = new FieldDataEntity($config, $SecurityPolicyReview);

		$documentContent = $SecurityPolicy->getFieldDataEntity('use_attachments');
		$config = $documentContent->config();
		// $config['editable'] = true;
		$config['group'] = 'security-policy-content';
		$config['renderHelper'] = ['SecurityPolicyReviews', 'documentContentField'];
		// $config['default'] = $mainItem['SecurityPolicy']['url'];
		// $config['renderHelper'] = ['SecurityPolicyReviews', 'urlField'];
		$customField = new FieldDataEntity($config, $SecurityPolicyReview);

		$mainItem = $e->subject->controller->_getMainItem();
		// if ($mainItem['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_URL) {
			

		// 	$url = $SecurityPolicy->getFieldDataEntity('url');
		// 	$config = $url->config();
		// 	$config['editable'] = true;
		// 	$config['group'] = 'security-policy-content';
		// 	$config['default'] = $mainItem['SecurityPolicy']['url'];
		// 	$config['renderHelper'] = ['SecurityPolicyReviews', 'urlField'];
		// 	$customField = new FieldDataEntity($config, $SecurityPolicyReview);
		// }

		// if ($mainItem['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_CONTENT) {
		// 	$description = $SecurityPolicy->getFieldDataEntity('description');
		// 	$config = $description->config();
		// 	$config['description'] = __('Update the description of the Policy');
		// 	$config['editable'] = true;
		// 	$config['group'] = 'security-policy-content';
		// 	$config['default'] = $mainItem['SecurityPolicy']['description'];
		// 	$config['renderHelper'] = ['SecurityPolicyReviews', 'descriptionField'];
		// 	$config['_field'] = 'review_description';

		// 	$customField = new FieldDataEntity($config, $SecurityPolicyReview);
		// }

		$ReviewsCollection = &$this->_controller()->_FieldDataCollection;

		// we build a customized collection of fields
		// based on a type of notification being created
		// $_Collection = new FieldDataCollection([], $e->subject->model);

		$action = $e->subject->crud->action();

		if ($action instanceof AddCrudAction) {
			$ReviewsCollection->get('planned_date')->toggleEditable(true);
		}

		if ($action instanceof AddCrudAction || $action instanceof EditCrudAction) {
			$ReviewsCollection->add($version);
			$ReviewsCollection->add($nextReviewDate);

			if (isset($customField)) {
				$ReviewsCollection->add($customField);
			}
		}
	}

	public function afterSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$action = $subject->crud->action();

		if ($e->subject->success) {
			if ($action instanceof AddCrudAction || $action instanceof EditCrudAction)
			{
				$this->_handleReviewAttachments($e);
			}
		}
	}

	protected function _handleReviewAttachments(CakeEvent $e)
	{
		$request = $this->_request();
		$controller = $this->_controller();
		$model = $this->_model();

		if ($controller->Session->read('SecurityPolicyReview.Attachment.hash') !== null) {
			$Attachment = ClassRegistry::init('Attachments.Attachment');
			// ddd($Attachment);
			$Attachment->tmpToNormal(
				$controller->Session->read('SecurityPolicyReview.Attachment.hash'), 
				'SecurityPolicyReview', 
				$e->subject->id
			);

			// clone attachments to relevant policy
			$model->bindAttachments();
			$data = $model->find('first', [
				'conditions' => [
					'SecurityPolicyReview.id' => $e->subject->id
				],
				'contain' => [
					'Attachment'
				]
			]);

			if (!empty($data['Attachment'])) {
				foreach ($data['Attachment'] as $attachment) {
					$attachment['model'] = 'SecurityPolicy';
					$attachment['type'] = Attachment::TYPE_NORMAL;
					$foreignKey = $data['SecurityPolicyReview']['foreign_key'];

					$model->Attachment->cloneAttachment($attachment, $foreignKey);
				}
			}

		}

		$controller->Session->delete('SecurityPolicyReview.Attachment.hash');
	}

}