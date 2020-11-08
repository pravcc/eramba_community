<?php
App::uses('AppHelper', 'View/Helper');

class SecurityServiceAuditImprovementsHelper extends AppHelper {
	public $helpers = array('Html', 'FieldData.FieldData');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function securityServiceField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'disabled' => true
			// 'disabled' => $this->request->action === 'edit' ? true : false
		]);
	}

}