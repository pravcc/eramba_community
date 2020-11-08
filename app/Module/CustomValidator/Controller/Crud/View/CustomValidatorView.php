<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud/View');

class CustomValidatorView extends CrudView
{
	/**
	 * Check if custom validator is enabled on section.
	 * 
	 * @return boolean
	 */
	public function enabled()
	{
		return $this->getSubject()->model->Behaviors->enabled('CustomValidator.CustomValidator');
	}

	public function getValidators()
	{
		return $this->getSubject()->model->getCustomValidator();
	}
}
