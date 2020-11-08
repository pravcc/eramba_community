<?php
App::uses('AppHelper', 'View/Helper');

class ComplianceManagementsHelper extends AppHelper
{
	public $settings = array();
	public $helpers = array('Html', 'FieldData.FieldData');

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function detailsField(FieldDataEntity $Field)
	{	
		return $this->_View->element('compliance_package_items/info', [
			'data' => $this->_View->get('compliancePackageItem')
		]);
	}

}