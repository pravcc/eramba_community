<?php
App::uses('SectionBaseHelper', 'View/Helper');
App::uses('Country', 'Model');
App::uses('DataAssetGdprArchivingDriver', 'Model');
App::uses('Hash', 'Utility');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('DataAssetGdprDataType', 'Model');
App::uses('DataAssetGdprLawfulBase', 'Model');

class DataAssetGdprHelper extends SectionBaseHelper {
	public $helpers = ['Html', 'Eramba', 'FieldData.FieldData', 'LimitlessTheme.Alerts'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function idField(FieldDataEntity $Field)
	{
		$info = '';

		if ($this->_View->get('dataAssetInstance') !== null) {
			$dataAssetInstance = $this->_View->get('dataAssetInstance');
			$message = ($dataAssetInstance['DataAssetSetting']['gdpr_enabled']) ? __('Since GDPR is enabled all fields in this tab must be completed.') : __('Since GDPR is disabled at the asset general attributes this fields are disabled.');

			$info = $this->Alerts->info($message);
		}

		return $this->FieldData->input($Field) . $info;
	}

	public function dataAssetGdprDataTypeField(FieldDataEntity $Field)
	{
		$info = $this->Alerts->info(
			__('Sensitive Data: Processing sensitive data may be prohibited unless certain conditions apply. You may want to review Rec.51-56; Art.9, Art.9(2)(a) to Art.9(2)(j) and Art.9(4).'),
			['class' => 'data-asset-gdpr-data-type-info data-asset-gdpr-data-type-info-' . DataAssetGdprDataType::SENSITIVE]
		);

		$info .= $this->Alerts->info(
			__('Criminal Offences: You may want to look at certain constraints stated on Art.10, 23(1)(j).'),
			['class' => 'data-asset-gdpr-data-type-info data-asset-gdpr-data-type-info-' . DataAssetGdprDataType::CRIMINAL_OFFENCES]
		);

		$script = $this->Html->scriptBlock("
			function toggleDataTypeWarning() {
				$('.data-asset-gdpr-data-type-info').addClass('hidden');
				if ($('#DataAssetGdprDataAssetGdprDataType').val() !== null) {
					$('#DataAssetGdprDataAssetGdprDataType').val().forEach(function(item) {
						$('.data-asset-gdpr-data-type-info-' + item).removeClass('hidden');
					});
				}
			}
			toggleDataTypeWarning();
			$('#DataAssetGdprDataAssetGdprDataType').on('change', function() {
				toggleDataTypeWarning();
			});
		");

		return $this->FieldData->input($Field) . $info . $script;
	}

	public function dataAssetGdprLawfulBaseField(FieldDataEntity $Field)
	{
		$info = $this->Alerts->info(
			__('Consent: You may want to look at certain constraints stated on Rec.32, 43; Art.7(4), Rec.32; Art.6(1)(a), Rec.32, 42; Art.4(11), 7(1), Rec 32, Art.7(2), Rec.42, 65; Art.7(3), Rec.111; Art.49(1)(a), (3), Rec.171 and Rec.42; Art.7(1).'),
			['class' => 'data-asset-gdpr-lawful-base-info data-asset-gdpr-lawful-base-info-' . DataAssetGdprLawfulBase::CONSENT]
		);

		$script = $this->Html->scriptBlock("
			function toggleLawfulBaseWarning() {
				$('.data-asset-gdpr-lawful-base-info').addClass('hidden');
				if ($('#DataAssetGdprDataAssetGdprLawfulBase').val() !== null) {
					$('#DataAssetGdprDataAssetGdprLawfulBase').val().forEach(function(item) {
						$('.data-asset-gdpr-lawful-base-info-' + item).removeClass('hidden');
					});
				}
			}
			toggleLawfulBaseWarning();
			$('#DataAssetGdprDataAssetGdprLawfulBase').on('change', function() {
				toggleLawfulBaseWarning();
			});
		");

		return $this->FieldData->input($Field) . $info . $script;
	}

	public function dataAssetGdprArchivingDriverField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('.DataAssetGdprArchivingDriverEmpty').on('change', function() {
			    if ($(this).is(':checked')) {
			        $('#DataAssetGdprDataAssetGdprArchivingDriver').val('').trigger('change');
			    }
			});
			$('#DataAssetGdprDataAssetGdprArchivingDriver').on('change', function(e) { 
			    $('.DataAssetGdprArchivingDriverEmpty').prop('checked', true).trigger('click');
			});
        ");

		$notApplicableInput = $this->FieldData->input(ClassRegistry::init('DataAssetGdpr')->getFieldDataEntity('archiving_driver_empty'), [
			'label' => false,
			'class' => ['DataAssetGdprArchivingDriverEmpty'],
			'toggleLabel' => __('Not Applicable'),
		]);

		return $this->FieldData->input($Field, [
			'after' => $notApplicableInput . $this->FieldData->error($Field) . $this->FieldData->description($Field),
		]) . $script;
	}

	public function thirdPartyInvolvedField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('.third-party-involved-all').on('change', function() {
			    if ($(this).is(':checked')) {
			        $('#third-party-involved').val('').trigger('change');
			    }
			});
			$('#third-party-involved').on('change', function(e) {
				if ($(this).val()) {
					$('.third-party-involved-all').prop('checked', true).trigger('click');
				}
			});
        ");

		$ThirdPartyInvolvedAllField = ClassRegistry::init('DataAssetGdpr')->getFieldDataEntity('third_party_involved_all');
		$notApplicableInput = $this->FieldData->input($ThirdPartyInvolvedAllField, [
			'label' => false,
			'class' => ['third-party-involved-all'],
			'toggleLabel' => $ThirdPartyInvolvedAllField->getLabel(),
		]);

		return $this->FieldData->input($Field, [
			'id' => 'third-party-involved',
			'after' => $notApplicableInput . $this->FieldData->error($Field) . $this->FieldData->description($Field),
		]) . $script;
	}

	public function transferOutsideEeaField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			function toggleEeaFields() {
				if (!$('.DataAssetGdprTransferOutsideEea').is(':checked')) {
					$('#third-party-involved').prop('disabled', true).val('').trigger('change');
					$('.third-party-involved-all').data('switchery').disable();
					// $('.third-party-involved-all').data('disabled', true).prop('checked', false).trigger('click');
					$('#DataAssetGdprDataAssetGdprThirdPartyCountry').prop('disabled', true).val('').trigger('change');
				}
				else {
					$('#third-party-involved').prop('disabled', false);
					$('.third-party-involved-all').data('switchery').enable();
					$('#DataAssetGdprDataAssetGdprThirdPartyCountry').prop('disabled', false);
				}
			}
	
			setTimeout(function() {
				toggleEeaFields();
			}, 500);

			$('.DataAssetGdprTransferOutsideEea').on('change', function() {
				toggleEeaFields();
			});
		");

		return $this->FieldData->input($Field, ['class' => ['DataAssetGdprTransferOutsideEea']]) . $script;
	}

	public function getThirdPartyInvolved($data, $tags = false) {
		$countries = [];

		if ($data['DataAssetGdpr']['third_party_involved_all']) {
			$item = __('Anywhere in the world');
			$countries[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
		}
		else {
			$countryIds = Hash::extract($data, 'DataAssetGdpr.ThirdPartyInvolved.{n}.country_id');
			foreach ($countryIds as $countryId) {
				$item = Country::countries()[$countryId];
				$countries[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
			}
		}

		$separator = ($tags) ? ' ' : ', ';
		
		return implode($countries, $separator);
	}

	public function outputThirdPartyInvolved($data) {
		if (empty($data)) {
			return $this->Eramba->getEmptyValue('');
		}

		return $this->getThirdPartyInvolved($data, true);
	}

	public function getArchivingDriver($data, $tags = false) {
		$list = [];

		if ($data['DataAssetGdpr']['archiving_driver_empty']) {
			$item = __('Not applicable');
			$list[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
		}
		else {
			$driverIds = Hash::extract($data, 'DataAssetGdpr.DataAssetGdprArchivingDriver.{n}.archiving_driver');
			foreach ($driverIds as $driverId) {
				$item = DataAssetGdprArchivingDriver::archivingDrivers()[$driverId];
				$list[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
			}
		}

		$separator = ($tags) ? ' ' : ', ';
		
		return implode($list, $separator);
	}

	public function outputArchivingDriver($data) {
		if (empty($data)) {
			return $this->Eramba->getEmptyValue('');
		}

		return $this->getArchivingDriver($data, true);
	}
}