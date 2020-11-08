<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Dispatcher class for any Section Helper class that can route methods using a model name as argument. 
 */
class SectionDispatchHelper extends AppHelper {

	/**
	 * Call section's helper methods throught this dispatcher using a Model name.
	 * Can be used in functionalities that can manage all sections in one place under one view.
	 * 
	 * @param  string $name Method to call.
	 * @param  array  $args Arguments, first argument should always be a model name.
	 */
	public function __call($name, $args) {
		if (count($args) < 1) {
			throw new InternalErrorException('Model reference for dispatch is missing.');
		}

		// First argument should always be a model name when using this dispatcher.
		// The rest of the argument list goes to the called method as arguments.
		$model = array_shift($args);
		$SectionHelper = $this->initHelper($model);
		if (!$SectionHelper instanceof Helper) {
			return false;
		}

		return call_user_func_array([$SectionHelper, $name], $args);
	}

	public function methodExists($model, $name) {
		$SectionHelper = $this->initHelper($model);
		
		return method_exists($SectionHelper, $name);
	}

	/**
	 * Initialize section helper class based on model and convention - pluralized version of a model.
	 * 
	 * @param  string $model Model name.
	 * @return Helper        Helper class.
	 */
	protected function initHelper($model) {
		$Helper = Inflector::pluralize($model);
		$HelperClass = $Helper . 'Helper';

		App::import('Helper', $Helper);
		if (!class_exists($HelperClass)) {
			trigger_error(sprintf('Class %s for SectionDispatch call does not exist!', $HelperClass));
			return false;
		}

		return new $HelperClass($this->_View);
	}

}