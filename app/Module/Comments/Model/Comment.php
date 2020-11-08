<?php
App::uses('CommentsAppModel', 'Comments.Model');
App::uses('SidebarWidgetTrait', 'Model/Trait');

class Comment extends CommentsAppModel
{
	use SidebarWidgetTrait;

	const TYPE_NORMAL = 0;
	const TYPE_TMP = 1;

	public $displayField = 'message';

	public $actsAs = [
		'Containable',
		'HtmlPurifier.HtmlPurifier' => [
			'config' => 'Strict',
			'fields' => [
				'message',
			]
		]
	];

	public $validate = [
		'message' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a comment'
		]
	];

	public $belongsTo = [
		'User'
	];

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Comment');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'type' => [
				'label' => __('Type'),
				'editable' => false,
			],
			'hash' => [
				'label' => __('Hash'),
				'editable' => false,
			],
			'model' => [
				'label' => __('Model'),
				'editable' => false,
			],
			'foreign_key' => [
				'label' => __('Foreign Key'),
				'editable' => false,
			],
			'message' => [
				'label' => false, //__('Post a comment'),
				'editable' => true,
				'description' => __('')
			],
			'user_id' => [
				'label' => __('User'),
				'editable' => false,
			],
			'last_created' => [
				'label' => __('Last Created'),
				'editable' => false,
				'hidden' => true
			],
		];
		
		parent::__construct($id, $table, $ds);
	}

	public function beforeFind($query)
	{
		$query = $this->widgetBeforeFind($query);
		
		return $query;
	}

	public function afterSave($created, $options = array()) {
		if ($created) {
			// clear the index widget cache when added
			Cache::clearGroup('widget_data', 'widget_data');
		}

		//Project ObjectStatus trigger
		$this->triggerProjectObjectStatus($this->id);

		$this->logComment($this->id);
	}

	public function logComment($id)
	{
		$models = [
			'VendorAssessments.VendorAssessmentFeedback' => 'VendorAssessments.VendorAssessmentFeedback',
			'AccountReviews.AccountReviewFeedback' => 'AccountReviews.AccountReviewFeedback',
		];

		$data = $this->find('first', [
			'conditions' => [
				"{$this->alias}.id" => $id
			],
			'recursive' => -1
		]);

		if (empty($data) || !isset($models[$data['Comment']['model']])) {
			return false;
		}

		$Model = ClassRegistry::init($models[$data['Comment']['model']]);

		return $Model->logComment($data);
	}

	/**
	 * Triggers dependent Project statuses.
	 */
	public function triggerProjectObjectStatus($id)
	{
		$data = $this->find('first', [
			'conditions' => [
				'Comment.id' => $id
			],
			'recursive' => -1
		]);

		$triggerModels = [
			'Project', 'ProjectAchievement', 'ProjectExpense'
		];

		if (empty($data) || !in_array($data['Comment']['model'], $triggerModels)) {
			return false;
		}

		$Model = ClassRegistry::init($data['Comment']['model']);

		return $Model->triggerObjectStatus('no_updates', $data['Comment']['foreign_key']);
	}

	/**
	 * Clone comment.
	 */
	public function cloneComment($data, $foreignKey)
	{
		$this->create();
		$data = [
			'model' => $data['model'],
			'message' => $data['message'],
			'user_id' => $data['user_id'],
			'foreign_key' => $foreignKey,
		];
		return $this->save($data);
	}

	public function tmpToNormal($hash, $model, $foreignKey)
	{
		$data = [
			'type' => self::TYPE_NORMAL,
			'hash' => '""',
			'model' => '"' . $model . '"',
			'foreign_key' => $foreignKey
		];

		return $this->updateAll($data, [
			'Comment.hash' => $hash
		]);
	}

}
