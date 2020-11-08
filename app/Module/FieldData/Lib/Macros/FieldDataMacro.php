<?php
App::uses('Macro', 'Macros.Lib');
App::uses('ObjectRendererHelper', 'ObjectRenderer.View/Helper');

/**
 * FieldData Macro class.
 */
class FieldDataMacro extends Macro
{

	/**
	 * Get macro value.
	 *
	 * @param string $data Subject item data.
	 * @return string Macro value.
	 */
	public function getValue($data)
	{
		if (isset($this->subject()->modelPath)) {
			$data = $this->_traverse($data, $this->subject()->modelPath);
		}

		// if there is no data return empty string
		if ($data === null) {
			return '';
		}

		$params = [
			'field' => $this->subject()->field,
			'item' => $data,
		];

		$processors = [
			'Text',
			'CustomFields.CustomFields',
			ObjectRendererHelper::getSectionProcessor($data->getModel())
		];

		$output = call_user_func($this->_value, 'AdvancedFilters.Cell', $params, $processors);
		$output->setRenderScope(['text']);

		return $output->render();
	}

	/**
	 * Traverse items function.
	 */
	protected function _traverse($data, $path)
	{
		$path = explode('.', $path);

        foreach ($path as $assoc) {
        	$data = $data->{$assoc};

        	if ($data === null) {
        		return null;
        	}
        }

        return $data;
	}
}