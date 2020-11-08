<?php
App::uses('AppHelper', 'View/Helper');
class SecurityIncidentsHelper extends AppHelper {
	public $settings = array();
	public $helpers = ['Html', 'FieldData.FieldData', 'LimitlessTheme.Alerts'];

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function securityServiceField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'compensating-controls'
		];

		$out = $this->FieldData->input($Field, $options);

		return $out;
	}

	public function assetField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'compromised-asset'
		];

		$out = $this->FieldData->input($Field, $options);

		return $out;
	}

	public function thirdPartyField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'third-parties-affected'
		];

		$out = $this->FieldData->input($Field, $options);

		return $out;
	}

	public function assetRiskField(FieldDataEntity $Field)
	{
		$id = 'asset-risk-id';

		$options = [
			'class' => ['eramba-auto-complete'],
			'id' => $id,
			'data-model' => 'AssetRisk',
			'data-url' => '/securityIncidents/getAssets',
			'data-request-key' => 'riskIds',
			'data-request-type' => 'POST',
			'data-assoc-input' => '#compromised-asset'
		];

		$out = $this->FieldData->input($Field, $options);
		$out .= $this->_riskWarningScript($id);
		$out .= $this->_riskAutocompleteScript($id);
		$out .= $this->Html->div(null, '', [
			'id' => 'AssetRisk_policies'
		]);

		return $out;
	}

	public function thirdPartyRiskField(FieldDataEntity $Field)
	{
		$id = 'third-party-risk-id';

		$options = [
			'class' => ['eramba-auto-complete'],
			'id' => $id,
			'data-model' => 'ThirdPartyRisk',
			'data-url' => '/securityIncidents/getThirdParties',
			'data-request-key' => 'riskIds',
			'data-request-type' => 'POST',
			'data-assoc-input' => '#third-parties-affected'
		];

		$out = $this->FieldData->input($Field, $options);
		$out .= $this->_riskWarningScript($id);
		$out .= $this->_riskAutocompleteScript($id);
		$out .= $this->Html->div(null, '', [
			'id' => 'ThirdPartyRisk_policies'
		]);

		return $out;
	}

	public function businessContinuityField(FieldDataEntity $Field)
	{
		$id = 'business-continuity-id';

		$options = [
			'id' => $id,
			'data-model' => 'BusinessContinuity'
		];

		$out = $this->FieldData->input($Field, $options);
		$out .= $this->_riskWarningScript($id);
		$out .= $this->_riskAutocompleteScript($id);
		$out .= $this->Html->div(null, '', [
			'id' => 'BusinessContinuity_policies'
		]);

		return $out;
	}

	protected function _riskWarningScript($id)
	{
		$out = $this->Html->scriptBlock('
			jQuery(function($) {
				$("#' . $id . '").on("change.ErambaWarning", function(e) {
					var riskIds = [];
					var model = $(this).data("model");

					$.each($(this).find("option:selected"), function(i, e) {
						riskIds.push($(e).val());
					});


					var $formGroup = $("#" + model + "_policies").closest(".form-group");

					$.ajax({
						url: "/securityIncidents/getRiskProcedures",
						type: "GET",
						dataType: "HTML",
						data: {
							riskIds: JSON.stringify(riskIds),
							model: model
						},
						beforeSend: function( xhr ) {
							// Eramba.Ajax.blockEle($formGroup);
						}
					})
					.done(function(data) {
						var $policyListWrapper = $("#" + model + "_policies");
						$policyListWrapper.empty().html(data);

						// Eramba.Ajax.unblockEle($formGroup);
					});
				}).trigger("change.ErambaWarning");
			});
		');

		return $out;
	}

	protected function _riskAutocompleteScript($id)
	{
		$out = $this->Html->scriptBlock('
			jQuery(function($) {
				$("#' . $id . '").erambaAutoComplete({
					input: "#asset-risk-id, #third-party-risk-id, #business-continuity-id",
					url: "/securityIncidents/getControls",
					requestKey: ["riskIds", "tpRiskIds", "buRiskIds"],
					requestType: "GET",
					assocInput: "#compensating-controls"
				});
			});
		');

		return $out;
	}

	public function autoCloseIncidentField(FieldDataEntity $Field)
	{
		$edit = $this->_View->get('edit');
		$stagesExists = $this->_View->get('stagesExists');

		$options = [
			'class' => ['auto-close-incident'],
			'disabled' => !$stagesExists
		];

		if (!isset($edit)) {
			$options['default'] = true;
		}

		$out = $this->FieldData->input($Field, $options);
		if (!$stagesExists) {
			$out .= $this->Alerts->info(__('Not available as stages are not defined.'));
		}

		return $out;
	}

	public function closureDateField(FieldDataEntity $Field)
	{
		$stagesExists = $this->_View->get('stagesExists');

		$FieldDataCollection = ClassRegistry::init('SecurityIncident')->getFieldCollection();

		$out = $this->FieldData->input($FieldDataCollection->auto_close_incident);
        $out .= $this->FieldData->input($Field);

        if ($stagesExists) {
	        $out .= $this->Html->scriptBlock('
				$(function() {
					$statusSelect = $("#SecurityIncidentSecurityIncidentStatusId");
					$closureDateInput = $("[data-field-name=closure_date]");
					$autoCloseInput = $("[data-field-name=auto_close_incident]")

					function toggleClosureDate() {
						if ($autoCloseInput.is(":checked")) {
							$closureDateInput.attr("disabled", true);
						}
						else {
							$closureDateInput.attr("disabled", false);
						}
					}

					toggleClosureDate();
					$autoCloseInput.on("change", function() {
						toggleClosureDate();
					});
				});
			');
	    }

        return $out;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'SecurityIncident');
		$statuses = array();

		if ($item['SecurityIncident']['security_incident_status_id'] == SECURITY_INCIDENT_ONGOING) {
			if ($this->getAllowCond($allow, 'ongoing_incident') && $item['SecurityIncident']['ongoing_incident'] == SECURITY_INCIDENT_ONGOING_INCIDENT) {
				$statuses[$this->getStatusKey('ongoing_incident')] = array(
					'label' => __('Ongoing Incident'),
					'type' => 'warning'
				);
			}

			if ($this->getAllowCond($allow, 'lifecycle_incomplete') && $item['SecurityIncident']['lifecycle_incomplete']) {
				$statuses[$this->getStatusKey('lifecycle_incomplete')] = array(
					'label' => __('Lifecycle Incomplete'),
					'type' => 'warning'
				);
			}
		}

		if ($this->getAllowCond($allow, 'security_incident_status_id') && $item['SecurityIncident']['security_incident_status_id'] == SECURITY_INCIDENT_CLOSED) {
			$statuses[$this->getStatusKey('security_incident_status_id')] = array(
				'label' => __('Closed'),
				'type' => 'success'
			);
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);
		
		return $this->styleStatuses($statuses, $options);
	}

}