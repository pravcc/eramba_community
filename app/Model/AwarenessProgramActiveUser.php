<?php
App::uses('AwarenessProgramUser', 'Model');
App::uses('AwarenessProgram', 'Model');

class AwarenessProgramActiveUser extends AwarenessProgramUser {

    public $actsAs = [
        'FieldData' => [
            'useTable' => true
        ]
    ];

    public $useTable = 'awareness_program_active_users';

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Active Users');

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
            'email' => [
                'label' => __('Email'),
                'editable' => false,
            ],
            'name' => [
                'label' => __('Users in the Program'),
                'editable' => false,
            ],
            'reminder' => [
                'label' => __('Reminders'),
                'editable' => false,
                'hidden' => true
            ],
            // 'training' => [
            //     'label' => __('Trainings'),
            //     'editable' => false,
            //     'hidden' => true
            // ],
        ];

        $this->advancedFilterSettings = [
            'pdf_title' => __('Awareness Program Active Users'),
            'pdf_file_name' => __('awareness_program_active_users'),
            'csv_file_name' => __('awareness_program_active_users'),
            'actions' => false,
            'url' => [
                'controller' => 'awarenessProgramUsers',
                'action' => 'index',
                'ActiveUser',
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

    public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id')
                ->multipleSelectField('awareness_program_id', [ClassRegistry::init('AwarenessProgram'), 'getList'], [
                    'label' => __('Awareness Program'),
                    'showDefault' => true
                ])
                ->textField('uid', [
                    'label' => __('User'),
                    'showDefault' => true
                ])
                ->textField('email', [
                    'label' => __('Email'),
                    'showDefault' => true
                ])
                ->textField('name', [
                    'label' => __('Name'),
                    'showDefault' => true
                ])
                ->multipleSelectField('AwarenessProgram-status', [ClassRegistry::init('AwarenessProgram'), 'statuses'], [
                    'name' => __('Awareness Program Status'),
                ])
                ->nonFilterableField('reminder', [
                    'showDefault' => true
                ]);

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getDisplayFilterFields()
    {
        return ['awareness_program_id', 'uid'];
    }
}