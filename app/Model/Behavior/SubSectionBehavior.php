<?php
App::uses('ModelBehavior', 'Model/Behavior');

class SubSectionBehavior extends ModelBehavior
{

	protected $_defaults = array(
		'parentField' => false, // field of parent section
		'childModels' => false // toggle of child models
	);

	public $settings = [];

	public function setup(Model $Model, $settings = [])
	{
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}
	}

	public function getChildModels(Model $Model)
	{
		$hasMany = $Model->getAssociated('hasMany');
		
		$childModels = [];
		if (!empty($hasMany)) {
			foreach ($hasMany as $assoc) {
				$subAssoc = $Model->{$assoc};
				$reverseAssoc = $subAssoc->getAssociated($Model->alias);
				$reverseForeignKey = $reverseAssoc['foreignKey'];

				if ($subAssoc->Behaviors->enabled('SubSection')) {
					$reverseParentField = $subAssoc->Behaviors->SubSection->settings[$subAssoc->alias]['parentField'];

					if ($reverseForeignKey == $reverseParentField) {
						$childModels[] = $assoc;
					}
				}
			}
		}

		return $childModels;
	}

}