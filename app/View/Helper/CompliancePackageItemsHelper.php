<?php
class CompliancePackageItemsHelper extends AppHelper {
	public $helpers = array('Html', 'FieldData.FieldData', 'LimitlessTheme.Alerts');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function compliancePackageRegulatorField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field, [
			'data-yjs-request' => 'crud/load',
			'data-yjs-event-on' => 'change',
			'data-yjs-server-url' => 'post::/compliancePackageItems/loadPackages',
			'data-yjs-target' => '#compliance-package-field-wrapper',
			'data-yjs-forms' => 'CompliancePackageItemSectionAddForm|CompliancePackageItemSectionEditForm'
		]);

		$content = $this->_View->element('../CompliancePackageItems/load_packages');

		$out .= $this->Html->div('', $content, [
			'id' => 'compliance-package-field-wrapper'
		]);

		return $out;
	}

	public function compliancePackageField(FieldDataEntity $Field)
	{
		$CompliancePackageCollection = $this->_View->get('CompliancePackageCollection');

		$out = null;

		$out .= $this->FieldData->input($Field, [
			'data-yjs-request' => 'crud/load',
			'data-yjs-event-on' => 'change',
			'data-yjs-server-url' => 'post::/compliancePackageItems/loadPackageFormFields',
			'data-yjs-target' => '#compliance-package-form-fields-wrapper',
			'data-yjs-forms' => 'CompliancePackageItemSectionAddForm|CompliancePackageItemSectionEditForm'
		]);

		$content = $this->_View->element('../CompliancePackageItems/load_package_form_fields');

		$out .= $this->Html->div('', $content, [
			'id' => 'compliance-package-form-fields-wrapper'
		]);

		return $out;
	}

	//sort items by item_id
	public function sortByItemId($data){
		$defaultSortItemId = [0, 0, 0, 0, 0, 0, 0];
		foreach ($data as $key => $packageItem) {
			$explodeSortId = explode('.', $packageItem['item_id']) + $defaultSortItemId;
			$sortId = '';
			foreach ($explodeSortId as $idItem) {
				$sortId .= sprintf('%08d', $idItem);
			}
			$data[$key]['sort_item_id'] = $sortId;
		}

		return Hash::sort($data, '{n}.sort_item_id', 'asc');
	}
}