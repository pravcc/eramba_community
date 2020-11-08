<?php
/**
 * @package       Visualisation.Lib
 */

App::uses('ModuleBase', 'Lib');
App::uses('AppModule', 'Lib');

//share class
class VisualisationModule extends ModuleBase {
	public $toolbar = false;

	public function __construct() {
		$this->name = __('Visualisation');

		// use this feature together with workflows only
		$this->_whitelist = AppModule::instance('Workflows')->whitelist();

		parent::__construct();
	}

	public static function getCacheKey($loggedId, $model)
	{
		$cacheKeys = [
			'readable',
			$loggedId,
			$model
		];

		return implode('_', $cacheKeys);
	}

	// section settings
	public function getSectionUrl($model) {
		return [
			'plugin' => 'visualisation',
			'controller' => 'visualisationSettings',
			'action' => 'edit',
			$model
		];
	}

	// share item link
	public function getItemUrl($model, $foreignKey) {
		return [
			'plugin' => 'visualisation',
			'controller' => 'visualisation',
			'action' => 'share',
			$model,
			$foreignKey
		];
	}
}
