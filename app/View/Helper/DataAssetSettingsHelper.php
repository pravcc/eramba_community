<?php
App::uses('SectionBaseHelper', 'View/Helper');
App::uses('Country', 'Model');
App::uses('Hash', 'Utility');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class DataAssetSettingsHelper extends SectionBaseHelper
{
	public $helpers = ['Html', 'Eramba', 'Form', 'FieldData.FieldData', 'FormReload'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function nameField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'readonly' => true
		]);
	}

	public function gdprEnabledField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function dpoField(FieldDataEntity $Field)
	{
		$script = $this->Html->scriptBlock("
			$('.not-applicable-checkbox').on('change', function(e, stopPropagate) {
	            if (stopPropagate !== true && $(this).is(':checked')) {
	                $(this).closest('.form-group').parent().closest('.form-group').find('.not-applicable-select').val('').trigger('change', true);
	            }
	        });
	        $('.not-applicable-select').on('change', function(e, stopPropagate) {
	        	if (stopPropagate !== true) {
	        		$(this).closest('.form-group').find('.not-applicable-checkbox').prop('checked', true).trigger('click', true);
        		}
	        });
        ");

		return $this->_notApplicableField($Field, 'dpo_empty') . $script;
	}

	public function processorField(FieldDataEntity $Field)
	{
		return $this->_notApplicableField($Field, 'processor_empty');
	}

	public function controllerField(FieldDataEntity $Field)
	{
		return $this->_notApplicableField($Field, 'controller_empty');
	}

	public function controllerRepresentativeField(FieldDataEntity $Field)
	{
		return $this->_notApplicableField($Field, 'controller_representative_empty');
	}

	protected function _notApplicableField(FieldDataEntity $Field, $emptyField)
	{
		$notApplicableInput = $this->FieldData->input(ClassRegistry::init('DataAssetSetting')->getFieldDataEntity($emptyField), [
			'label' => false,
			'toggleLabel' => __('Not Applicable'),
			'class' => ['not-applicable-checkbox']
		]);

		return $this->FieldData->input($Field, [
			'after' => $notApplicableInput . $this->FieldData->error($Field) . $this->FieldData->description($Field),
			'class' => ['not-applicable-select']
		]);
	}

	public function getDpo($data, $tags = false) {
		$list = [];

		if ($data['DataAssetSetting']['dpo_empty']) {
			$item = __('Not applicable');
			$list[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
		}
		else {
			$list = Hash::extract($data, 'DataAssetSetting.Dpo.{n}.full_name');
			if ($tags) {
				foreach ($list as $key => $user) {
					$list[$key] = $this->Eramba->getLabel($user, 'improvement');
				}
			}
		}

		$separator = ($tags) ? ' ' : ', ';
		
		return implode($list, $separator);
	}

	public function outputDpo($data) {
		if (empty($data)) {
			return $this->Eramba->getEmptyValue('');
		}

		return $this->getDpo($data['DataAssetInstance'], true);
	}

	public function getControllerRepresentative($data, $tags = false) {
		$list = [];

		if ($data['DataAssetSetting']['controller_representative_empty']) {
			$item = __('Not applicable');
			$list[] = ($tags) ? $this->Eramba->getLabel($item, 'improvement') : $item;
		}
		else {
			$list = Hash::extract($data, 'DataAssetSetting.ControllerRepresentative.{n}.full_name');
			if ($tags) {
				foreach ($list as $key => $user) {
					$list[$key] = $this->Eramba->getLabel($user, 'improvement');
				}
			}
		}

		$separator = ($tags) ? ' ' : ', ';
		
		return implode($list, $separator);
	}

	public function outputControllerRepresentative($data) {
		if (empty($data)) {
			return $this->Eramba->getEmptyValue('');
		}

		return $this->getControllerRepresentative($data['DataAssetInstance'], true);
	}
}