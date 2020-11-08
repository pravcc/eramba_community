<?php
App::uses('AppHelper', 'View/Helper');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('DataAsset', 'View/Helper');

class DataAssetsHelper extends AppHelper
{
	public $helpers = ['Html', 'FieldData.FieldData', 'FormReload', 'LimitlessTheme.Alerts'];
	public $settings = [];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function dataAssetInstanceField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function dataAssetStatusIdField(FieldDataEntity $Field)
	{
		$info = '';

		if (!empty($this->_View->request->data['DataAsset']['data_asset_status_id'])) {
			$info = $this->Alerts->info(DataAsset::statusesInfo($this->_View->request->data['DataAsset']['data_asset_status_id']));
		}

		return $this->FieldData->input($Field, $this->FormReload->triggerOptions()) . $info;
	}

	public function riskField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('#risk-id').erambaAutoComplete({
				url: '/dataAssets/getAssociatedRiskData',
				requestKey: ['riskIds'],
				requestType: 'GET',
				responseKey: ['securityServices', 'projects', 'securityPolicies'],
				assocInput: '#security-service-id, #project-id, #security-policy-id'
			});
		");

		return $this->FieldData->input($Field, [
			'id' => 'risk-id'
		]) . $script;
	}

	public function thirdPartyRiskField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('#third-party-risk-id').erambaAutoComplete({
				url: '/dataAssets/getAssociatedThirdPartyRiskData',
				requestKey: ['riskIds'],
				requestType: 'GET',
				responseKey: ['securityServices', 'projects', 'securityPolicies'],
				assocInput: '#security-service-id, #project-id, #security-policy-id'
			});
		");

		return $this->FieldData->input($Field, [
			'id' => 'third-party-risk-id'
		]) . $script;
	}

	public function businessContinuityField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('#business-continuity-id').erambaAutoComplete({
				url: '/dataAssets/getAssociatedBusinessContinuityData',
				requestKey: ['riskIds'],
				requestType: 'GET',
				responseKey: ['securityServices', 'projects', 'securityPolicies'],
				assocInput: '#security-service-id, #project-id, #security-policy-id'
			});
		");

		return $this->FieldData->input($Field, [
			'id' => 'business-continuity-id'
		]) . $script;
	}

	public function securityServiceField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('#security-service-id').erambaAutoComplete({
				url: '/dataAssets/getAssociatedSecurityServices',
				requestKey: ['serviceIds'],
				requestType: 'GET',
				assocInput: '#security-policy-id'
			});
		");

		return $this->FieldData->input($Field, [
			'id' => 'security-service-id'
		]) . $script;
	}

	public function securityPolicyField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'id' => 'security-policy-id'
		]);
	}

	public function projectField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'id' => 'project-id'
		]);
	}

}