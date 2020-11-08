<?php
App::uses('AppModel', 'Model');
App::uses('AwarenessProgram', 'Model');

class AwarenessReminder extends AppModel
{
    public $displayField = 'uid';

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
        $this->label = __('Reminders');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'uid' => [
                'label' => __('Uid'),
                'editable' => false,
            ],
            'email' => [
                'label' => __('Email'),
                'editable' => false,
            ],
            'awareness_program_id' => [
                'label' => __('Awareness Program'),
                'editable' => false,
            ],
            'demo' => [
                'label' => __('Demo'),
                'editable' => false,
            ],
            'reminder_type' => [
                'label' => __('Reminder Type'),
                'editable' => false,
                'options' => [$this, 'reminderTypes']
            ],
        ];

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
                ->selectField('demo', [$this, 'getStatusFilterOption'], [
                    'label' => __('Demo'),
                    'showDefault' => true
                ])
                ->selectField('reminder_type', [$this, 'reminderTypes'], [
                    'label' => __('Type'),
                    'showDefault' => true
                ])
                ->dateField('created', [
                    'label' => __('Date'),
                    'showDefault' => true
                ])
                ->multipleSelectField('AwarenessProgram-status', [ClassRegistry::init('AwarenessProgram'), 'statuses'], [
                    'name' => __('Awareness Program Status'),
                ]);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getDisplayFilterFields()
    {
        return ['awareness_program_id', 'uid'];
    }

    public function parentModel()
    {
        return 'AwarenessProgram';
    }

    /*
	 * static enum: Model::function()
	 * @access static
	 */
	 public static function reminderTypes($value = null) {
		$options = array(
			self::REMINDER_DEFAULT => __('Default'),
			self::REMINDER_INVITATION => __('Invitation'),
			self::REMINDER_REMINDER => __('Reminder')
		);
		return parent::enum($value, $options);
	}
    // default is not used anymore, its here only for backwards compatibility
	const REMINDER_DEFAULT = 0;
	const REMINDER_INVITATION = 1;
	const REMINDER_REMINDER = 2;

    public function getReminderTypes() {
        return self::reminderTypes();
    }

    public function getAwarenessPrograms() {
        $data = $this->AwarenessProgram->find('list', array(
            'fields' => array('AwarenessProgram.id', 'AwarenessProgram.title'),
        ));
        return $data;
    }
}