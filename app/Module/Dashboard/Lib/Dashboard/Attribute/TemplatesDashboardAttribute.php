<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

/**
 * Abstract class to make solid base in Advanced Filters to make use of Dashboard Attributes.
 */
abstract class TemplatesDashboardAttribute extends DashboardAttribute {
	public $templateClassName = 'Dashboard.DashboardTemplate';

	// possibility to use templates that re-use advanced filter field parameters to build query
	public $templates = null;

	/**
	 * Loaded templates.
	 * 
	 * @var array
	 */
	protected $_loaded = [];

	/**
	 * Get instance of a class initialized with requested template.
	 * 
	 * @param  string $path    Template path @see Hash::get().
	 * @return DashboardTemplate instance of a class.
	 */
	public function templateInstance(Model $Model, $path) {
		$template = Hash::get($this->templates, $path);
		if ($template === null) {
			throw new DashboardException("Error while reading a dashboard attribute template. Template {$path} not found.", 1);
		}

		list($plugin, $class) = pluginSplit($this->templateClassName, true);
		if (isset($this->_loaded[$path])) {
			return $this->_loaded[$path];
		}

		$object = $class;
		App::uses($class, $plugin . 'Lib/Dashboard/Attribute/Template');

		if (!class_exists($class)) {
			throw new CakeException(sprintf('Class for Dashboard Templater %s doesnt exist.', $class));
		}

		$TemplateInstance = new $class($path, $template);
		$this->_loaded[$path] = $TemplateInstance;

		return $this->_loaded[$path];
	}

	public function getLabel(Model $Model, $attribute) {
		return $this->templateInstance($Model, $attribute)->getTitle($Model->label());
	}

	public function listAttributes(Model $Model) {
		return array_keys($this->templates);
	}

	// returns if the template allows to find trashed items
	public function softDelete(Model $Model, $attribute) {
		$TemplateInstance = $this->templateInstance($Model, $attribute);

		return $TemplateInstance->softDelete();
	}

}