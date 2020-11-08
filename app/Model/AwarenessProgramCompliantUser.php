<?php
App::uses('AwarenessProgramUser', 'Model');
App::uses('AwarenessProgram', 'Model');

class AwarenessProgramCompliantUser extends AwarenessProgramUser {

    public $displayField = 'uid';

    public $actsAs = [
        'FieldData' => [
            'useTable' => true
        ]
    ];

    public $useTable = 'awareness_program_compliant_users';

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Compliant Users');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'awareness_program_id' => [
                'label' => __('Awareness Program'),
                'editable' => false,
            ],
            'uid' => [
                'label' => __('Uid'),
                'editable' => false,
            ],
            'reminder' => [
                'label' => __('Reminders'),
                'editable' => false,
                'hidden' => true
            ],
        ];

        $this->advancedFilterSettings = [
            'pdf_title' => __('Awareness Program Compliant Users'),
            'pdf_file_name' => __('awareness_program_compliant_users'),
            'csv_file_name' => __('awareness_program_compliant_users'),
            'actions' => false,
            'url' => [
                'controller' => 'awarenessProgramUsers',
                'action' => 'index',
                'CompliantUser',
                '?' => [
                    'advanced_filter' => 1
                ]
            ],
            'reset' => [
                'controller' => 'awarenessPrograms',
                'action' => 'index',
            ]
        ];

        parent::__construct($id, $table, $ds);
    }

    public function getDisplayFilterFields()
    {
        return ['awareness_program_id', 'uid'];
    }

    /*public function beforeFind($query) {
        $this->virtualFields =array(
            'last_compliant_date' => 'AwarenessTraining.created'
        );
        $query['joins'] = array(
            array(
                'table' => 'awareness_users',
                'alias' => 'AwarenessUser',
                'type' => 'LEFT',
                'conditions' => array(
                    'AwarenessUser.login = AwarenessProgramCompliantUser.uid'
                )
            ),
            array(
                'table' => 'awareness_program_recurrences',
                'alias' => 'AwarenessProgramRecurrence',
                'type' => 'LEFT',
                'conditions' => array(
                    'AwarenessProgramRecurrence.awareness_program_id = AwarenessProgramCompliantUser.awareness_program_id'
                )
            ),
            array(
                'table' => 'awareness_trainings',
                'alias' => 'AwarenessTraining',
                'type' => 'LEFT',
                'conditions' => array(
                    'AwarenessTraining.awareness_user_id = AwarenessUser.id',
                    'AwarenessTraining.demo = 0',
                    'AwarenessProgramCompliantUser.awareness_program_id = AwarenessTraining.awareness_program_id',
                    'AwarenessTraining.awareness_program_recurrence_id = AwarenessProgramRecurrence.id'
                )
            )
        );

        $query['group'] = 'AwarenessProgramCompliantUser.id';
        $query['order']['AwarenessProgramRecurrence.start'] = 'DESC';
        $query['order']['AwarenessProgramCompliantUser.last_compliant_date'] = 'DESC';
        $query['order']['AwarenessTraining.created'] = 'DESC';
        // debug($query);
        
        return $query;
    }*/
}