<?php
App::uses('CustomLabelsAppModel', 'CustomLabels.Model');

class CustomLabel extends CustomLabelsAppModel
{
	public $actsAs = [
		'Containable',
		'HtmlPurifier.HtmlPurifier' => [
			'config' => 'Strict',
			'fields' => [
				'label'
			]
		]
	];

	const TYPE_FIELD_DATA = 1;

	public function afterSave($created, $options = [])
	{
		parent::afterSave($created, $options);

		$this->clearCache();
	}

	public function afterDelete()
	{
		parent::afterSave();

		$this->clearCache();
	}

	/**
	 * Get custom labels data for given type and model.
	 *
	 * @param int $type
	 * @param string $model
	 * @return array
	 */
	public function getCusomLables($type, $model)
	{
		$cacheKey = $model . '_type_' . $type . '_list';

		if (($customLabels = Cache::read($cacheKey, 'custom_labels')) === false) {

            $customLabels = $this->find('all', [
				'conditions' => [
					'CustomLabel.type' => $type,
					'CustomLabel.model' => $model
				],
				'fields' => [
					'CustomLabel.id',
					'CustomLabel.type',
					'CustomLabel.model',
					'CustomLabel.subject',
					'CustomLabel.label',
					'CustomLabel.description',
				],
				'recursive' => -1
			]);

            Cache::write($cacheKey, $customLabels, 'custom_labels');
        }

		return $customLabels;
	}

	/**
	 * Clear custom_labes cache.
	 * 
	 * @return void
	 */
	public function clearCache()
	{
		Cache::clear(false, 'custom_labels');
	}
}
