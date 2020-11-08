<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');
App::uses('Inflector', 'Utility');

class SectionView extends CrudView
{
	public $plugin = false;

	/**
	 * List of all sections in the current view tree;
	 * 
	 * @var array
	 */
	protected $_sections = null;

	public function initialize()
	{
		parent::initialize();

		$this->_setSections();

		$this->_initCurrentSectionHelper();
	}

	/**
	 * Shorthand method to get the current request action used by CrudComponent.
	 * 
	 * @see CrudComponent::action();
	 * @return CrudAction
	 */
	public function getAction()
	{
		return $this->getSubject()->crud->action();
	}

	/**
	 * Configure list of sections that should be available to see during a request within current section.
	 *
	 * @return void
	 */
	protected function _setSections()
	{
		$SectionListener = $this->_listener('Section');
		$Subject = $this->getSubject();

		$sections = (array) $Subject->modelClass;
		$sections = array_merge($sections, $SectionListener->getChildrenByModel($Subject->modelClass));

		// if current section is a child of some other sections,
		// we include it's related sections on the same level and it's parent to the final list
		if ($Subject->model->hasMethod('parentModel') && $Subject->model->alias != 'DataAssetInstance') {
			$parentModel = (array) $Subject->model->parentModel();
			$neighbourghs = $SectionListener->getNeighbourghs();

			// root section is always first
			$sections = array_merge($parentModel, $sections, $neighbourghs);
		}

		$rootSection = array_shift($sections);
		sort($sections);
		array_unshift($sections, $rootSection);

		// hardcode mappings as last item in the queue
		if (in_array('ComplianceManagementMappingRelation', $sections)) {
			$key = array_search('ComplianceManagementMappingRelation', $sections);
			unset($sections[$key]);
			$sections[] = 'ComplianceManagementMappingRelation';
		}
		
		$this->_sections = $sections;
	}

	/**
	 * Get visible sections within current request.
	 * 
	 * @return array List of sections.
	 */
	public function getSections()
	{
		return $this->_sections;
	}

	public function getChildren()
	{
		return $this->_listener('Section')->getChildren();
	}

	/**
	 * Init CrudHelper of current model section.
	 * 
	 * @return void
	 */
	public function _initCurrentSectionHelper()
	{
		$Model = $this->getSubject()->model;

		$pluginPrefix = '';
		if (!empty($Model->plugin)) {
			$pluginPrefix = $Model->plugin . '.';
		}

		$helperAlias = Inflector::pluralize($Model->name) . 'Crud';
		$helperClass = $helperAlias . 'Helper';

		App::uses($helperClass, $pluginPrefix . 'View/Helper');

		if (class_exists($helperClass)) {
			$this->_controller()->helpers[] = $pluginPrefix . $helperAlias;
		}
	}
}
