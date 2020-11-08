<?php
App::uses('AwarenessProgramUser', 'Model');
App::uses('AwarenessProgram', 'Model');

class AwarenessProgramNotCompliantUser extends AwarenessProgramUser {

    public $displayField = 'uid';

    public $actsAs = [
        'FieldData' => [
            'useTable' => true
        ]
    ];

    public $useTable = 'awareness_program_not_compliant_users';

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Not Compliant Users');

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
            'pdf_title' => __('Awareness Program Not Compliant Users'),
            'pdf_file_name' => __('awareness_program_not_compliant_users'),
            'csv_file_name' => __('awareness_program_not_compliant_users'),
            'actions' => false,
            'url' => [
                'controller' => 'awarenessProgramUsers',
                'action' => 'index',
                'NotCompliantUser',
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
}