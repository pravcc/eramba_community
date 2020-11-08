<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('GroupConnector', 'Lib/Ldap');
App::uses('AuthConnector', 'Lib/Ldap');
App::uses('Hash', 'Utility');

class AwarenessProgram extends AppModel
{
	public $displayField = 'title';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'title',
		'logRecords' => true,
		'workflow' => false,
		'notificationSystem' => array('index')
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description', 'recurrence', 'reminder_apart', 'reminder_amount', 'redirect', 'ldap_connector_id'/*, 'video', 'video_extension', 'video_mime_type', 'video_file_size', 'questionnaire'*/, 'welcome_text', 'welcome_sub_text', 'thank_you_text', 'thank_you_sub_text', 'email_subject', 'email_reminder_subject', 'email_reminder_body', 'status'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report'
			]
		],
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'ObjectStatus.ObjectStatus',
		'Visualisation.Visualisation',
		'Uploader.FileValidation' => array(
			'video' => array(
				'extension' => array('mp4'),
				// 'type' => 'video',
				// 'required' => true,
				'required' => array(
					'rule' => array('required'),
					'message' => 'At least one of the steps must be uploaded',
				)
				// 'allowEmpty' => true
			),
			'questionnaire' => array(
				'extension' => array('csv'),
				'required' => array(
					'rule' => array('required'),
					'message' => 'At least one of the steps must be uploaded',
				)
			),
			'text_file' => array(
				'extension' => array('txt', 'html'),
				'required' => array(
					'rule' => array('required'),
					'message' => 'At least one of the steps must be uploaded',
				),
			)
		),
		'Uploader.Attachment' => array(
			'video' => array(
				'nameCallback' => 'formatName',
 				'uploadDir' => AWARENESS_VIDEO_PATH,
				'dbColumn' => 'video',
				'metaColumns' => array(
					'ext' => 'video_extension',
					'type' => 'video_mime_type',
					'size' => 'video_file_size'
				)
			),
			'questionnaire' => array(
				'nameCallback' => 'formatName',
 				'uploadDir' => AWARENESS_QUESTIONNAIRE_PATH,
				'dbColumn' => 'questionnaire'
			),
			'text_file' => array(
				'nameCallback' => 'formatName',
 				'uploadDir' => AWARENESS_TEXT_FILE_PATH,
				'dbColumn' => 'text_file',
				'metaColumns' => array(
					'ext' => 'text_file_extension'
				)
			)
		),
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'recurrence' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Recurrence is required'
			),
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Please enter a number'
			),
			'min' => array(
				'rule' => array('comparison', '>=', 1),
				'message' => 'Recurrence for a program must be one or more days'
			)
		),
		'reminder_apart' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			),
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Please enter a number'
			),
			'min' => array(
				'rule' => array('comparison', '>=', 1),
				'message' => 'Please enter one or more days'
			)
		),
		'reminder_amount' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			),
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Please enter a number'
			),
			'min' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please choose how many reminders a user gets if he ignores a training'
			)
		),
		'redirect' => array(
			'rule' => ['url', true],
			'required' => true,
			'allowEmpty' => false
		),
		'ldap_connector_id' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			],
			'ldapValidation' => [
				'rule' => 'ldapValidation',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'While we pull users we check your LDAP configurations. It seems your settings are conflicting and therefore is not possible to continue. Please cancel this action and review our documentation for common LDAP issues.'
			]
		),
		'AwarenessProgramLdapGroup' => [
			'ldapGroupEmptyValidation' => [
				'rule' => 'ldapGroupEmptyValidation',
				'required' => true,
				'message' => 'This field is required'
			],
			'ldapGroupValidation' => [
				'rule' => 'ldapGroupValidation',
				'required' => true,
				'message' => 'It seems there are no user accounts for this group(s) you have selected - please use other groups or cancel this action.'
			]
		],
		'ldap_check' => array(
			'ldapCheck' => array(
				'rule' => 'ldapCheckValidation',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The LDAP check failed, you can not create awareness trainings until this control works. Please review our documentation to understand what could be wrong on your settings.'
			)
		),
		// 'AwarenessProgramLdapGroup' => array(
		// 	'rule' => array('multiple', array('min' => 1)),
		// 	'required' => true
		// ),
		'text_file' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'At least one of the steps must be uploaded'
		),
		'video' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'At least one of the steps must be uploaded'
		),
		'questionnaire' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'At least one of the steps must be uploaded'
		),
		'welcome_text' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => true
		),
		'welcome_sub_text' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => true
		),
		'thank_you_text' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => true
		),
		'thank_you_sub_text' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => true
		),
		'email_subject' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'email_body' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'email_reminder_subject' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'email_reminder_body' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
	);

	public static $fieldGroups = [
		1 => [
			'title', 'description', 'recurrence', 'reminder_amount', 'reminder_apart', 'redirect'
		],
		2 => [
			'ldap_connector_id', 'AwarenessProgramLdapGroup', 'ldap_check'
		],
		3 => [
			'SecurityPolicy', 'uploads_sort_json', 'text_file', 'text_file_frame_size', 'video', 'questionnaire'
		],
		4 => [
			'welcome_text', 'welcome_sub_text', 'thank_you_text', 'thank_you_sub_text'
		],
		5 => [
			'email_subject', 'email_body', 'email_reminder_custom'
		]
	];

	public $belongsTo = array(
		'LdapConnector'
	);

	public $hasMany = array(
		'AwarenessProgramLdapGroup',
		'AwarenessProgramActiveUser',
		'AwarenessProgramIgnoredUser',
		'AwarenessProgramCompliantUser',
		'AwarenessProgramNotCompliantUser',
		'AwarenessReminder',
		'AwarenessTraining',
		'AwarenessProgramDemo',
		'AwarenessProgramRecurrence',
		'AwarenessProgramMissedRecurrence',
	);

	public $hasAndBelongsToMany = array(
		'SecurityPolicy'
	);

	public static $uploads_sort_json = array(
		0 => array('type' => 'text-file', 'field' => 'text_file', 'path' => AWARENESS_TEXT_FILE_PATH),
		1 => array('type' => 'video-file', 'field' => 'video', 'path' => AWARENESS_VIDEO_PATH),
		2 => array('type' => 'questionnaire-file', 'field' => 'questionnaire', 'path' => AWARENESS_QUESTIONNAIRE_PATH)
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_STARTED => __('Started'),
            self::STATUS_STOPPED => __('Stopped'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_STARTED = AWARENESS_PROGRAM_STARTED;
    const STATUS_STOPPED = AWARENESS_PROGRAM_STOPPED;

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function textFileFrameSizesLabels($value = null) {
        $options = array_combine(array_keys(static::textFileFrameSizes()), Hash::extract(static::textFileFrameSizes(), '{n}.label'));
        return parent::enum($value, $options);
    }

    public static function textFileFrameSizes() {
    	return [
			1 => [
				'label' => __('Width: 940px, Height: 300px (Default)'),
				'width' => '940px',
				'height' => '300px'
			],
			2 => [
				'label' => __('Width: 50%, Height: 350px'),
				'width' => '50%',
				'height' => '350px'
			],
			3 => [
				'label' => __('Width: 60%, Height: 400px'),
				'width' => '60%',
				'height' => '400px'
			],
			4 => [
				'label' => __('Width: 70%, Height: 500px'),
				'width' => '70%',
				'height' => '500px'
			],
			5 => [
				'label' => __('Width: 80%, Height: 600px'),
				'width' => '80%',
				'height' => '600px'
			],
			6 => [
				'label' => __('Width: 90%, Height: 700px'),
				'width' => '90%',
				'height' => '700px'
			],
		];
    }

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Awareness Programs');
		$this->_group = 'security-operations';

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'uploads' => [
				'label' => __('Uploads')
			],
			'texts' => [
				'label' => __('Texts')
			],
			'email' => [
				'label' => __('Email')
			],
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'description' => __('Enter a title for this Awareness Program'),
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Enter a description for this Awareness Program'),
			],
			'recurrence' => [
				'label' => __('Recurrence'),
				'editable' => true,
				'description' => __('How often does this training need to be done. For example: 1 means every day, 2 means every second day, Etc.'),
			],
			'reminder_amount' => [
				'label' => __('Reminders Amount'),
				'editable' => true,
				'description' => __('How many times it will send a reminder email to the users who ignore the email'),
			],
			'reminder_apart' => [
				'label' => __('Reminders Apart'),
				'editable' => true,
				'description' => __('How many days in between email notifications to the user, so he does not get a daily email'),
			],
			'redirect' => [
				'label' => __('Redirect'),
				'editable' => true,
				'description' => __('Where to redirect upon completion'),
			],
			'ldap_connector_id' => [
				'label' => __('LDAP Connector'),
				'editable' => true,
				'empty' => __('Select an LDAP Connector'),
				'options' => [$this, 'getGroupConnectors'],
				'description' => __('Choose an LDAP Connector for this program'),
				'renderHelper' => ['AwarenessPrograms', 'ldapConnectorField']
			],
			'AwarenessProgramLdapGroup' => [
				'type' => 'multiple',
				'label' => __('LDAP Groups'),
				'editable' => true,
				'description' => __('Choose one or more LDAP groups'),
				'renderHelper' => ['AwarenessPrograms', 'ldapGroupField'],
				'macro' => [
					'name' => 'ldap_group'
				]
			],
			'AwarenessProgramIgnoredUser' => [
				'type' => 'multiple',
				'label' => __('LDAP Ignored Users'),
				'editable' => true,
				'description' => __('Choose one or more users that should not supposed to be bothered with the training'),
				// 'renderHelper' => ['AwarenessPrograms', 'ldapIgnoredField'],
				'macro' => [
					'name' => 'ignored_users'
				]
			],
			'ldap_check' => [
				'type' => 'select',
				'label' => __('LDAP Check'),
				'editable' => true,
				'description' => __('Select the user to check the LDAP.'),
				'macro' => false
			],
			'text_file' => [
				'type' => 'file',
				'label' => __('Text File Upload'),
				'editable' => false,
				'group' => 'uploads',
				'description' => __('Upload a plain text file (.txt) or HTML file (.html) with inline CSS of your own within the file.'),
				'renderHelper' => ['AwarenessPrograms', 'textFileField']
			],
			'text_file_extension' => [
				'label' => __('Text File Extension'),
				'editable' => false,
				'hidden' => true,
				'group' => 'uploads',
			],
			'text_file_frame_size' => [
				'label' => __('Iframe Size'),
				'editable' => true,
				'group' => 'uploads',
				'description' => __('OPTIONAL: If you upload text/ html you are allowed to adjust the width and hight of the iframe that contains that text /html.'),
				'options' => [$this, 'textFileFrameSizesLabels']
			],
			'video' => [
				'type' => 'file',
				'label' => __('Video Upload'),
				'editable' => false,
				'group' => 'uploads',
				'description' => __('or upload a video here. (mp4)'),
				'renderHelper' => ['AwarenessPrograms', 'videoField']
			],
			'video_extension' => [
				'label' => __('Video Extension'),
				'editable' => false,
				'group' => 'uploads',
				'hidden' => true,
			],
			'video_mime_type' => [
				'label' => __('Video Mime Type'),
				'editable' => false,
				'group' => 'uploads',
				'hidden' => true,
			],
			'video_file_size' => [
				'label' => __('Video File Size'),
				'editable' => false,
				'group' => 'uploads',
				'hidden' => true,
			],
			'questionnaire' => [
				'type' => 'file',
				'label' => __('Questionnaire'),
				'editable' => false,
				'group' => 'uploads',
				'description' => __('or upload a multiple choice questionnaire file here. (csv)'),
				'renderHelper' => ['AwarenessPrograms', 'questionnaireField']
			],
			'SecurityPolicy' => [
				'label' => __('Security Policies'),
				'editable' => true,
				'options' => [$this, 'getSecurityPolicies'],
				'group' => 'uploads',
				'description' => __('Choose one or more Policies to associate.'),
			],
			'uploads_sort_json' => [
				'label' => __('Uploads Sort Json'),
				'editable' => false,
				'group' => 'uploads',
				'hidden' => true,
			],
			'welcome_text' => [
				'label' => __('Welcome Header Text'),
				'editable' => true,
				'group' => 'texts',
			],
			'welcome_sub_text' => [
				'label' => __('Welcome Sub Header Text'),
				'editable' => true,
				'group' => 'texts',
			],
			'thank_you_text' => [
				'label' => __('Thank You Header Text'),
				'editable' => true,
				'group' => 'texts',
			],
			'thank_you_sub_text' => [
				'label' => __('Thank You Sub Header Text'),
				'editable' => true,
				'group' => 'texts',
			],
			'email_subject' => [
				'label' => __('Email subject'),
				'editable' => true,
				'email' => 'email',
				'usable' => false,
				'default' => __('Awareness Training Invite')
			],
			'email_body' => [
				'label' => __('Email body'),
				'editable' => true,
				'email' => 'email',
				'usable' => false,
				'renderHelper' => ['AwarenessPrograms', 'emailBodyField'],
				'default' => __('Hello,
<br>
<br>
Please join us in completing this awareness training "%AWARENESSPROGRAM_TITLE%" by clicking on the url below and login using your company credentials.

Bare in mind this training is mandatory and must be completed every %AWARENESSPROGRAM_RECURRENCE%, you might receive reminders in case you forget to complete it on time.
<br>
<br>
Regards'),
			],
			'email_reminder_custom' => [
				'label' => __('Check to customize Reminder email'),
				'type' => 'toggle',
				'editable' => true,
				'email' => 'email',
				'renderHelper' => ['AwarenessPrograms', 'emailReminderCustomField'],
				'usable' => false
			],
			'email_reminder_subject' => [
				'label' => __('Reminder Email subject'),
				'editable' => true,
				'email' => 'email',
				'usable' => false,
				'default' => __('Awareness Training Reminder')
			],
			'email_reminder_body' => [
				'label' => __('Reminder Email body'),
				'editable' => true,
				'email' => 'email',
				'usable' => false,
				'renderHelper' => ['AwarenessPrograms', 'emailReminderBodyField'],
				'default' => __('Hello,
<br>
<br>
This is a reminder that you seem to be missing on of our mandatory awareness trainings.

Please join us in completing this awareness training "%AWARENESSPROGRAM_TITLE%" by clicking on the url below and login using your company credentials.

Bare in mind this training is mandatory and must be completed every %AWARENESSPROGRAM_RECURRENCE%, you might receive reminders in case you forget to complete it on time.
<br>
<br>
Regards')
			],
			'status' => [
				'label' => __('Status'),
				'option' => [$this, 'statuses'],
				'editable' => false,
			],
			'awareness_training_count' => [
				'label' => __('Number of Completed Trainings'),
				'editable' => false,
			],
			'active_users' => [
				'label' => __('Active Users'),
				'editable' => false,
				'macro' => [
					'name' => 'active_users_count'
				]
			],
			'active_users_percentage' => [
				'label' => __('Active Users Percentage'),
				'editable' => false,
			],
			'ignored_users' => [
				'label' => __('Ignored Users'),
				'editable' => false,
				'macro' => [
					'name' => 'ignored_users_count'
				]
			],
			'ignored_users_percentage' => [
				'label' => __('Ignored Users Percentage'),
				'editable' => false,
			],
			'compliant_users' => [
				'label' => __('Compliant Users'),
				'editable' => false,
				'macro' => [
					'name' => 'compliant_users_count'
				]
			],
			'compliant_users_percentage' => [
				'label' => __('Compliant Users Percentage'),
				'editable' => false,
			],
			'not_compliant_users' => [
				'label' => __('Not Compliant Users'),
				'editable' => false,
				'macro' => [
					'name' => 'not_compliant_users_count'
				]
			],
			'not_compliant_users_percentage' => [
				'label' => __('Not Compliant Users Percentage'),
				'editable' => false,
			],
			'stats_update_status' => [
				'label' => __('Status Update Status'),
				'editable' => false,
				'hidden' => true
			],
			'AwarenessProgramActiveUser' => [
				'label' => __('Active Users'),
				'editable' => false,
				'macro' => [
					'name' => 'active_users'
				]
			],
			'AwarenessProgramCompliantUser' => [
				'label' => __('Compliant Users'),
				'editable' => false,
				'macro' => [
					'name' => 'compliant_users'
				]
			],
			'AwarenessProgramNotCompliantUser' => [
				'label' => __('Not Compliant Users'),
				'editable' => false,
				'macro' => [
					'name' => 'not_compliant_users'
				]
			],
		];

		$this->notificationSystem = array(
			'macros' => array(
				'AWARENESSPROGRAM_ID' => array(
					'field' => 'AwarenessProgram.id',
					'name' => __('Awareness Program ID')
				),
				'AWARENESSPROGRAM_TITLE' => array(
					'field' => 'AwarenessProgram.title',
					'name' => __('Awareness Program Title')
				),
				'AWARENESSPROGRAM_DESCRIPTION' => array(
					'field' => 'AwarenessProgram.description',
					'name' => __('Awareness Program Description')
				),
				'AWARENESSPROGRAM_RECURRENCE' => array(
					'field' => 'AwarenessProgram.recurrence',
					'name' => __('Awareness Program Recurrence')
				),
				'AWARENESSPROGRAM_REMINDER_APART' => array(
					'field' => 'AwarenessProgram.reminder_apart',
					'name' => __('Awareness Program Reminders Apart')
				),
				'AWARENESSPROGRAM_REMINDER_AMOUNT' => array(
					'field' => 'AwarenessProgram.reminder_amount',
					'name' => __('Awareness Program Reminders Amount')
				),
				
				'AWARENESSPROGRAM_USERS' => array(
					'field' => 'AwarenessProgramActiveUser.{n}.uid',
					'name' => __('Awareness Program Active Users')
				),
				'AWARENESSPROGRAM_USER_EMAILS' => array(
					'field' => 'AwarenessProgramActiveUser.{n}.email',
					'name' => __('Awareness Program Active User Emails')
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Awareness Program'),
			'pdf_file_name' => __('awareness_program'),
			'csv_file_name' => __('awareness_program'),
			'additional_actions' => array(
				'AwarenessProgramActiveUser' => array(
					'label' => __('Active Users'),
					'url' => array(
						'controller' => 'awarenessProgramUsers',
						'action' => 'index',
						'ActiveUser',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
				'AwarenessProgramIgnoredUser' => array(
					'label' => __('Ignored Users'),
					'url' => array(
						'controller' => 'awarenessProgramUsers',
						'action' => 'index',
						'IgnoredUser',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
				'AwarenessProgramCompliantUser' => array(
					'label' => __('Compliant Users'),
					'url' => array(
						'controller' => 'awarenessProgramUsers',
						'action' => 'index',
						'CompliantUser',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
				'AwarenessProgramNotCompliantUser' => array(
					'label' => __('Not Compliant Users'),
					'url' => array(
						'controller' => 'awarenessProgramUsers',
						'action' => 'index',
						'NotCompliantUser',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
				'AwarenessReminder' => array(
					'label' => __('Awareness Reminders'),
					'url' => array(
						'controller' => 'awarenessReminders',
						'action' => 'index',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
				'AwarenessTraining' => array(
					'label' => __('Awareness Trainings'),
					'url' => array(
						'controller' => 'awarenessTrainings',
						'action' => 'index',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
			),
			'history' => true,
            'trash' => true,
			'use_new_filters' => true,
			'add-no-ajax' => true,
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
				->textField('title', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->numberField('recurrence', [
					'showDefault' => true
				])
				->numberField('reminder_apart', [
					'showDefault' => true
				])
				->numberField('reminder_amount', [
					'showDefault' => true
				])
				->numberField('awareness_training_count')
				->multipleSelectField('ldap_connector_id', [ClassRegistry::init('LdapConnector'), 'getList'], [
					'label' => __('LDAP Connector'),
				])
				->textField('video', [
					'label' => __('Video'),
				])
				->textField('questionnaire', [
					'label' => __('Questionnaire'),
				])
				->textField('text_file', [
					'label' => __('Text File'),
				])
			->group('compliance', [
				'name' => __('Compliance')
			])
				->numberField('active_users', [
					'label' => __('Users in the Program (Count)'),
					'showDefault' => true
				])
				->numberField('ignored_users', [
					'label' => __('Excluded Users (Count)'),
					'showDefault' => true
				])
				->numberField('compliant_users', [
					'label' => __('Compliant Users (Count)'),
					'showDefault' => true
				])
				->numberField('not_compliant_users', [
					'label' => __('Non Compliant Users (Count)'),
					'showDefault' => true
				])
				->numberField('active_users_percentage', [
					'label' => __('Users in the Program (Percentage)'),
				])
				->numberField('ignored_users_percentage', [
					'label' => __('Excluded Users (Percentage)'),
				])
				->numberField('compliant_users_percentage', [
					'label' => __('Compliant Users (Percentage)'),
				])
				->numberField('not_compliant_users_percentage', [
					'label' => __('Non Compliant Users (Percentage)'),
				]);

		$this->SecurityPolicy->relatedFilters($advancedFilterConfig)
			->field('SecurityPolicy', ['showDefault' => true]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getSectionInfoConfig()
    {
	    return [
            'map' => [
                'SecurityPolicy'
            ]
	    ];
    }

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function getReportsConfig()
    {
        return [
        	 'finder' => [
                'options' => [
                    'contain' => [
                    	'LdapConnector',
                    	'AwarenessProgramLdapGroup',
                    	'AwarenessProgramActiveUser' => [
                    		'AwarenessProgram'
                		],
                    	'AwarenessProgramIgnoredUser' => [
                    		'AwarenessProgram'
                		],
                    	'AwarenessProgramCompliantUser' => [
                    		'AwarenessProgram'
                		],
                    	'AwarenessProgramNotCompliantUser' => [
                    		'AwarenessProgram'
                		],
                    	'AwarenessReminder' => [
                    		'AwarenessProgram'
                		],
                    	'AwarenessTraining' => [
                    		'AwarenessProgram',
                    		'AwarenessProgramRecurrence',
                    		'AwarenessUser'
                    	],
                    	'AwarenessProgramDemo',
                    	'AwarenessProgramRecurrence' => [
                    		'AwarenessProgram',
							'AwarenessTraining',
							'AwarenessProgramMissedRecurrence'
                    	],
                    	'AwarenessProgramMissedRecurrence' => [
                    		'AwarenessProgramRecurrence',
                    		'AwarenessProgram'
                    	],
                    	'SecurityPolicy'
                    ]
                ]
            ],
        	'table' => [
				'model' => [
					'AwarenessProgramActiveUser', 'AwarenessProgramIgnoredUser', 'AwarenessProgramCompliantUser', 'AwarenessProgramNotCompliantUser',
				]
			],
            'chart' => [
            	1 => [
					'title' => __('Awareness Compliance Over Time'),
					'description' => __('This chart shows all configured and started awareness programs and their respective percentage of compliance calculated as: [ compliant users / ( total participants - excluded users ) ] * 100.'),
					'type' => ReportBlockChartSetting::TYPE_LINE,
					'templateType' => ReportTemplate::TYPE_SECTION,
					'dataFn' => 'awarenessComplianceOverTimeChart'
				],
				2 => [
					'title' => __('Compliance Over Time'),
					'description' => __('This chart shows the number of participants and compliant users for this awareness program.'),
					'type' => ReportBlockChartSetting::TYPE_LINE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'awarenessComplianceOverTimeChart'
				]
            ]
        ];
    }

    public function reminderMacros($Collection, $values = [])
	{
		$Collection->add(new Macro($this->getMacroAlias('user_uid'), __('User UID'), null, $values['user_uid']));
		$Collection->add(new Macro($this->getMacroAlias('user_email'), __('User Email'), null, $values['user_email']));
		$Collection->add(new Macro($this->getMacroAlias('user_name'), __('User Name'), null, $values['user_name']));
	}

	public function beforeSave($options = array())
	{
		if (isset($this->data['AwarenessProgramLdapGroup']) && !empty($this->data['AwarenessProgram']['id'])) {
			$this->AwarenessProgramLdapGroup->deleteAll(
				['AwarenessProgramLdapGroup.awareness_program_id' => $this->data['AwarenessProgram']['id']
			]);
		}
		if (isset($this->data['AwarenessProgramIgnoredUser']) && !empty($this->data['AwarenessProgram']['id'])) {
			$this->AwarenessProgramIgnoredUser->deleteAll(
				['AwarenessProgramIgnoredUser.awareness_program_id' => $this->data['AwarenessProgram']['id']
			]);
		}

		return true;
	}

	public function afterSave($created, $options = array()) {
		// parent::afterSave($created, $options);

		// if (isset($this->data['AwarenessProgram']['ldap_groups'])) {
		// 	$this->AwarenessProgramLdapGroup->deleteAll(['AwarenessProgramLdapGroup.awareness_program_id' => $this->id]);
		// 	$this->joinLdapGroups($this->data['AwarenessProgram']['ldap_groups'], $this->id);
		// }
		// if (isset($this->data['AwarenessProgram']['ignored_users_uid'])) {
		// 	$this->AwarenessProgramIgnoredUser->deleteAll(['AwarenessProgramIgnoredUser.awareness_program_id' => $this->id]);
		// 	$this->joinLdapIgnoredUsers($this->data['AwarenessProgram']['ignored_users_uid'], $this->id);
		// }
	}

	public function getObjectStatusConfig() {
		return [
			'started' => [
                'title' => __('Started'),
                'type' => 'success',
                'callback' => [$this, 'statusStarted'],
                'storageSelf' => false
            ],
            'stopped' => [
                'title' => __('Stopped'),
                'type' => 'danger',
                'callback' => [$this, 'statusStopped'],
                'storageSelf' => false
            ],
		];
	}

    public function statusStarted() {
    	$data = $this->find('count', [
			'conditions' => [
				'AwarenessProgram.id' => $this->id,
				'AwarenessProgram.status' => self::STATUS_STARTED,
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusStopped() {
    	$data = $this->find('count', [
			'conditions' => [
				'AwarenessProgram.id' => $this->id,
				'AwarenessProgram.status' => self::STATUS_STOPPED
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

	public function nullComparison($data = array(), $filter) {
		$field = $filter['field'];
		if ($data[$filter['name']]) {
			$query[] = $field . ' IS NOT NULL';
		}
		else {
			$query[] = $field . ' IS NULL';
		}
		return $query;
	}

	// questionaire was missing null
	public function emptyQuestionnaireComparison($data = array(), $filter) {
		$field = $filter['field'];
		if ($data[$filter['name']]) {
			$query[] = $field . ' IS NOT NULL';
			$query[] = $field . ' != \'\'';
		}
		else {
			$query[] = array($field => array(null, ''));
		}
		return $query;
	}

	public function getStatuses() {
		return array(
			AWARENESS_PROGRAM_STARTED => __('Started'),
			AWARENESS_PROGRAM_STOPPED => __('Stopped')
		);
	}

	public function getAwarenessTrainings() {
		$data = $this->AwarenessTraining->find('list', array(
			'fields' => array('AwarenessTraining.id'),
		));
		return $data;
	}

	public function getLdapConnectors() {
		$data = $this->LdapConnector->find('list', array(
			'fields' => array('LdapConnector.name'),
			'order' => array('LdapConnector.name' => 'ASC'),
		));
		return $data;
	}

	public function getAwarenessPrograms() {
		$data = $this->find('list', array(
			'fields' => array('AwarenessProgram.id', 'AwarenessProgram.title'),
		));
		return $data;
	}

	public function getSecurityPolicies() {
		return $this->SecurityPolicy->getListWithType();
	}

	public function ldapGroupEmptyValidation($check)
	{
		return !empty($this->data['AwarenessProgram']['AwarenessProgramLdapGroup']);
	}

	public function ldapGroupValidation($check)
	{
		if (empty($this->data['AwarenessProgram']['ldap_connector_id']) || empty($this->data['AwarenessProgram']['AwarenessProgramLdapGroup'])) {
			return false;
		}

		$GroupConnector = $this->getConnector($this->data['AwarenessProgram']['ldap_connector_id']);

		if ($GroupConnector === false || $GroupConnector->connect() !== true) {
			return false;
		}

		$groups = Hash::extract($this->data['AwarenessProgram']['AwarenessProgramLdapGroup'], '{n}.name');

		return !empty($GroupConnector->getMemberList($groups));
	}

	public function ldapValidation($check)
	{
		if (empty($this->data['AwarenessProgram']['ldap_connector_id'])) {
			return false;
		}

		$GroupConnector = $this->getConnector($this->data['AwarenessProgram']['ldap_connector_id']);

		if ($GroupConnector === false) {
			return false;
		}

		return ($GroupConnector->connect() === true);
	}

	public function getConnector($ldapId)
	{
		$connector = ClassRegistry::init('LdapConnector')->find('first', [
			'conditions' => [
				'LdapConnector.id' => $ldapId
			],
			'recursive' => -1
		]);

		if (empty($connector)) {
			return false;
		}

		return new GroupConnector($connector['LdapConnector']);
	}

	public function ldapCheckValidation($check)
	{
		if (empty($this->data['AwarenessProgram']['AwarenessProgramLdapGroup'])
			|| empty($this->data['AwarenessProgram']['ldap_check'])
		) {
			return false;
		}

		$ldapAuth = ClassRegistry::init('LdapConnectorAuthentication')->getAuthData();

		$AuthConnector = new AuthConnector($ldapAuth['AuthAwareness']);
		$ldapConnection = $AuthConnector->connect();
		
		App::uses('LDAPAuthenticate', 'Controller/Component/Auth');
		$groups = LDAPAuthenticate::getUserGroupsList($this->data['AwarenessProgram']['ldap_check'], $AuthConnector);
		$hasMatch = array_intersect($groups, Hash::extract($this->data['AwarenessProgram']['AwarenessProgramLdapGroup'], '{n}.name'));

		return !empty($hasMatch);
	}

	public function beforeValidate($options = array()) {
		$requestData = $this->data['AwarenessProgram'];

		// check minimum of 1 upload required
		$minUploads = false;
		foreach ($this->getUploadsSorting() as $upload) {
			if (!empty($requestData[$upload['field']]['tmp_name'])) {
				$minUploads = true;
			}
		}

		//check if we are in edit action and file is already uploaded
		if (!empty($this->data['AwarenessProgram']['id'])) {
			$hasFile = $this->find('count', [
				'conditions' => [
					'AwarenessProgram.id' => $this->data['AwarenessProgram']['id'],
					'OR' => [
						[
							'AwarenessProgram.text_file != ""',
							'AwarenessProgram.text_file IS NOT NULL',
						],
						[
							'AwarenessProgram.video != ""',
							'AwarenessProgram.video IS NOT NULL',
						],
						[
							'AwarenessProgram.questionnaire != ""',
							'AwarenessProgram.questionnaire IS NOT NULL',
						],
					]
				]
			]);

			if ($hasFile) {
				$minUploads = true;
			}
		}

		if (empty($requestData['email_reminder_custom'])) {
			$this->validator()->remove('email_reminder_subject');
			$this->validator()->remove('email_reminder_body');
		}

		if ($minUploads) {
			foreach ($this->getUploadsSorting() as $upload) {
				$this->validator()->remove($upload['field'], 'required');
				$this->validator()->remove($upload['field'], 'notBlank');
			}
		}

		if (isset($requestData['SecurityPolicy'])) {
			if (!$this->checkRelatedExists('SecurityPolicy', $this->data['AwarenessProgram']['SecurityPolicy'])) {
				$this->invalidate('SecurityPolicy', __('At least one of the selected items does not exist.'));
			}
		}

		// if creatig a new program, recurrence value needs to be higher than the number of reminders * their frequency (reminder_amount*reminder_apart)
		$condCheck = !empty($requestData['recurrence']);
		$condCheck &= !empty($requestData['reminder_amount']);
		$condCheck &= !empty($requestData['reminder_apart']);

		if ($condCheck) {
			$remindersValidate = $requestData['recurrence'] > ($requestData['reminder_amount'] * $requestData['reminder_apart']);
			if (empty($requestData['id']) && !$remindersValidate) {
				$this->invalidate('recurrence', __('The multiplication of the number of reminders and their reminders apart setting can not be equal or higher to the recurrence of this training. This control is meant to avoid confusing users with emails.'));
			}
		}

		return true;
	}

	public function afterValidate() {
		parent::afterValidate();

		// if (!empty($this->validationErrors['AwarenessProgramLdapGroup'])) {
		// 	$this->invalidate('ldap_connector_id', __('LDAP Groups can not be empty'));
		// }
	}

	public function formatName($name, $file) {
		return Inflector::slug($name, '-');
	}

	public function getIgnoredUsers($id) {
		return $this->AwarenessProgramIgnoredUser->find('list', array(
			'conditions' => array(
				'AwarenessProgramIgnoredUser.awareness_program_id' => $id
			),
			'fields' => array('AwarenessProgramIgnoredUser.uid'),
			'recursive' => -1
		));
	}

	public function getActiveUsers($id) {
		return $this->AwarenessProgramActiveUser->find('list', array(
			'conditions' => array(
				'AwarenessProgramActiveUser.awareness_program_id' => $id
			),
			'fields' => array('AwarenessProgramActiveUser.uid'),
			'recursive' => -1
		));
	}

	/**
	 * Get all, both ignored and active users together for Awareness Program.
	 * This is Database-driven version of an LDAP method @see AwarenessMgtComponent::getProgramLdapUsers().
	 */
	public function getAllUsers($id) {
		$active = $this->getActiveUsers($id);
		$allUsers = am($this->getIgnoredUsers($id), $active);
		
		return array_unique($allUsers);
	}

	/**
	 * Array of Awareness Program statistics shown on the index.
	 *
	 * @param int $id Awareness Program ID.
	 * @param mixed $allUsers Array of all program users. Set null to get this inside the method.
	 */
	public function getProgramStats($id, $allUsers = null) {
		if ($allUsers === null) {
			$allUsers = $this->getAllUsers($id);

			if (empty($allUsers)) {
				return false;
			}
		}

		$ignoredUsers = $this->getIgnoredUsers($id);

		$ignoredUsersPercentageValue = count($ignoredUsers)/count($allUsers);
		$ignoredUsersPercentage = CakeNumber::toPercentage($ignoredUsersPercentageValue, 0, array('multiply' => true));

		$activeUsers = array_diff($allUsers, $ignoredUsers);

		$activeUsersPercentageValue = count($activeUsers)/count($allUsers);
		$activeUsersPercentage = CakeNumber::toPercentage($activeUsersPercentageValue, 0, array('multiply' => true));

		$recurrenceData = $this->AwarenessProgramRecurrence->find('first', array(
			'conditions' => array(
				'AwarenessProgramRecurrence.awareness_program_id' => $id,
				// 'AwarenessTraining.demo' => 0
			),
			'fields' => array(
				// 'AwarenessProgramRecurrence.id',
				// 'AwarenessTraining.id'
			),
			'order' => array('AwarenessProgramRecurrence.start' => 'DESC'),
			'recursive' => -1
		));

		$trainings = array();
		$currentRecurrenceStart = null;
		if (!empty($recurrenceData)) {
			$currentRecurrenceStart = $recurrenceData['AwarenessProgramRecurrence']['start'];

			$trainings = $this->AwarenessTraining->find('all', array(
				'conditions' => array(
					'AwarenessTraining.awareness_program_recurrence_id' => $recurrenceData['AwarenessProgramRecurrence']['id'],
					'AwarenessProgramActiveUser.uid' => $activeUsers,
					'AwarenessTraining.demo' => 0
				),
				'group' => array('AwarenessProgramActiveUser.uid'),
				'joins' => array(
					array(
						'table' => 'awareness_program_active_users',
						'alias' => 'AwarenessProgramActiveUser',
						'type' => 'LEFT',
						'conditions' => array(
							'AwarenessUser.login = AwarenessProgramActiveUser.uid'
						)
					)
				),
				'fields' => array(
					'AwarenessProgramActiveUser.uid'
				),
				'recursive' => 0
			));
		}
		
		$compliantUsers = $notCompliantUsers = $usersWithTraining = array();
		if (!empty($trainings)) {
			foreach ($trainings as $training) {
				//log users having at least some training records
				$usersWithTraining[] = $training['AwarenessProgramActiveUser']['uid'];
				$compliantUsers[] = $training['AwarenessProgramActiveUser']['uid'];
			}			
		}
		else {
		}

		// if some users does not have any training recorded, that means they are non compliant
		$usersWithoutTraining = array_diff($activeUsers, $usersWithTraining);
		$notCompliantUsers = array_merge($notCompliantUsers, $usersWithoutTraining);

		$compliantUsersPercentageValue = 0;
		$notCompliantUsersPercentageValue = 0;
		if (count($activeUsers) != 0) {
			$compliantUsersPercentageValue = count($compliantUsers)/count($activeUsers);
			$notCompliantUsersPercentageValue = count($notCompliantUsers)/count($activeUsers);
		}

		$compliantUsersPercentage = CakeNumber::toPercentage($compliantUsersPercentageValue, 0, array(
			'multiply' => true
		));

		$notCompliantUsersPercentage = CakeNumber::toPercentage($notCompliantUsersPercentageValue, 0, array(
			'multiply' => true
		));

		return array(
			'activeUsers' => $activeUsers,
			'activeUsersCount' => count($activeUsers),
			'activeUsersPercentageValue' => $activeUsersPercentageValue*100,
			'activeUsersPercentage' => $activeUsersPercentage,

			'ignoredUsers' => $ignoredUsers,
			'ignoredUsersCount' => count($ignoredUsers),
			'ignoredUsersPercentageValue' => $ignoredUsersPercentageValue*100,
			'ignoredUsersPercentage' => $ignoredUsersPercentage,

			'compliantUsers' => $compliantUsers,
			'compliantUsersCount' => count($compliantUsers),
			'doneRecurrences' => $this->getDoneRecurrences($id, $compliantUsers, $currentRecurrenceStart),

			'notCompliantUsers' => $notCompliantUsers,
			'notCompliantUsersCount' => count($notCompliantUsers),
			'missedRecurrences' => $this->getMissedRecurrences($id, $notCompliantUsers, $currentRecurrenceStart),

			'compliantUsersPercentageValue' => $compliantUsersPercentageValue*100,
			'compliantUsersPercentage' => $compliantUsersPercentage,

			'notCompliantUsersPercentageValue' => $notCompliantUsersPercentageValue*100,
			'notCompliantUsersPercentage' => $notCompliantUsersPercentage
		);
	}

	/**
	 * Get data of finished recurrence trainings of users.
	 * 
	 */
	private function getDoneRecurrences($awarenessProgramId, $users = array(), $currentRecurrence = null) {
		$doneRecurrenceDates = $this->AwarenessTraining->find('all', array(
			'conditions' => array(
				'AwarenessUser.login' => $users,
				'AwarenessTraining.awareness_program_id' => $awarenessProgramId
			),
			'fields' => array(
				'AwarenessProgramRecurrence.start',
				'AwarenessUser.login',
			),
			'order' => array('AwarenessProgramRecurrence.start' => 'DESC'),
			'recursive' => 0,
			// 'limit' => 3
		));

		$doneRecurrences = array();
		/*if ($currentRecurrence !== null) {
			foreach ($users as $uid) {
				$doneRecurrences[$uid][] = $currentRecurrence;
			}
		}*/

		foreach ($doneRecurrenceDates as $item) {
			$start = $item['AwarenessProgramRecurrence']['start'];
			$uid = $item['AwarenessUser']['login'];

			// if (empty($doneRecurrences[$uid]) && !empty($currentRecurrence)) {
			// 	$doneRecurrences[$uid][] = $currentRecurrence;
			// }

			if (empty($doneRecurrences[$uid]) || !in_array($start, $doneRecurrences[$uid])) {
				$doneRecurrences[$uid][] = $start;
			}
		}

		foreach ($doneRecurrences as $uid => $dates) {
			// $doneRecurrences[$uid] = array_unique($doneRecurrences[$uid]);
		}

		return $doneRecurrences;
	}

	/**
	 * Get data of missing recurrence trainings of users.
	 * 
	 * @param  string $currentRecurrence  To fill in the current actual recurrence not yet available in database.
	 */
	private function getMissedRecurrences($awarenessProgramId, $users = array(), $currentRecurrence = null) {
		$missedRecurrencesDates = $this->AwarenessProgramMissedRecurrence->find('all', array(
			'conditions' => array(
				'AwarenessProgramMissedRecurrence.uid' => $users,
				'AwarenessProgramMissedRecurrence.awareness_program_id' => $awarenessProgramId
			),
			'fields' => array(
				'AwarenessProgramRecurrence.start',
				'AwarenessProgramMissedRecurrence.uid'
			),
			'order' => array('AwarenessProgramRecurrence.start' => 'DESC'),
			'recursive' => 0
		));

		$missedRecurrences = array();
		if ($currentRecurrence !== null) {
			foreach ($users as $uid) {
				$missedRecurrences[$uid][] = $currentRecurrence;
			}
		}

		foreach ($missedRecurrencesDates as $item) {
			$start = $item['AwarenessProgramRecurrence']['start'];
			$uid = $item['AwarenessProgramMissedRecurrence']['uid'];

			if (!in_array($start, $missedRecurrences[$uid])) {
				$missedRecurrences[$uid][] = $start;
			}
		}

		foreach ($missedRecurrences as $uid => $dates) {
			$missedRecurrences[$uid] = array_unique($missedRecurrences[$uid]);
		}

		return $missedRecurrences;
	}

	/**
	 * Get order of program steps defined by user, or default.
	 */
	public function getUploadsSorting($id = null) {
		$json = false;
		if (!empty($id)) {
			$data = $this->find('first', array(
				'conditions' => array(
					'AwarenessProgram.id' => $id
				),
				'fields' => array('AwarenessProgram.uploads_sort_json'),
				'recursive' => -1
			));

			$json = $data['AwarenessProgram']['uploads_sort_json'];
		}

		$sort = array();
		if (!empty($json)) {
			$sort = json_decode($json, true);
		}

		return self::processUploadSorting($sort);
	}

	public static function processUploadSorting($arr = array()) {
		if (empty($arr)) {
			$arr = self::$uploads_sort_json;
		}

		ksort($arr);
		return $arr;
	}

	public function getGroupConnectors() {
		$ldapConnectors = $this->LdapConnector->find('list', array(
			'conditions' => [
				'LdapConnector.type' => 'group'
			],
			'order' => ['LdapConnector.name' => 'ASC']
		));

		return $ldapConnectors;
	}

	public function deleteTextFile($id)
	{
		return $this->deleteFiles($id, ['text_file']);
	}

	public function deleteQuestionnaire($id)
	{
		return $this->deleteFiles($id, ['questionnaire']);
	}

	public function deleteVideo($id)
	{
		return $this->deleteFiles($id, ['video']);
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
