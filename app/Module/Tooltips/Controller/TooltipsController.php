<?php
App::uses('TooltipsAppController', 'Tooltips.Controller');
App::uses('AppModule', 'Lib');
App::uses('Hash', 'Utility');

class TooltipsController extends TooltipsAppController
{
	protected $_appControllerConfig = [
		'components' => [
			'Crud.Crud' => [
				'actions' => [
					'tooltip' => [
						'enabled' => true,
						'className' => 'Tooltips.Tooltip'
					]
				]
			]
		],
		'helpers' => [
			'Html', 'Form', 'LimitlessTheme.Tooltips'
		],
		'elements' => [
		]
	];

	public $uses = [
		'Tooltips.TooltipLog'
	];

	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	public function tooltip($modelAlias, $type = 'large', $dataset = '', $id = null)
	{
		return $this->Crud->execute();
	}

	public function saveLog($modelAlias, $type, $fileId = null)
	{
		$this->autoRender = false;
		$this->autoLayout = false;

		if ($this->request->is('post')) {
			$fileId = !empty($fileId) ? $fileId : 0;
			$log = $this->TooltipLog->find('first', [
				'conditions' => [
					'user_id' => $this->Auth->user('id'),
					'model' => $modelAlias,
					'type' => $type,
					'file_id' => $fileId
				]
			]);

			if (empty($log) || $log['TooltipLog']['seen'] == 0) {
				$data = [
					'user_id' => $this->Auth->user('id'),
					'seen' => 1,
					'model' => $modelAlias,
					'type' => $type,
					'file_id' => $fileId
				];

				if (!empty($log) && $log['TooltipLog']['seen'] == 0) {
					$data['id'] = $log['TooltipLog']['id'];
				}
				
				$this->TooltipLog->save($data);
			}
		}
	}
}
