<?php
App::uses('AppModel', 'Model');

class AwarenessProgramUser extends AppModel
{
    public $useTable = false;
    // public $mapController = 'awarenessPrograms';

    public $belongsTo = array(
        'AwarenessProgram'
    );

    public $actsAs = array(
        'Containable',
        'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
            'fields' => array()
        ),
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report'
            ]
        ],
        'FieldData' => [
            'useTable' => false
        ],
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'AdvancedFilters.AdvancedFilters'
    );

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public function __construct($id = false, $table = null, $ds = null) {
        if (empty($this->label)) {
            $this->label = __('Awareness Program Users');
        }

        // $this->fieldGroupData = [
        //     'default' => [
        //         'label' => __('General')
        //     ],
        // ];

        // $fieldData = [
        //     'awareness_program_id' => [
        //         'label' => __('Awareness Program'),
        //         'editable' => false,
        //     ],
        //     'uid' => [
        //         'label' => __('Uid'),
        //         'editable' => false,
        //     ],
        // ];

        // $this->fieldData = (!empty($this->fieldData)) ? array_merge($fieldData, $this->fieldData) : $fieldData;

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
                ->multipleSelectField('AwarenessProgram-status', [ClassRegistry::init('AwarenessProgram'), 'statuses'], [
                    'name' => __('Awareness Program Status'),
                ])
                ->nonFilterableField('reminder', [
                    'showDefault' => true
                ]);

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function parentModel()
    {
        return 'AwarenessProgram';
    }

    public function getAwarenessPrograms() {
        $data = $this->AwarenessProgram->find('list', array(
            'fields' => array('AwarenessProgram.id', 'AwarenessProgram.title'),
        ));
        return $data;
    }
}