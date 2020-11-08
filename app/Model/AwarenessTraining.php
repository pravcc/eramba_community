<?php
App::uses('AppModel', 'Model');

class AwarenessTraining extends AppModel
{
	public $actsAs = array(
		'Containable',
		'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
            'fields' => array()
        ),
	);

	public $belongsTo = array(
		'AwarenessUser',
		'AwarenessProgram' => array(
			'counterCache' => true,
			'counterScope' => array(
				'AwarenessTraining.demo' => 0
			)
		),
		'AwarenessProgramRecurrence' => array(
			'counterCache' => true,
			'counterScope' => array(
				'AwarenessTraining.demo' => 0
			)
		)
	);

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

	public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Awareness Trainings');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'awareness_user_id' => [
                'label' => __('User'),
                'editable' => false,
            ],
            'awareness_program_id' => [
                'label' => __('Awareness Program'),
                'editable' => false,
            ],
            'awareness_program_recurrence_id' => [
                'label' => __('Awareness Program Recurrence'),
                'editable' => false,
            ],
            'answers_json' => [
                'label' => __('Answers Json'),
                'editable' => false,
            ],
            'correct' => [
                'label' => __('Correct'),
                'editable' => false,
            ],
            'wrong' => [
                'label' => __('Wrong'),
                'editable' => false,
            ],
            'demo' => [
                'label' => __('Demo'),
                'editable' => false,
            ],
            'login' => [
                'label' => __('Demo'),
                'editable' => false,
            ],
        ];

        $this->advancedFilter = array(
            __('General') => array(
                'id' => array(
                    'type' => 'text',
                    'name' => __('ID'),
                    'filter' => false
                ),
                'awareness_program_id' => array(
                    'type' => 'multiple_select',
                    'name' => __('Awareness Program'),
                    'show_default' => true,
                    'filter' => array(
                        'type' => 'subquery',
                        'method' => 'findComplexType',
                        'findField' => 'AwarenessTraining.awareness_program_id',
                        'field' => 'AwarenessTraining.id',
                    ),
                    'data' => array(
                        'method' => 'getAwarenessPrograms',
                    ),
                    'contain' => array(
                        'AwarenessProgram' => array(
                            'title'
                        )
                    ),
                ),
                'login' => array(
                    'type' => 'text',
                    'name' => __('User'),
                    'show_default' => true,
                    'filter' => array(
                        'type' => 'subquery',
                        'method' => 'findComplexType',
                        'findField' => 'AwarenessUser.login',
                        'field' => 'AwarenessTraining.awareness_user_id',
                    ),
                    'contain' => array(
                    	'AwarenessUser' => array(
                    		'login'
                		)
                	)
                ),
                'demo' => array(
                    'type' => 'select',
                    'name' => __('Demo'),
                    'show_default' => true,
                    'filter' => array(
                        'type' => 'subquery',
                        'method' => 'findComplexType',
                        'findField' => 'AwarenessTraining.demo',
                        'field' => 'AwarenessTraining.id',
                    ),
                    'data' => array(
                        'method' => 'getStatusFilterOption',
                        'empty' => __('All'),
                        'result_key' => true
                    ),
                ),
                'created' => array(
                    'type' => 'date',
                    'name' => __('Date'),
                    'comparison' => true,
                    'show_default' => true,
                    'filter' => array(
                        'type' => 'subquery',
                        'method' => 'findComplexType',
                        'findField' => 'AwarenessTraining.created',
                        'field' => 'AwarenessTraining.id',
                    ),
                ),
            ),
        );

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Awarness Reminders'),
            'pdf_file_name' => __('awareness_reminders'),
            'csv_file_name' => __('awareness_reminders'),
            'actions' => false,
            'reset' => array(
                'controller' => 'awarenessPrograms',
                'action' => 'index',
            ),
            'use_new_filters' => true,
            'include_timestamps' => false,
        );

        parent::__construct($id, $table, $ds);
    }

	/**
	 * Extended deleteAll function to update counterCache correctly.
	 */
	public function deleteAll($conditions, $cascade = true, $callbacks = false){
		$deleteIds = $this->find('list', array(
			'conditions' => $conditions,
			'fields' => array('id'),
			'recursive' => -1
		));

		$keys = array();
		foreach ($deleteIds as $id) {
			$foreignData = $this->find('first', array(
				'fields' => $this->_collectForeignKeys(),
				'conditions' => array($this->alias . '.' . $this->primaryKey => $id),
				'recursive' => -1,
				'callbacks' => false
			));

			$keys[] = $foreignData;
		}

		$ret = parent::deleteAll($conditions, $cascade, $callbacks);

		foreach ($keys as $key) {
			if (!empty($key[$this->alias])) {
				$this->updateCounterCache($key[$this->alias]);
			}
		}

		return $ret;
	}

	public function findByUser($data = array(), $filter) {
		$this->AwarenessUser->Behaviors->attach('Containable', array('autoFields' => false));
		$this->AwarenessUser->Behaviors->attach('Search.Searchable');

		$query = $this->AwarenessUser->getQuery('all', array(
			'conditions' => array(
				'AwarenessUser.login LIKE' => '%' .  $data[$filter['name']] . '%'
			),
			'fields' => array(
				'AwarenessUser.id'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function getAwarenessPrograms() {
        $data = $this->AwarenessProgram->find('list', array(
            'fields' => array('AwarenessProgram.id', 'AwarenessProgram.title'),
        ));
        return $data;
    }
}
