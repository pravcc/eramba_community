<?php
App::uses('Macro', 'Macros.Lib');

/**
 * Collection of macros.
 */
class MacroCollection
{
/**
 * Subject model.
 * 
 * @var Model
 */
	protected $_Model = null;

/**
 * List of macros.
 * 
 * @var array
 */
	protected $_macros = [];

/**
 * List of groups.
 * 
 * @var array
 */
	protected $_groups = [];

/**
 * Construct.
 * 
 * @param Model $Model Subject model.
 */
	public function __construct(Model $Model) {
		$this->_Model = $Model;
	}

/**
 * Add macro to collection.
 * 
 * @param Macro $macro
 */
	public function add(Macro $macro, $group = null) {
		if ($group !== null) {
			$this->addGroup($group);
		}

		$this->_macros[$macro->alias()] = [
			'macro' => $macro,
			'group' => $group
		];
	}

/**
 * Add multiple macros by MacroSeed.
 * 
 * @param MacroSeed $seed
 * @return void
 */
	public function addBySeed(MacroSeed $seed) {
		$seed->seed($this);
	}

/**
 * Add group to collection.
 * 
 * @param string $name Group name.
 * @param string $slug Group slug key.
 * @return void 
 */
	public function addGroup($name, $slug = null) {
		if (empty($slug)) {
			$slug = $name;
		}

		if (!isset($this->_groups[$slug])) {
			$this->_groups[$slug] = [
				'slug' => $slug,
				'name' => $name
			];
		}
	}

/**
 * Get Macro object by alias.
 * 
 * @param string $alias
 * @return void
 */
	public function get($alias) {
		$macro = null;

		if ($this->_macros[$alias]) {
			$macro = $this->_macros[$alias]['macro'];
		}

		return $macro;
	}

/**
 * Get macro string by subject (Macro::macro()).
 * 
 * @param mixed $subject
 * @return string
 */
	public function getMacro($subject) {
		$Macro = $this->get($subject);

		return (!empty($Macro)) ? $Macro->macro() : '';
	}

/**
 * Get list of all macros in collection for select element.
 * 
 * @return array List of macros for select.
 */
	public function getList() {
		$list = [];

		foreach ($this->_macros as $macro) {
			$list[$macro['macro']->macro()] = $macro['macro']->label();
		}

		return $list;
	}

/**
 * Get grouped list of all macros in collection for select element.
 * 
 * @return array Grouped list of macros for select.
 */
	public function getGroupedList() {
		$groupList = [];

		foreach ($this->_groups as $group) {
			$groupList[$group['slug']] = [
				'slug' => $group['slug'],
				'name' => $group['name'],
				'macros' => []
			];
		}

		foreach ($this->_macros as $macro) {
			$groupList[$macro['group']]['macros'][] = [
				'macro' => $macro['macro']->macro(),
				'label' => $macro['macro']->label()
			];
		}

		return $groupList;
	}

/**
 * Apply all collection macros on text.
 * 
 * @param string $text Text with possible macros.
 * @param string $data Subject item data.
 * @return string Text with applied macros.
 */
	public function apply($text, $data) {
		foreach ($this->_macros as $macro) {
			$text = $macro['macro']->apply($text, $data);
		}

		return $text;
	}

	public function getModel() {
		return $this->_Model;
	}
}