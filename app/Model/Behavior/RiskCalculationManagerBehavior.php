<?php
App::uses('ModelBehavior', 'Model/Behavior');
use Composer\Autoload;

class RiskCalculationManagerBehavior extends ModelBehavior {
	public $vendorDir;
	private $activeMethods = array('eramba', 'erambaMultiply', 'magerit');
	public $runtime = array();

	public function __construct() {
		$this->vendorDir = APP . 'Vendor' . DS . 'riskCalculations';

		$loader = new \Composer\Autoload\ClassLoader();
		$loader->add('EvalMath', APP . 'Vendor/evalmath');
		$loader->add('RiskCalculation', $this->vendorDir);
		$loader->register();
		unset($loader);
	}

	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array(

			);
		}

		$this->settings[$Model->alias] = array_merge(
		$this->settings[$Model->alias], (array) $settings);
	}

	public function setupCalculationsList(RiskCalculation $Model) {
		$list = $rules = array();
		foreach ($this->activeMethods as $method) {
			$f = $this->getMethodNs($method);
			$class = new $f();
			$list[$method] = array(
				'name' => $class->name,
				'description' => $class->description
			);

			foreach ($class->allowedModels as $m) {
				$rules[$m][] = $method;
			}
		}

		$Model->methods = $list;
		$Model->calcRules = $rules;
	}

	private function getMethodNs($method) {
		$fn = Inflector::camelize($method . '_calculation');
		return 'RiskCalculation\Package\\' . $fn;
	}

	public function calculateByMethod(Model $Model, $options = array()) {
		$options = array_merge($options, array());

		$method = $this->getMethod($Model);
		$fn = Inflector::camelize($method . '_calculation');

		$class = $this->getClass($Model);

		$calculationValues = $this->getCalculationValues($Model, $method);
		$this->calculationMath = null;
		return $class->calculate($Model, $options, $calculationValues);
	}

	public function getOtherData(Model $Model) {
		$class = $this->getClass($Model);
		return $class->otherData;
	}

	public function getCalculationMath(Model $Model, $options = array()) {
		$class = $this->getClass($Model);

		return $class->calculationMath;
	}

	public function getMethod(Model $Model) {
		$Model->bindModel(array(
			'belongsTo' => array(
				'RiskCalculation' => array(
					'foreignKey' => false,
					'conditions' => array(
						'RiskCalculation.model' => $Model->alias
					)
				)
			)
		));

		return $Model->RiskCalculation->field('method', array('RiskCalculation.model' => $Model->alias));
	}

	/**
	 * Get calculation values for a certain Calculation Method including all sections.
	 */
	public function getCalculationValues(Model $Model, $method) {
		return $Model->RiskCalculation->RiskCalculationValue->find('all', array(
			'conditions' => array(
				'RiskCalculation.method' => $method
			),
			'fields' => array('RiskCalculationValue.*')
		));
	}

	/**
	 * Get calculation values for a single section.
	 */
	public function getSectionValues(Model $Model) {
		return $Model->RiskCalculation->RiskCalculationValue->find('all', array(
			'conditions' => array(
				'RiskCalculation.model' => $Model->alias
			),
			'fields' => array('RiskCalculationValue.*')
		));
	}

	public function getClassificationTypeValues(Model $Model, $values) {
		$types = array();
		if (!empty($values)) {
			foreach ($values as $value) {
				$types[] = $value['RiskCalculationValue']['value'];
			}
		}

		return array_unique($types);
	}

	public function resetCalculationClass(Model $Model) {
		unset($this->runtime[$Model->alias]);
	}

	public function getClass(Model $Model) {
		if (!isset($this->runtime[$Model->alias]) || !($this->runtime[$Model->alias]['methodClass'] instanceof RiskCalculation\BaseCalculation)) {
			$method = $this->getMethod($Model);

			$fn = Inflector::camelize($method . '_calculation');
			$f = 'RiskCalculation\Package\\' . $fn;
			$class = new $f();
			
			$this->runtime[$Model->alias] = array(
				'method' => $method,
				'methodClass' => $class,
			);
		}
		else {
			$class = $this->runtime[$Model->alias]['methodClass'];
		}

		return $class;
	}
}
