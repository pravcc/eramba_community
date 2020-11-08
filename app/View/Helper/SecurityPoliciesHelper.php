<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('Review', 'Model');
App::uses('Attachment', 'Attachments.Model');
App::uses('SecurityServicesHelper', 'View/Helper');

class SecurityPoliciesHelper extends ErambaHelper {
	public $helpers = array('NotificationSystem', 'Html', 'Policy', 'FieldData.FieldData', 'Attachments.Attachments');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function permissionField(FieldDataEntity $Field)
	{
		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$LdapConnectorField = $SecurityPolicy->getFieldDataEntity('ldap_connector_id');
		// $DescriptionField->toggleEditable(true);

		$out = $this->FieldData->input($Field, [
			'id' => 'permission'
		]);

		$out .= $this->FieldData->input($LdapConnectorField, [
			'div' => [
				'id' => 'ldap-connector-select-wrapper'
			],
			'id' => 'ldap-connector-select'
		]);

		$out .= '<div id="ldap-group-select"><div id="ldap-group-wrapper"></div></div>';

		$out .= $this->_View->element('../SecurityPolicies/ldap_connector_script');

		return $out;
	}

	public function disabledFields(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'disabled' => $this->_View->get('disabledReviewFields')
		]);
	}

	public function versionField(FieldDataEntity $Field)
	{
		$edit = $this->_View->get('edit');

		$options = [];
		if ($edit === true) {
			$options['disabled'] = true;
		}

		return $this->FieldData->input($Field, $options);
	}

	public function documentContentField(FieldDataEntity $Field, $customOptions = [])
	{
		$edit = $this->_View->get('edit');

		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$DescriptionField = $SecurityPolicy->getFieldDataEntity('description');
		$UrlField = $SecurityPolicy->getFieldDataEntity('url');
		$attachmentField = $SecurityPolicy->getFieldDataEntity('attachment');

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
		if ($edit === true) {
			$options1['disabled'] = true;
			$options2['disabled'] = true;
			$options3['disabled'] = true;
		}

		$options1 = array_merge($options1, $customOptions);
		$options2 = array_merge($options2, $customOptions);
		$options3 = array_merge($options3, $customOptions);

		$inputs .= $this->FieldData->input($Field, $options1);
		$inputs .= $this->FieldData->input($DescriptionField, $options2);
		$inputs .= $this->FieldData->input($UrlField, $options3);
		$inputs .= $this->FieldData->input($attachmentField);
		$inputs .= $this->_View->element('../SecurityPolicies/add_script');

		$DescriptionField->toggleEditable(false);
		$UrlField->toggleEditable(false);
		$attachmentField->toggleEditable(false);
		
		return $inputs;
	}

	public function attachmentField(FieldDataEntity $Field)
	{
		$currentModel = $this->_View->get('currentModel');
		$reviewCompleted = $this->_View->get('reviewCompleted');

		if ($currentModel == 'SecurityPolicyReview' && $reviewCompleted) {
			return false;
		}
		
		$attachmentHash = $this->_View->get('attachmentHash');

		$content = '';

		if ($attachmentHash !== null) {
			$content = $this->Attachments->attachmentTmpField($Field);
		}

		return $this->Html->div('', $content, [
			'id' => 'attachments-wrapper'
		]);
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'SecurityPolicy');
		$statuses = array();

		if ($this->getAllowCond($allow, 'status') && $item['SecurityPolicy']['status'] == SECURITY_POLICY_DRAFT) {
			$statuses[$this->getStatusKey('status')] = array(
				'label' => __('Draft'),
				'type' => 'danger'
			);
		}

		if ($this->getAllowCond($allow, 'expired_reviews') && $item['SecurityPolicy']['expired_reviews'] == RISK_EXPIRED_REVIEWS) {
			$statuses[$this->getStatusKey('expired_reviews')] = array(
				'label' => __('Missing Reviews'),
				'type' => 'warning'
			);
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);

		return $this->styleStatuses($statuses, $options);
	}

	public function documentLink($policy, $options = array()) {
		$options = am(array(
			'title' => '<i class="icon-info-sign"></i>',
			'tooltip' => __('View'),
			'class' => array(),
			'style' => null
		), $options);

		if (is_string($options['class'])) {
			$options['class'] = explode(' ', $options['class']);
		}

		$viewUrl = $this->Policy->getDocumentUrl($policy['id'], true);
		$documentAttrs = $this->Policy->getDocumentAttrs($policy['id']);

		$documentAttrs['class'] = implode(' ', $options['class']);
		$documentAttrs['style'] = $options['style'];

		if (!empty($options['tooltip'])) {
			$documentAttrs = am($documentAttrs, array(
				'class' => 'bs-tooltip',
				'title' => $options['tooltip'],
				'style' => 'text-decoration:none;'
			));
		}

		return $this->Html->link($options['title'], $viewUrl, $documentAttrs);

		/*if (empty($policy['use_attachments'])) {
			$viewUrl = $this->Policy->getDocumentUrl($policy['id'], true);
			$documentAttrs = $this->Policy->getDocumentAttrs($policy['id']);
			$documentAttrs = am($documentAttrs, array(
				'class' => 'bs-tooltip',
				'title' => __('View'),
				'style' => 'text-decoration:none;'
			));

			return $this->Html->link('<i class="icon-info-sign"></i>', $viewUrl, $documentAttrs);
		}
		elseif ($policy['use_attachments'] == SECURITY_POLICY_USE_URL) {
			return $this->Html->link('<i class="icon-info-sign"></i>', $policy['url'], array(
				'target' => '_blank',
				'escape' => false
			));
		}
		else {
			$viewUrl = $this->Policy->getDocumentUrl($policy['id'], true);
			$documentAttrs = $this->Policy->getDocumentAttrs($policy['id']);
			$documentAttrs = am($documentAttrs, array(
				'class' => 'bs-tooltip',
				'title' => __('View'),
				'style' => 'text-decoration:none;'
			));

			return $this->Html->link('<i class="icon-info-sign"></i>', $viewUrl, $documentAttrs);
		}*/
	}

	public static function complianceItemsList($Item)
	{
		return SecurityServicesHelper::complianceItemsList($Item);
	}

	public static function riskItemsList($Item)
	{
		$list = '';

		$risks = [
			'RiskIncident' => __('Incident Asset Risk'),
			'RiskTreatment' => __('Treatment Asset Risk'),
			'ThirdPartyRiskIncident' => __('Incident Third Party Risk'),
			'ThirdPartyRiskTreatment' => __('Treatment Third Party Risk'),
			'BusinessContinuityIncident' => __('Incident Business Risk'),
			'BusinessContinuityTreatment' => __('Treatment Business Risk'),
		];

		foreach ($risks as $model => $modelLabel) {
			foreach ($Item->{$model} as $RiskItem) {
				$list .= sprintf('<li>%s / %s </li>', $modelLabel, $RiskItem->title);
			}
		}

		if (!empty($list)) {
			$list = '<ul>' . $list . '</ul>';
		}

		return $list;
	}

	public static function dataAssetItemsList($Item)
	{
		return SecurityServicesHelper::dataAssetItemsList($Item);
	}
}