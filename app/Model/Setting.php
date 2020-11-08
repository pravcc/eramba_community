<?php
App::uses('PhinxApp', 'Lib');
App::uses('CacheCleaner', 'Lib/Cache');
App::uses('VisualisationShell', 'Visualisation.Console/Command');
App::uses('DataAssetShell', 'Console/Command');
App::uses('ObjectStatusShell', 'ObjectStatus.Console/Command');
App::uses('SystemShell', 'Console/Command');
App::uses('CakeSession', 'Model/Datasource');
App::uses('File', 'Utility');
App::uses('ErambaHttpSocket', 'Network/Http');
App::uses('CakeLog', 'Log');
App::uses('SystemHealthLib', 'Lib');
App::uses('CakeTime', 'Utility');

class Setting extends AppModel
{
	const USE_SSL_NO_ENCRYPTION = 0;
	const USE_SSL_SSL = 1;
	const USE_SSL_TLS = 2;

	public $name = 'Setting';

	public $notes = array();

	public $groupTitles = array();

	public $groupHelpText = array();

	public $actsAs = array(
		'FieldData.FieldData',
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'variable', 'value'
			)
		),
		'Uploader.Attachment' => array(
			'logo_file' => array(
				'nameCallback' => 'formatName',
				'dbColumn' => 'value',
				'transforms' => array(
					'logo' => array(
						'method' => 'resize',
						'nameCallback' => 'transformName',
						'overwrite' => true,
						//'width' => MAX_SMALL_IMAGE_WIDTH,
						'height' => 58,
						'expand' => false,
						'aspect' => true,
						'mode' => 'width'
					)
				),
			)
		),
		'Uploader.FileValidation' => array(
			'logo_file' => array(
				'extension' => array('gif', 'jpg', 'png', 'jpeg'),
				'type' => 'image',
				'required' => true
			)
		)
	);

	public $validate = array(
		'QUEUE_TRANSPORT_LIMIT' => [
			'number' => [
				'rule' => array('range', 9, 501),
				'required' => false,
				'message' => 'Only natural numbers in range of 10 - 500 are allowed.'
			]
		],
		'PDF_PATH_TO_BIN' => [
			'binaryCheck' => [
				'rule' => 'validatePdfBinary',
				'required' => false,
				'message' => 'An error occurred while we tried to generate a test pdf - the configured path or the library is wrong, please review the installation.'
			]
		],
		'BRUTEFORCE_WRONG_LOGINS' => [
			'rule' => 'naturalNumber',
			'required' => false,
			'message' => 'Only natural numbers are allowed.'
		],
		'BRUTEFORCE_SECONDS_AGO' => [
			'rule' => 'naturalNumber',
			'required' => false,
			'message' => 'Only natural numbers are allowed.'
		],
		'BRUTEFORCE_BAN_FOR_MINUTES' => [
			'rule' => 'naturalNumber',
			'required' => false,
			'message' => 'Only natural numbers are allowed.'
		],
		'CRON_URL' => [
			'rule' => 'url',
			'required' => false,
			'message' => 'Please enter a valid URL.'
		],
	);

	const TYPE_TEXT = 'text';
	const TYPE_NUMBER = 'number';
	const TYPE_PASSWORD = 'password';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_SELECT = 'select';

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Settings');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true
			],
		];

		parent::__construct($id, $table, $ds);

		$this->groupTitles = array(
			'DEBUGCFG' => __('Enabling debug will make errors on the system very explicit. This is useful if you are debugging some issue alone. Do not enable debug unless strictly needed.')
		);

		$this->groupHelpText = array(
			'TRANSLATION' => __('You might need to clear the cache after an upgrade on the system.'),
			'CLRACLCACHE' => __('You might need to clear the ACL (the sub-system that handles who access where) after an upgrade on the system or made significant changes on the ACL assignation. This is a debug feature and we do not recommend using it unless you have been suggested by eramba team.')
		);
	}

	public function formatName($name, $file) {
		return Inflector::slug($name, '-');
	}

	public function transformName($name, $file) {
		return $this->getUploadedFile()->name();
	}

	public function afterSave($created, $options = [])
	{
		parent::afterSave($created, $options);

		Cache::clear(false, 'settings');
	}

	public function afterDelete()
	{
		parent::afterDelete();

		Cache::clear(false, 'settings');
	}

	/**
	 * Sets a value for custom variable.
	 */
	public function updateVariable($variable, $value) {
		$db = $this->getDataSource();
		$dbValue = $db->value($value, 'string');

		$ret = $this->updateAll(
			array(
				'Setting.value' => $dbValue,
				'Setting.modified' => 'NOW()',
			),
			array('Setting.variable' => $variable)
		);

		Cache::clear(false, 'settings');

		// if db version is the case, we update also DB version stored in runtime configuration
		if ($ret && $variable == 'DB_SCHEMA_VERSION') {
			Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
		}

		return $ret;
	}

	public function getVariable($variable) {
		// if db version is the case, we return up-to-date runtime value of DB version
		if ($variable == 'DB_SCHEMA_VERSION' && Configure::read('Eramba.Settings.DB_SCHEMA_VERSION')) {
			return Configure::read('Eramba.Settings.DB_SCHEMA_VERSION');
		}

		$value = $this->find('first', array(
			'conditions' => array(
				'Setting.variable' => $variable
			),
			'fields' => array('id', 'value'),
			'recursive' => -1
		));

		return $value['Setting']['value'];
	}

	/*
	 * Execute database schema file.
	 */
	public function runSchemaFile($path) {
		if (!is_file($path)) {
			return false;
		}
		
		$lines = file($path);

		if (!$lines) {
			return false;
		}

		$templine = '';

		$ret = $this->checkQueryResponse($this->query('SET FOREIGN_KEY_CHECKS=0'));

		foreach ($lines as $line) {
			if (substr($line, 0, 2) == '--' || $line == '') {
				continue;
			}

			$templine .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				$response = $this->query($templine);
				$ret &= $this->checkQueryResponse($response);
				$templine = '';
			}
		}

		$ret &= $this->checkQueryResponse($this->query('SET FOREIGN_KEY_CHECKS=1'));

		return $ret;
	}

	/**
	 * Drop all tables from current database.
	 */
	public function dropAllTables() {
		$result = $this->query("SHOW TABLES");
		$tables = array();

		foreach ($result as $table) {
			$tables[] = current(current($table));
		}

		$ret = $this->checkQueryResponse($this->query("SET FOREIGN_KEY_CHECKS=0"));

		foreach ( $tables as $table ) {
			$ret &= $this->checkQueryResponse($this->query('DROP TABLE IF EXISTS `' . $table . '`'));
		}

		$ret &= $this->checkQueryResponse($this->query("SET FOREIGN_KEY_CHECKS=1"));

		return $ret;
	}

	private function checkQueryResponse($response) {
		return checkQueryResponse($response);
	}

	public function deleteCache($folder) {
		if (empty($folder)) {
			$folder = CACHE;
		}

		return CacheCleaner::deleteCache($folder);
	}

	/**
	 * Reseting the database to its initial status having all migrations up to the latest version present.
	 * 
	 * @param  boolean $keepClientID Keep previous client_id or remove that too.
	 */
	public function resetDatabase($keepClientID = true)
	{
		if ($keepClientID) {
			// before we reset the database we store the CLIENT_ID so the app stays registered 
			$keepClientID = $this->getVariable('CLIENT_ID');
		}

		// first we drop all tables
		$ret = $this->dropAllTables();

		// then we load the latest available database within this release
		$ret &= $this->runSchemaFile(APP . 'Config/db_schema/e2.5.5.sql');

		// and we run DB migrations to make the loaded database catch up with all changes
		$ret &= $this->runMigrations();

		// do necessary synces after reset
		if ($ret) {
			$VisualisationShell = new VisualisationShell();
            $VisualisationShell->startup();
            $ret &= $VisualisationShell->acl_sync();
			$ret &= $VisualisationShell->CustomRoles->sync();

			$DataAssetShell = new DataAssetShell();
			$DataAssetShell->startup();
			$ret &= $DataAssetShell->add_instances();
		
			$ObjectStatusShell = new ObjectStatusShell();
			$ObjectStatusShell->startup();
			$ret &= $ObjectStatusShell->sync_all_statuses();

			$this->recalculateRiskScores();
			
			$SystemShell = new SystemShell();
			$SystemShell->startup();
			$ret &= $SystemShell->sync_db();

			$this->syncAcl();
		}

		if ($keepClientID) {
			$ret &= $this->updateVariable('CLIENT_ID', $keepClientID);
		}

		$ret &= $this->deleteCache('');

		return $ret;
	}

	// recalculate risk scores after fixes to it in this update package
    public function recalculateRiskScores() {
        $models = [
            'Risk' => 'Asset',
            'ThirdPartyRisk' => 'ThirdParty',
            'BusinessContinuity' => 'BusinessUnit'
        ];

        foreach ($models as $riskModel => $assocModel) {
            $Model = ClassRegistry::init($riskModel);
            $ids = $Model->find('list', ['fields' => "{$Model->alias}.id"]);

            foreach ($ids as $id) {
                $Model->afterSaveRiskScore($id, $assocModel);
            }
        }
    }

	/**
	 * generate md5 integrity hash from controllers models and views
	 * 
	 * @return string integrity hash
	 */
	public function getIntegrityHash() {
		$hash = '';
		$folderFiles = array();

		$folder = new Folder(APP . 'Controller');
		$folderFiles['controller'] = $this->prepareTreeForHash($folder->tree()[1]);

		$folder = new Folder(APP . 'Model');
		$folderFiles['model'] = $this->prepareTreeForHash($folder->tree()[1]);

		$folder = new Folder(APP . 'View');
		$folderFiles['view'] = $this->prepareTreeForHash($folder->tree()[1]);
		
		$hash .= md5(count($folderFiles, COUNT_RECURSIVE));

		foreach ($folderFiles as $folder) {
			foreach ($folder as $file) {
				$str = file($file, FILE_IGNORE_NEW_LINES);
				$str = array_map('rtrim', $str);
				$str = implode("\n", $str);

				$checksum = md5($str);
				$hash .= $checksum;
			}
		}

		return md5($hash);
	}

	/**
	 * Maintain cross platform support when generating checksum.
	 */
	private function prepareTreeForHash($tree) {
		$tree = str_replace('\\', '/', $tree);
		sort($tree, SORT_STRING);

		return $tree;
	}

	/**
	 * Synchronize ACL.
	 *
	 * @param boolean $fullSync True to fully synchronize the ACL,
	 *                          otherwise just update the Aco Tree with new controller actions.
	 * @return boolean			True on success, False otherwise.
	 */
	public function syncAcl($fullSync = true) {
		App::uses('AppModule', 'Lib');
		AppModule::loadAll();

		App::uses('AclExtras', 'AclExtras.Lib');
		$this->AclExtras = new AclExtras();
		$this->AclExtras->startup();
		$this->AclExtras->controller->constructClasses();
		
		if ($fullSync === false) {
			return $this->AclExtras->aco_update();
		}

		return $this->AclExtras->aco_sync();
	}

	/**
	 * Runs DB migrations from within the app.
	 *
	 * @return TBD
	 */
	public function runMigrations($target = null) {
		try {
			// lets disable database cache
			$ds = ConnectionManager::getDataSource('default');
			$ds->cacheSources = false;

			// lets check if there is any new migration
			$PhinxApp = new PhinxApp();
			$ret = $PhinxApp->getStatus();

			// run migrations if necessary
			if ($ret !== true || $target !== null) {
				$ret = $PhinxApp->getMigrate($target);
			}
		}
		catch (Exception $e) {
			App::uses('CakeLog', 'Log');
			CakeLog::write('error', 'Migration error: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString());
			return false;
		}

		return $ret;
	}

	public function syncVisualisation() {
		App::uses('VisualisationShell', 'Visualisation.Console/Command');
		$VisualisationShell = new VisualisationShell();
		$VisualisationShell->startup();
		return $VisualisationShell->acl_sync();
	}

	// recalculate risk scores after fixes to it in this update package
	public function runComposer() {
		// lets disable debug so people wont have --dev vendors
		$debug = Configure::read('debug');
		Configure::write('debug', 0);

		App::uses('SystemShell', 'Console/Command');

		$SystemShell = new SystemShell();
		$SystemShell->startup();
		$SystemShell->args = ['update'];

		$SystemShell->composer();

		Configure::write('debug', $debug);
	}

	public function validatePdfBinary($check)
	{
		return static::pdfBinaryCheck($check['PDF_PATH_TO_BIN']);
	}

	/**
	 * @alias for SystemHealthLib::pdfBinaryCheck() method
	 */
	public static function pdfBinaryCheck($path)
	{
		return SystemHealthLib::pdfBinaryCheck($path);
	}

	public static function settingsConfig()
	{
		return [
			'DB_SCHEMA_VERSION' => [
				'variable' => 'DB_SCHEMA_VERSION',
				'name' => __('DB Schema Version'),
				'type' => self::TYPE_TEXT,
				'hidden' => true,
			],
			'CLIENT_ID' => [
				'variable' => 'CLIENT_ID',
				'name' => __('Client ID'),
				'type' => self::TYPE_TEXT,
				'hidden' => true,
			],
			'BRUTEFORCE_WRONG_LOGINS' => [
				'variable' => 'BRUTEFORCE_WRONG_LOGINS',
				'name' => __('Bruteforce wrong logins'),
				'type' => self::TYPE_NUMBER,
				'group' => 'BFP',
			],
			'BRUTEFORCE_SECONDS_AGO' => [
				'variable' => 'BRUTEFORCE_SECONDS_AGO',
				'name' => __('Bruteforce second ago'),
				'type' => self::TYPE_NUMBER,
				'group' => 'BFP',
			],
			'BRUTEFORCE_BAN_FOR_MINUTES' => [
				'variable' => 'BRUTEFORCE_BAN_FOR_MINUTES',
				'name' => __('Bruteforce ban from minutes'),
				'type' => self::TYPE_NUMBER,
				'group' => 'BFP',
			],
			'DEFAULT_CURRENCY' => [
				'variable' => 'DEFAULT_CURRENCY',
				'name' => __('Default currency'),
				'type' => self::TYPE_SELECT,
				'group' => 'CUE',
				'options' => function() {
					return self::currencyOptions();
				}
			],
			'SMTP_USE' => [
				'variable' => 'SMTP_USE',
				'name' => __('Type'),
				'type' => self::TYPE_SELECT,
				'group' => 'MAILCNF',
				'options' => function() {
					return [
						'Mail',
						'SMTP'
					];
				},
				'info' => __('Type: Select the type of mail configuration you want to use to send notifications. Mail will use the operating system internal mailing capabilities while SMTP allows the use of any type of mail system (Exchange, Google, Etc).')
			],
			'SMTP_HOST' => [
				'variable' => 'SMTP_HOST',
				'name' => __('SMTP host'),
				'type' => self::TYPE_TEXT,
				'group' => 'MAILCNF',
				'info' => __('SMTP host: The server to which eramba will connect to send emails. For example "mail.google.com"')
			],
			'USE_SSL' => [
				'variable' => 'USE_SSL',
				'name' => __('Encryption'),
				'type' => self::TYPE_SELECT,
				'group' => 'MAILCNF',
				'options' => function() {
					return [
						__('No Encryption'),
						'SSL',
						'TLS'
					];
				},
				'info' => __('Choose the encryption type you would like to use when sending emails.')
			],
			'SMTP_USER' => [
				'variable' => 'SMTP_USER',
				'name' => __('SMTP user'),
				'type' => self::TYPE_TEXT,
				'group' => 'MAILCNF',
				'info' => __('SMTP user: The username to use in order to authenticate against the previously defined SMTP server. If left empty, the system will not authenticate the connection.')
			],
			'SMTP_PWD' => [
				'variable' => 'SMTP_PWD',
				'name' => __('SMTP password'),
				'type' => self::TYPE_PASSWORD,
				'group' => 'MAILCNF',
				'info' => __('SMTP password: The password to use in order to authenticate against the previously defined SMTP server. If left empty, the system will not authenticate the connection.')
			],
			'SMTP_TIMEOUT' => [
				'variable' => 'SMTP_TIMEOUT',
				'name' => __('SMTP timeout'),
				'type' => self::TYPE_NUMBER,
				'group' => 'MAILCNF',
				'info' => __('SMTP timeout: The time to wait before closing the SMTP connection in case we have some trouble reaching the server.')
			],
			'SMTP_PORT' => [
				'variable' => 'SMTP_PORT',
				'name' => __('SMTP port'),
				'type' => self::TYPE_TEXT,
				'group' => 'MAILCNF',
				'info' => __('SMTP port: The port to which eramba will connect')
			],
			'EMAIL_NAME' => [
				'variable' => 'EMAIL_NAME',
				'name' => __('Name'),
				'type' => self::TYPE_TEXT,
				'group' => 'MAILCNF',
				'info' => __('Define a name for this account that will be used as a sender for emails.')
			],
			'NO_REPLY_EMAIL' => [
				'variable' => 'NO_REPLY_EMAIL',
				'name' => __('No reply Email'),
				'type' => self::TYPE_TEXT,
				'group' => 'MAILCNF',
				'info' => __('No reply email: Is the email eramba will send the emails "From"')
			],
			'QUEUE_TRANSPORT_LIMIT' => [
				'variable' => 'QUEUE_TRANSPORT_LIMIT',
				'name' => __('Email Queue Throughput'),
				'type' => self::TYPE_NUMBER,
				'group' => 'MAILCNF',
				'info' => __('All emails sent by eramba go first to a queue (System / Settings / Email Queue) which flushes as the hourly cron runs (System / Settings / Crons Jobs). This setting defines how many emails are flushed out of the queue every time the hourly cron runs. We do not recommend increasing the size by more than 50 as the cron might time out while sending emails (the cron will run by 5 minutes at most).')
			],
			'BANNERS_OFF' => [
				'variable' => 'BANNERS_OFF',
				'name' => __('Banners off'),
				'type' => self::TYPE_CHECKBOX,
				'group' => 'BANNER',
			],
			'DEBUG' => [
				'variable' => 'DEBUG',
				'name' => __('Debug'),
				'type' => self::TYPE_CHECKBOX,
				'group' => 'DEBUGCFG',
				'info' => __('This feature enables more detailed output in case you are experiencing an error or other issue. Enable this if you feel confident you know what you are doing and send us your logs if you think you have found an issue in the application.')
			],
			'EMAIL_DEBUG' => [
				'variable' => 'EMAIL_DEBUG',
				'name' => __('Email Debug'),
				'type' => self::TYPE_CHECKBOX,
				'group' => 'DEBUGCFG',
				'info' => __('While this option is enabled, eramba wont send emails.')
			],
			'RISK_APPETITE' => [
				'variable' => 'RISK_APPETITE',
				'name' => __('Risk Appetite'),
				'type' => self::TYPE_NUMBER,
				'group' => 'RISKAPPETITE',
			],
			'TIMEZONE' => [
				'variable' => 'TIMEZONE',
				'name' => __('Timezone'),
				'type' => self::TYPE_SELECT,
				'group' => 'TZONE',
				'options' => function() {
					return self::timezoneOptions();
				}
			],
			'BACKUPS_ENABLED' => [
				'variable' => 'BACKUPS_ENABLED',
				'name' => __('Backups Enabled'),
				'type' => self::TYPE_CHECKBOX,
				'group' => 'BACKUP',
				'info' => __('Enable backup of your database during a daily CRON job')
			],
			'BACKUP_DAY_PERIOD' => [
				'variable' => 'BACKUP_DAY_PERIOD',
				'name' => __('Backup Day Period'),
				'type' => self::TYPE_SELECT,
				'group' => 'BACKUP',
				'options' => function() {
					return [
						1 => __('Every day'),
						2 => __('Every %s days', 2),
						3 => __('Every %s days', 3),
						4 => __('Every %s days', 4),
						5 => __('Every %s days', 5),
						6 => __('Every %s days', 6),
						7 => __('Every %s days', 7),
					];
				},
				'info' => __('How often we need to backup your database')
			],
			'BACKUP_FILES_LIMIT' => [
				'variable' => 'BACKUP_FILES_LIMIT',
				'name' => __('Backup Files Limit'),
				'type' => self::TYPE_SELECT,
				'group' => 'BACKUP',
				'options' => function() {
					return [
						1 => 1,
						5 => 5,
						10 => 10,
						15 => 15,
					];
				},
				'info' => __('How many backups should we keep on your system')
			],
			'RISK_GRANULARITY' => [
				'variable' => 'RISK_GRANULARITY',
				'name' => __('Risk Granularity'),
				'type' => self::TYPE_SELECT,
				'group' => 'RISK_GRANULARITY',
				'defaultValue' => 10,
				'options' => function() {
					return [
						1 => 1,
						2 => 2,
						5 => 5,
						10 => 10
					];
				},
				'info' => __('When you create a risk and set a residual score you do that as a percentage of the total Risk score. This option allows you to set the scales uses for the percentage value, by default the value is 10.')
			],
			'PDF_PATH_TO_BIN' => [
				'variable' => 'PDF_PATH_TO_BIN',
				'name' => __('WKHTMLTOPDF path to bin file'),
				'type' => self::TYPE_TEXT,
				'defaultValue' => '/usr/local/bin/wkhtmltopdf',
				'group' => 'PDFCONFIG',
				'info' => __('We use a third party software (Wkhtmltopdf.org) to produce reports, please make sure you include the full path to the binary that software uses to generate PDF documents.')
			],
			'SSL_OFFLOAD_ENABLED' => [
				'variable' => 'SSL_OFFLOAD_ENABLED',
				'name' => __('Enable SSH Offload'),
				'type' => self::TYPE_CHECKBOX,
				'defaultValue' => 0,
				'group' => 'SSLOFFLOAD',
				'info' => __('By enabling this checkbox eramba will read the x-forwarded-proto header to understand if the original client connection was made over https or http and respond based on that. This is typically used when offloading SSL before the server running eramba. This is a BETA functionality.')
			],
			'CSV_DELIMITER' => [
				'variable' => 'CSV_DELIMITER',
				'name' => __('CSV Delimiter'),
				'type' => self::TYPE_SELECT,
				'group' => 'CSV',
				'options' => function() {
					return [
						',' => '"," (' . __('Comma') . ')',
						';' => '";" (' . __('Semicolon') . ')',
					];
				},
				'info' => __('Choose the delimiter eramba will use when exporting CSV files from the filters, by default we use commas.')
			],
			'CLIENT_KEY' => [
				'variable' => 'CLIENT_KEY',
				'name' => __('Client Key'),
				'type' => self::TYPE_TEXT,
				'group' => 'ENTERPRISE_USERS',
				'info' => __('Insert the key provided to at the time you purchased your Enterprise subscription, if you are unsure what your key is please contact support@eramba.org')
			],
			'SECURITY_SALT' => [
				'variable' => 'SECURITY_SALT',
				'name' => __('Security Salt'),
				'type' => self::TYPE_TEXT,
				'group' => 'SECSALT',
				'info' => __('After changing this value all users will be logged out as cookies/sessions need to be regenerated.')
			],
			'CRON_TYPE' => [
				'variable' => 'CRON_TYPE',
				'name' => __('Cron Type'),
				'type' => self::TYPE_SELECT,
				'group' => 'SECKEY',
				'options' => function() {
					return [
						'web' => __('Web'),
						'cli' => 'CLI'
					];
				},
				'info' => __('Select the method you want to use for running crontab jobs, we recommend CLI as Web might be soon deprecated.')
			],
			'CRON_URL' => [
				'variable' => 'CRON_URL',
				'name' => __('Cron URL'),
				'type' => self::TYPE_TEXT,
				'defaultValue' => Configure::read('App.fullBaseUrl'),
				'group' => 'SECKEY',
				'info' => __('Configure the URL you normally use to access eramba. Make sure you correctly define http or https, for example: https://eramba.company.com')
			],
			'CRON_SECURITY_KEY' => [
				'variable' => 'CRON_SECURITY_KEY',
				'name' => __('Cron security key'),
				'type' => self::TYPE_TEXT,
				'group' => 'SECKEY',
				'info' => __('This is a private key that must be alphanumeric and be passed as an argument to the CRON calls, please see the examples below.')
			],
			'DEFAULT_TRANSLATION' => [
				'variable' => 'DEFAULT_TRANSLATION',
				'name' => __('Default Language'),
				'type' => self::TYPE_SELECT,
				'group' => 'DEFAULT_TRANSLATION',
				'options' => function() {
					return ClassRegistry::init('Translations.Translation')->getAvailableTranslations();
				},
				'defaultValue' => 1,
				'hidden' => true
			],
		];
	}

	public static function groupsConfig($excludeHidden = false)
	{
		$config = [
			'ACCESSMGT' => [
				'slug' => 'ACCESSMGT',
				'name' => __('Access Management'),
				'children' => [
					'ACCESSLST' => [
						'slug' => 'ACCESSLST',
						'name' => __('Access Lists'),
						'url' => [
							'plugin' => false,
							'controller' => 'admin',
							'action' => 'acl',
							'aros',
							'ajax_role_permissions'
						]
					],
					'AUTH' => [
						'slug' => 'AUTH',
						'name' => __('Authentication'),
						'url' => [
							'plugin' => false,
							'controller' => 'ldapConnectorAuthentications',
							'action' => 'edit',
						],
						'modal' => true,
					],
					'GROUP' => [
						'slug' => 'GROUP',
						'name' => __('Groups'),
						'url' => [
							'plugin' => false,
							'controller' => 'groups',
							'action' => 'index',
						],
					],
					'LDAP' => [
						'slug' => 'LDAP',
						'name' => __('LDAP Connectors'),
						'url' => [
							'plugin' => false,
							'controller' => 'ldapConnectors',
							'action' => 'index',
						],
					],
					'USER' => [
						'slug' => 'USER',
						'name' => __('User Management'),
						'url' => [
							'plugin' => false,
							'controller' => 'users',
							'action' => 'index',
						],
					],
					'VISUALISATION' => [
						'slug' => 'VISUALISATION',
						'name' => __('Visualisation'),
						'url' => [
							'plugin' => 'visualisation',
							'controller' => 'visualisationSettings',
							'action' => 'index',
						],
					],
					'OAUTH' => [
						'slug' => 'OAUTH',
						'name' => __('OAuth Connectors'),
						'url' => [
							'plugin' => false,
							'controller' => 'oauthConnectors',
							'action' => 'index',
						],
					],
					'SAML' => [
						'slug' => 'SAML',
						'name' => __('SAML Connectors'),
						'url' => [
							'plugin' => false,
							'controller' => 'samlConnectors',
							'action' => 'index',
						],
					],
					// 'ROLES' => [
					// 	'slug' => 'ROLES',
					// 	'name' => __('Roles'),
					// 	'url' => [
					// 		'plugin' => false,
					// 		'controller' => 'scopes',
					// 		'action' => 'index',
					// 	],
					// 	'hidden' => true,
					// ],
				]
			],
			'DB' => [
				'slug' => 'DB',
				'name' => __('Database'),
				'children' => [
					'BAR' => [
						'slug' => 'BAR',
						'name' => __('Backup & Restore'),
						'url' => [
							'plugin' => 'backupRestore',
							'controller' => 'backupRestore',
							'action' => 'index',
						],
						'modal' => true,
					],
					'DBRESET' => [
						'slug' => 'DBRESET',
						'name' => __('Reset Database'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'resetDatabase',
						],
						'modal' => true,
					],
					'BACKUP' => [
						'slug' => 'BACKUP',
						'name' => __('Backup Configuration'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'backup'
						],
						'modal' => true,
						'info' => __('Warning: the backup functionality only looks at the database, attached files are not going to be back up by this functionality, please review our Install / Configuration for suggestions on how we recommend doing full application backups.')
					],
					// 'DBCNF' => [
					// 	'slug' => 'aaa',
					// 	'name' => __('Database Configurations'),
					// 	'hidden' => true,
					// ],
					// 'PRELOAD' => [
					// 	'slug' => 'aaa',
					// 	'name' => __('Pre-load the database with default databases'),
					// 	'hidden' => true,
					// ],
				]
			],
			'DEBUG' => [
				'slug' => 'DEBUG',
				'name' => __('Debug Settings and Logs'),
				'children' => [
					'DEBUGCFG' => [
						'slug' => 'DEBUGCFG',
						'name' => __('Debug Config'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'debug'
						],
						'modal' => true,
					],
					'ERRORLOG' => [
						'slug' => 'ERRORLOG',
						'name' => __('Error Log'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'logs',
							'error'
						],
						'modal' => true,
					],
					'MAILLOG' => [
						'slug' => 'MAILLOG',
						'name' => __('Email Log'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'logs',
							'email'
						],
						'modal' => true,
						'hidden' => (!Configure::read('debug')) ? true : false
					],
					'CLRCACHE' => [
						'slug' => 'CLRCACHE',
						'name' => __('Clear Cache'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'deleteCache',
						],
					],
				]
			],
			'LOC' => [
				'slug' => 'LOC',
				'name' => __('Localization'),
				'children' => [
					'CUE' => [
						'slug' => 'CUE',
						'name' => __('Currency'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'currency',
						],
						'modal' => true,
					],
					'LOGO' => [
						'slug' => 'LOGO',
						'name' => __('Custom Logo'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'customLogo',
						],
						'modal' => true,
					],
					'TZONE' => [
						'slug' => 'TZONE',
						'name' => __('Timezone'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'timezone',
						],
						'modal' => true,
					],
					'CSV' => [
						'slug' => 'CSV',
						'name' => __('CSV Delimiter'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'csv',
						],
						'modal' => true,
						'info' => __('Warning: The delimiter applies only to Exports (not Imports).')
					],
					'TRANSLATION' => [
						'slug' => 'TRANSLATION',
						'name' => __('Languages'),
						'url' => [
							'plugin' => 'translations',
							'controller' => 'translations',
							'action' => 'index',
						],
					],
					'DEFAULT_TRANSLATION' => [
						'slug' => 'DEFAULT_TRANSLATION',
						'name' => __('Default Language'),
						'modal' => true,
						'hidden' => true,
					],
				]
			],
			'MAIL' => [
				'slug' => 'MAIL',
				'name' => __('Mail'),
				'children' => [
					'MAILCNF' => [
						'slug' => 'MAILCNF',
						'name' => __('Mail Configurations'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'email',
						],
						'modal' => true,
					],
					'QUEUE' => [
						'slug' => 'QUEUE',
						'name' => __('Emails In Queue'),
						'url' => [
							'plugin' => false,
							'controller' => 'queue',
							'action' => 'index',
						],
					],
				]
			],
			'SEC' => [
				'slug' => 'SEC',
				'name' => __('Security'),
				'children' => [
					'BFP' => [
						'slug' => 'BFP',
						'name' => __('Brute Force Protection'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'bruteForceProtection',
						],
						'modal' => true,
					],
					'HEALTH' => [
						'slug' => 'HEALTH',
						'name' => __('System Health'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'systemHealth',
						],
						'modal' => true,
					],
					'UPDATES' => [
						'slug' => 'UPDATES',
						'name' => __('Updates'),
						'url' => [
							'plugin' => false,
							'controller' => 'updates',
							'action' => 'index',
						],
					],
					'SSLOFFLOAD' => [
						'slug' => 'SSLOFFLOAD',
						'name' => __('SSL Offload'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'sslOffload',
						],
						'modal' => true,
					],
					'ENTERPRISE_USERS' => [
						'slug' => 'ENTERPRISE_USERS',
						'name' => __('Enterprise Users'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'enterpriseUser',
						],
						'modal' => true,
						'hidden' => (Configure::read('Eramba.version')[0] == 'c') ? true : false
					],
				]
			],
			'CRONJOBS' => [
				'slug' => 'CRONJOBS',
				'name' => __('Cron Jobs'),
				'children' => [
					'SECKEY' => [
						'slug' => 'SECKEY',
						'name' => __('Crontab'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'crontab',
						],
						'modal' => true,
					],
					'CRON' => [
						'slug' => 'CRON',
						'name' => __('Crontab History'),
						'url' => [
							'plugin' => false,
							'controller' => 'cron',
							'action' => 'index',
						],
					],
				]
			],
			'GENERAL' => [
				'slug' => 'GENERAL',
				'name' => __('General Settings'),
				'children' => [
					'PDFCONFIG' => [
						'slug' => 'aaa',
						'name' => __('PDF Configuration'),
						'url' => [
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'pdf',
						],
						'modal' => true,
					],
				]
			],
			'RISK' => [
				'slug' => 'RISK',
				'name' => __('Risk'),
				'children' => [
					'RISKAPPETITE' => [
						'slug' => 'RISKAPPETITE',
						'name' => __('Risk appetite'),
						'modal' => true,
					],
					'RISK_GRANULARITY' => [
						'slug' => 'RISK_GRANULARITY',
						'name' => __('Risk Granularity'),
						'modal' => true,
					],
				],
				'hidden' => true,
			],
			// 'SECSALT' => [
			// 	'slug' => 'SECSALT',
			// 	'name' => __('Security Salt'),
			// 	'hidden' => true,
			// ],
		];

		if ($excludeHidden) {
			foreach ($config as $key => $item) {
				if (!empty($item['children'])) {
					foreach ($item['children'] as $childKey => $childItem) {
						if (!empty($childItem['hidden'])) {
							unset($config[$key]['children'][$childKey]);
						}
					}
				}

				if (!empty($item['hidden'])) {
					unset($config[$key]);
				}
			}
		}

		return $config;
	}

	public static function getGroup($slug)
	{
		foreach (self::groupsConfig() as $key => $item) {
			if ($key == $slug) {
				$item['slug'] = $key;
				return $item;
			}

			if (!empty($item['children'])) {
				foreach ($item['children'] as $subKey => $subItem) {
					if ($subKey == $slug) {
						$subItem['slug'] = $subKey;
						return $subItem;
					}
				}
			}
		}

		return null;
	}

	public static function getGroupWithSettings($slug)
	{
		$group = self::getGroup($slug);

		if ($group == null) {
			return null;
		}

		$group['settings'] = [];

		foreach (self::settingsConfig() as $variable => $setting) {
			if (isset($setting['group']) && $setting['group'] == $slug) {
				$group['settings'][$variable] = $setting;
			}
		}

		return $group;
	}

	public static function currencyOptions()
	{
		$customCurrencies = getCustomCurrencies();

		$options = array();
		foreach ($customCurrencies as $c => $currencyOpts) {
			$label = $c;

			if (!empty($currencyOpts['currency'])) {
				$label .= ' - ' . $currencyOpts['currency'];
			}

			$locations = false;
			if (!empty($currencyOpts['locations'])) {
				$locations = implode(', ', $currencyOpts['locations']);
			}
			
			$options[$c] = array(
				'name' => $label,
				'value' => $c,
				'data-locations' => $locations
			);
		}
		array_unshift($options, array('' => ''));

		return $options;
	}

	public static function timezoneOptions()
	{
		$options = CakeTime::listTimezones();

		$options['UTC Timezone'] = $options['UTC'];
		unset($options['UTC']);

		foreach ($options as $group => $values) {
			if (empty($values)) {
				continue;
			}

			foreach ($values as $tz => $val) {
				$options[$group][$tz] = sprintf('%s (%s)', $val, $tz);
			}
		}

		return $options;
	}

}
