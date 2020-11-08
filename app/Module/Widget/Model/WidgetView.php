<?php
App::uses('WidgetAppModel', 'Widget.Model');

class WidgetView extends WidgetAppModel
{
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $actsAs = [
	];

	public $validate = [
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Widget Views');

		parent::__construct($id, $table, $ds);
	}

	public function createOrUpdateView($model, $foreignKey, $userId, $widgetView, $commentsView = null, $attachmentsView = null)
	{
		$item = $this->find('first', [
			'conditions' => [
				'WidgetView.model' => $model,
				'WidgetView.foreign_key' => $foreignKey,
				'WidgetView.user_id' => $userId,
			],
			'fields' => ['WidgetView.id'],
			'recursive' => -1,
		]);

		$data = [
			'widget_view' => $widgetView,
		];
		
		if ($commentsView) {
			$data['comments_view'] = $commentsView;
		}

		if ($attachmentsView) {
			$data['attachments_view'] = $attachmentsView;
		}

		if (empty($item)) {
			$data['model'] = $model;
			$data['foreign_key'] = $foreignKey;
			$data['user_id'] = $userId;
		}
		else {
			$data['id'] = $item['WidgetView']['id'];
		}

		$this->create();
		return $this->save(['WidgetView' => $data]);
	}
}
