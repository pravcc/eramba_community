<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('Inflector', 'Utility');

class ObjectRenderer
{
	protected $_View = null;

	protected $_output = 'ObjectRenderer.Base';

	public function __construct(View $View)
	{
		$this->_View = $View;
	}

	public function render($params = [], $processors = [])
	{
		return $this->getOutput($params, $processors)->render();
	}

	public function getOutput($params = [], $processors = [])
	{
		$outputClass = $this->_loadOutputClass($this->_outputClass);
		$Output = new $outputClass($this->_View);

		$this->runProcessors($Output, $processors, $params);

		return $Output;
	}

	public function _render($params = [])
	{
		$outputClass = $this->_loadOutputClass($this->_outputClass);
		$Output = new $outputClass($this->_View);

		$this->runProcessors($Output, $this->_processors, $params);

		return $Output->render();
	}

	public function addProcessor($processors = [])
	{
		$processors = (array) $processors;

		$this->_processors = array_merge($this->_processors, $processors);
	}

	public function setOutputClass($outputClass)
	{
		$this->_outputClass = $outputClass;
	}

	public function runProcessors($Output, $processors, $params)
	{
		$params['view'] = $this->_View;

		foreach ($processors as $processor => $options) {
			if (is_string($options)) {
				$processor = $options;
			}

			if ($options === false) {
				continue;
			}

			if (is_array($options)) {
				$params = array_merge($params, $options);
			}

			$this->_executeProcessor($processor, $Output, $params);
		}
	}

	protected function _executeProcessor($processor, OutputBuilder $Output, $params)
	{
		$subject = (object) $params;

		if (is_string($processor)) {
			if (!self::processorExists($processor)) {
				return false;
			}

			$className = $this->_loadProcessorClass($processor);
			$processor = new $className();
		}

		$Output->apply($processor, $subject);
	}

	protected function _loadOutputClass($outputBuilder)
	{
		list($plugin, $class) = pluginSplit($outputBuilder, true);

		$class .= 'Output';

		App::uses($class, $plugin . 'View/Renderer/Output');

		return $class;
	}

	protected function _loadProcessorClass($processor)
	{
		list($plugin, $class) = pluginSplit($processor, true);

		$class .= 'RenderProcessor';

		App::uses($class, $plugin . 'View/Renderer/Processor');

		return $class;
	}

	public static function processorExists($processor)
	{
		list($plugin, $class) = pluginSplit($processor);

		if (!empty($plugin) && !AppModule::loaded($plugin)) {
			return false;
		}

		$class = self::_loadProcessorClass($processor);

		return class_exists($class);
	}


}