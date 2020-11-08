<?php
App::uses('CrudBaseObject', 'Crud.Controller/Crud');

/**
 * Manage View logic within a class initiated inside instance of CrudListener class.
 */
abstract class CrudView extends CrudBaseObject {

	/**
	 * The name of this CrudView. CrudView names are named after the CrudListener they manipulate.
	 *
	 * @var string
	 */
	public $name = null;

	public $plugin = null;

	/**
	 * Constructor for CrudView.
	 * 
	 * @param CrudSubject $subject
	 */
	public function __construct(CrudSubject $subject, $defaults = [])
	{
		parent::__construct($subject, $defaults);

		if ($this->name === null) {
			$this->name = substr(get_class($this), 0, -4);
		}

		if ($this->plugin === null) {
			$this->plugin = $this->name;
		}

		$this->initialize();
	}

	/**
	 * No events available for CrudView yet.
	 * 
	 * @return array
	 */
	public function implementedEvents()
	{
		return [];
	}

	/**
	 * Method gets executed after this class instance is successfully constructed.
	 * Meant to separate individual class' logic from default construct() logic.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		// individual initialize logic for listener's beforeRender event
		$pluginPrefix = (!empty($this->plugin)) ? "{$this->plugin}." : '';
		$helperClass = "{$pluginPrefix}{$this->name}Crud";
		$this->_controller()->helpers[] = $helperClass;
	}

	/**
	 * Exposed method that returns current CrudSubject instance.
	 * 
	 * @return CrudSubject
	 */
	public function getSubject()
	{
		return $this->_container;
	}

}