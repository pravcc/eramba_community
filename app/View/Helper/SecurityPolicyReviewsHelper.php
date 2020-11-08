<?php
App::uses('AppHelper', 'View/Helper');
App::uses('SecurityPoliciesHelper', 'View/Helper');

class SecurityPolicyReviewsHelper extends AppHelper {
	public $helpers = array('NotificationSystem', 'Html', 'FieldData.FieldData', 'Reviews', 'Limitless.Alerts', 'SecurityPolicies', 'Attachments.Attachments');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function attachmentField(FieldDataEntity $Field)
	{
		$attachmentHash = $this->_View->get('attachmentHash');

		$content = '';

		if ($attachmentHash !== null) {
			$content = $this->Attachments->attachmentTmpField($Field);
		}

		return $this->Html->div('', $content, [
			'id' => 'attachments-wrapper'
		]);
	}

	public function documentContentField($Field)
	{
		$reviewCompleted = $this->_View->get('reviewCompleted');
		$options = [
			'disabled' => false
		];
		if ($reviewCompleted) {
			$options['disabled'] = true;
		}

		return $this->SecurityPolicies->documentContentField($Field, $options);

		/*$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$SecurityPolicyReview = ClassRegistry::init('SecurityPolicyReview');

		$DescriptionField = $SecurityPolicy->getFieldDataEntity('description');
		$UrlField = $SecurityPolicy->getFieldDataEntity('url');
		$attachmentField = $SecurityPolicyReview->getFieldDataEntity('attachment');

		$DescriptionField->toggleEditable(true);
		$UrlField->toggleEditable(true);
		$attachmentField->toggleEditable(true);

		$inputs = null;

		$options1 = [
			'inputName' => 'SecurityPolicy.use_attachments',
			'div' => [
				'id' => 'use-attachments'
			]
		];

		$options2 = [
			'div' => [
				'id' => 'tinymce-wrapper'
			]
		];

		$options3 = [
			'div' => [
				'id' => 'url-wrapper'
			]
		];

		$reviewCompleted = $this->_View->get('reviewCompleted');
		$options1 = $options2 = $options3 = [
			'disabled' => false
		];
		if ($reviewCompleted) {
			$options1['disabled'] = true;
			$options2['disabled'] = true;
			$options3['disabled'] = true;
		}

		// $options1 = array_merge($options1, $customOptions);
		// $options2 = array_merge($options2, $customOptions);
		// $options3 = array_merge($options3, $customOptions);

		// $inputs .= $this->FieldData->input($Field, $options1);
		$inputs .= $this->FieldData->input($DescriptionField, $options2);
		$inputs .= $this->FieldData->input($UrlField, $options3);
		$inputs .= $this->FieldData->input($attachmentField);
		$inputs .= $this->_View->element('../SecurityPolicies/add_script');

		$DescriptionField->toggleEditable(false);
		$UrlField->toggleEditable(false);
		
		return $inputs;

		$edit = $this->_View->get('edit');
		$prevReview = $this->_View->get('prevReview');
		$mainItem = $this->_View->get('mainItem');

		
		
		if (!empty($edit)) {
			$versionVal = (!empty($this->request->data['SecurityPolicy']['version'])) ? $this->request->data['SecurityPolicy']['version'] : $this->request->data['SecurityPolicyReview']['version'];
		}
		else {
			$versionVal = (!empty($prevReview)) ? $prevReview['SecurityPolicyReview']['version'] : $mainItem['SecurityPolicy']['version'];
		}

		$out = $this->FieldData->input($Field, [
			'default' => $versionVal,
			'inputName' => 'SecurityPolicy.' . $Field->getFieldName(),
			'readonly' => $this->Reviews->isFieldDisabled()
		]);

		if (!empty($prevReview)) {
			$out .= $this->Alerts->info(__('The last version for this document is %s', $prevReview['SecurityPolicyReview']['version']));
		}

		return $out;*/
	}

	public function versionField($Field)
	{
		$edit = $this->_View->get('edit');
		$prevReview = $this->_View->get('prevReview');
		$mainItem = $this->_View->get('mainItem');

		if (!empty($edit)) {
			$versionVal = (!empty($this->request->data['SecurityPolicy']['version'])) ? $this->request->data['SecurityPolicy']['version'] : $this->request->data['SecurityPolicyReview']['version'];
		}
		else {
			$versionVal = (!empty($prevReview)) ? $prevReview['SecurityPolicyReview']['version'] : $mainItem['SecurityPolicy']['version'];
		}

		$out = $this->FieldData->input($Field, [
			'default' => $versionVal,
			'inputName' => 'SecurityPolicy.' . $Field->getFieldName(),
			'readonly' => $this->Reviews->isFieldDisabled()
		]);

		if (!empty($prevReview)) {
			$out .= $this->Alerts->info(__('The last version for this document is %s', $prevReview['SecurityPolicyReview']['version']));
		}

		return $out;
	}

	public function urlField($Field)
	{
		return $this->FieldData->input($Field, [
			'inputName' => 'SecurityPolicy.' . $Field->getFieldName(),
			'readonly' => $this->Reviews->isFieldDisabled()
		]);
	}

	public function descriptionField($Field)
	{
		return $this->FieldData->input($Field, [
			'inputName' => 'SecurityPolicy.' . $Field->getFieldName(),
			'readonly' => $this->Reviews->isFieldDisabled()
		]);
	}

	public function getStatuses($item) {
		$statuses = array();

		$item = $this->processItemArray($item, 'SecurityPolicyReview');

		$statuses = array_merge($statuses, $this->NotificationSystem->getStatuses($item));

		return $this->processStatuses($statuses);
	}

	public static function complianceItemsList($Item)
	{
		return SecurityPoliciesHelper::complianceItemsList($Item->SecurityPolicy);
	}

	public static function riskItemsList($Item)
	{
		return SecurityPoliciesHelper::riskItemsList($Item->SecurityPolicy);
	}

	public static function dataAssetItemsList($Item)
	{
		return SecurityPoliciesHelper::dataAssetItemsList($Item->SecurityPolicy);
	}
}