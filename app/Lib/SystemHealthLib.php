<?php
App::uses('CakeObject', 'Core');
App::uses('Model', 'Model');
App::uses('ConnectionManager', 'Model');
App::uses('CakeLog', 'Log');
App::uses('CakeTime', 'Utility');
App::uses('Router', 'Routing');

class SystemHealthLib extends CakeObject
{
	const PHP_VERSION_REQUIRED = '7.0.0';
	const MYSQL_VERSION_REQUIRED = '5.6.5';
	const MARIADB_VERSION_REQUIRED = '10.1.0';
	const PHP_MEMORY_LIMIT = '2048M';
	const PHP_MAX_EXECUTION_TIME = '200';
	const PHP_UPLOAD_LIMIT = 8;
	const PHP_MAX_INPUT_VARS = 3000;
	const MYSQL_ALLOWED_PACKET = 128000000;
	const MYSQL_INNODB_LOCK_WAIT_TIMEOUT = self::PHP_MAX_EXECUTION_TIME;

	const SYSTEM_HEALTH_OK = 1;
	const SYSTEM_HEALTH_NOT_OK = 0;
	const SYSTEM_HEALTH_CRITICAL = 'critical';
	const SYSTEM_HEALTH_DESIRED = 'desired';

	private $checkStatuses = array();

	public function loadChecks()
	{
		$this->checkStatuses = array(
			array(
				'groupName' => __('PHP Libraries and Extensions'),
				'checks' => array(
					array(
						'name' => __('PHP 7'),
						'fn' => array('SystemHealthLib', 'phpVersion'),
						'description' => __('eramba needs PHP versions above %s', SystemHealthLib::PHP_VERSION_REQUIRED),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('MySQL'),
						'fn' => array('SystemHealthLib', 'mysql'),
						'description' => __(
							'We need at least MySQL %s / MariaDB %s, installed with PHP',
							SystemHealthLib::MYSQL_VERSION_REQUIRED,
							SystemHealthLib::MARIADB_VERSION_REQUIRED
						),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('OpenSSL'),
						'fn' => array('SystemHealthLib', 'openssl'),
						'description' => __('We need SSL in order to encrpyt SMTP, LDAP and API connections. SNI support and OpenSSL 0.9.8k or greater is required'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('CURL'),
						'fn' => array('SystemHealthLib', 'curl'),
						'description' => __('We need Curl libraries to manage file uploads. Altough this module in linux is called different depending on the distribution and version, you could try: php-pear-Net-Curl.noarch or php-curl.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('LDAP'),
						'fn' => array('SystemHealthLib', 'ldap'),
						'description' => __('We need LDAP libraries in PHP to manage accounts and groups. Altough this module in linux is called different depending on the distribution and version, you could try: php-ldap'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Mail'),
						'fn' => array('SystemHealthLib', 'mail'),
						'description' => __('We use mail libraries to connect to SMTP servers.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Fileinfo'),
						'fn' => array('SystemHealthLib', 'fileinfo'),
						'description' => __('We need fileinfo libraries as part of PHP'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Multibyte String'),
						'fn' => array('SystemHealthLib', 'mbstring'),
						'description' => __('We need multibyte strings as part of PHP.Altough this module in linux is called different depending on the distribution and version, you could try: php-mbstring'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('GD'),
						'fn' => array('SystemHealthLib', 'gd'),
						'description' => __('We use GD libraries to manage the system Logo. Altough this module in linux is called different depending on the distribution and version, you could try: php-gd'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Exif'),
						'fn' => array('SystemHealthLib', 'exif'),
						'description' => __('We need exif as part of PHP to manage images. Altough this module in linux is called different depending on the distribution and version, you could try: php-exif'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Zlib'),
						'fn' => array('SystemHealthLib', 'zlib'),
						'description' => __('We need Zlib to handle file compression. Altough this module in linux is called different depending on the distribution and version, you could try: php7.0-zip or php-pclzip or php-pecl'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Phar'),
						'fn' => array('SystemHealthLib', 'pharDataClass'),
						'description' => __('PharData as Phar extensions for archive files accessing. This is part of PHP.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('ZipArchive'),
						'fn' => array('SystemHealthLib', 'zipArchiveClass'),
						'description' => __('ZipArchive class for bundling a backup of the system. If you have installed all ZIP files (check above) this should be OK.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Intl'),
						'fn' => array('SystemHealthLib', 'intl'),
						'description' => __('Altough this module in linux is called different depending on the distribution and version, you could try: php-intl'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('SimpleXML'),
						'fn' => array('SystemHealthLib', 'simplexml'),
						'description' => __('Altough this module in linux is called different depending on the distribution and version, you could try: php-xml'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
				)
			),
			array(
				'groupName' => __('Server Settings'),
				'checks' => array(
					array(
						'name' => __('Max Execution Time'),
						'fn' => array('SystemHealthLib', 'maxExecutionTime'),
						'description' => __('We require setting max execution time to be equal or more than %s seconds. You can find this setting on your php.ini (under /etc/) file under the setting: max_execution_time', SystemHealthLib::PHP_MAX_EXECUTION_TIME),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Memory Limit'),
						'fn' => array('SystemHealthLib', 'memoryLimit'),
						'description' => __(
							'We require setting memory limitations to be equal or more than %s (MG). You can find this setting on your php.ini (under /etc/) file under the setting: memory_limit',
							SystemHealthLib::PHP_MEMORY_LIMIT
						),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Access Wrappers'),
						'fn' => array('SystemHealthLib', 'allow_url_fopen'),
						'description' => __('We require a special URL management setting under php to be enabled. You can find this setting on your php.ini (under /etc/) file under the setting: allow_url_fopen'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Max Input Vars'),
						'fn' => array('SystemHealthLib', 'maxInputVars'),
						'description' => __('We require setting max input vars to be equal or more than %s. You can find this setting on your php.ini (under /etc/) file under the setting: max_input_vars', SystemHealthLib::PHP_MAX_INPUT_VARS),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_DESIRED
					),
					array(
						'name' => __('Program execution Function'),
						'fn' => array('SystemHealthLib', 'procOpen'),
						'description' => __('We require that proc_open function cannot be disabled by "disabled_function" configuration in your php.ini (under /etc/).'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
				)
			),
			array(
				'groupName' => __('Crons'),
				'checks' => array(
					'cron-hourly' => array(
						'name' => __('Hourly'),
						'fn' => [$this, 'cronsHourly'],
						'description' => __('You need to make sure crons are being called every hour. The cron should call the following URL: http(s)://yourdomain/cron/hourly/KEY where KEY is defined under System / Settings / Security Key. You can validate if the cron are running correctly or not at System / Settings / Cron <br><br>For example: @hourly /usr/bin/wget --no-check-certificate -O /dev/null https://mydomain/cron/hourly/rqltLFkcEc (where my Security Key is: rqltLFkcEc)'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					'cron-daily' => array(
						'name' => __('Daily'),
						'fn' => [$this, 'cronsDaily'],
						'description' => __('You need to make sure crons are being called every day. The cron should call the following URL: http(s)://yourdomain/cron/daily/KEY where KEY is defined under System / Settings / Security Key. You can validate if the cron are running correctly or not at System / Settings / Cron. The cron must run succesfully at least once in the last 30 hours to set status as OK. <br><br>For example: @daily /usr/bin/wget --no-check-certificate -O /dev/null https://mydomain/cron/daily/rqltLFkcEc (where my Security Key is: rqltLFkcEc)

							<br><br>NOTE: If you are an enterprise customer you need your license to be valid'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Yearly'),
						'fn' => [$this, 'cronsYearly'],
						'description' => __('You need to make sure crons are being called every year. The cron should call the following URL: http(s)://yourdomain/cron/yearly/KEY where KEY is defined under System / Settings / Security Key. You can validate if the cron are running correctly or not at System / Settings / Cron. <br><br>For example: @yearly /usr/bin/wget --no-check-certificate -O /dev/null https://mydomain/cron/yearly/rqltLFkcEc (where my Security Key is: rqltLFkcEc)<br><br>NOTE: if you are doing a fresh install, call this cron URL with your browser.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					/*array(
						'name' => __('Correct CRON URL'),
						'fn' => [$this, 'checkCronUrl'],
						'description' => __('This test checks that the servername you used on your crontab is the same as you use with your browser to access eramba. For this reason you can not use localhost or 127.0.0.1 (as eramba must run in a separate server from your clients).'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					)*/
				)
			),
			array(
				'groupName' => __('Write Permissions'),
				'checks' => array(
					array(
						'name' => __('Temporary folder'),
						'fn' => array('SystemHealthLib', 'writeTmp'),
						'description' => __('The system should be allowed to write and read on the directory eramba_v2/app/tmp/*. Altough every distribution works different the quickest way to solve this is by using the command "chown" and assign all this files the user/group apache is using.<br><br>For example, in ubuntu while at the eramba_v2 directory: chown www-data:www-data app/tmp/ -R<br><br>NOTE: some systems enforce selinux, which can override this permissions.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('User Attachments & Awareness Media folder'),
						'fn' => array('SystemHealthLib', 'writeFiles'),
						'description' => __('The system should be allowed to write and read on the directory eramba_v2/app/webroot/files/*. Altough every distribution works different the quickest way to solve this is by using the command "chown" and assign all this files the user/group apache is using.<br><br>For example, in ubuntu while at the eramba_v2 directory: chown www-data:www-data app/webroot/files/ -R<br><br>NOTE: some systems enforce selinux, which can override this permissions.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Policy HTML Editor Media folder'),
						'fn' => array('SystemHealthLib', 'writeMedia'),
						'description' => __('The system should be allowed to write and read on the directory eramba_v2/app/webroot/media/*. Altough every distribution works different the quickest way to solve this is by using the command "chown" and assign all this files the user/group apache is using.<br><br>For example, in ubuntu while at the eramba_v2 directory: chown www-data:www-data app/webroot/media/ -R<br><br>NOTE: some systems enforce selinux, which can override this permissions.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Database Backups folder'),
						'fn' => array('SystemHealthLib', 'writeBackups'),
						'description' => __('The system should be allowed to write and read on the directory eramba_v2/app/webroot/backups/*. Altough every distribution works different the quickest way to solve this is by using the command "chown" and assign all this files the user/group apache is using.<br><br>For example, in ubuntu while at the eramba_v2 directory: chown www-data:www-data app/webroot/backups/ -R<br><br>NOTE: some systems enforce selinux, which can override this permissions.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Mail Queue folder'),
						'fn' => array('SystemHealthLib', 'writeQueue'),
						'description' => __('The system should be allowed to write and read on the directory eramba_v2/app/Vendor/queue/. Altough every distribution works different the quickest way to solve this is by using the command "chown" and assign all this files the user/group apache is using.<br><br>For example, in ubuntu while at the eramba_v2 directory: chown www-data:www-data app/Vendor/queue/ -R<br><br>NOTE: some systems enforce selinux, which can override this permissions.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
				)
			),
			array(
				'groupName' => __('Other'),
				'checks' => array(
					'default-password' => array(
						'name' => __('Default Password'),
						'fn' => [$this, 'password'],
						'description' => __('You must change the admin password at System / Settings / User Management'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_DESIRED
					),
					// 'default-salt' => array(
					// 	'name' => __('Default Security Salt'),
					// 	'fn' => [$this, 'salt'],
					// 	'description' => __('You must change the default Security Salt at System / Settings / Security Salt.'),
					// 	'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					// ),
					array(
						'name' => __('Max Post Size'),
						'fn' => array('SystemHealthLib', 'postSize'),
						'description' => __('In order to upload large files you need to set post_max_size to %sM or more. You can find this setting on your php.ini (under /etc/).', SystemHealthLib::PHP_UPLOAD_LIMIT),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('Max Upload Filesize'),
						'fn' => array('SystemHealthLib', 'uploadFilesize'),
						'description' => __('In order to upload large files you need to set upload_max_filesize to %sM or more. You can find this setting on your php.ini (under /etc/)', SystemHealthLib::PHP_UPLOAD_LIMIT),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					array(
						'name' => __('MySQL Strict'),
						'fn' => [$this, 'sqlStrict'],
						'description' => __('You need to disable strict mode in MySQL or MariaDB. You need to edit MySQL / MariaDB configuration files (this changes from distribution to distribution: /etc/mysql/mysql.conf.d/mysqld.cnf in Ubuntu, /etc/my.cnf in Red Hat) and under the section [mysqld] add: sql_mode="". You will need to restart the engine after the configuration change is done.<br><br>We recommend looking at the specifics of your linux distribution to ensure the change is done correctly.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL

					),
					array(
						'name' => __('MySQL Allowed Packet'),
						'fn' => [$this, 'sqlPacket'],
						'description' => __('You need to set cache limits in MySQL or MariaDB. You need to edit MySQL / MariaDB configuration files (this changes from distribution to distribution: /etc/mysql/mysql.conf.d/mysqld.cnf in Ubuntu, /etc/my.cnf in Red Hat) and under the section [mysqld] add: max_allowed_packet="%s". You will need to restart the engine after the configuration change is done.<br><br>We recommend looking at the specifics of your linux distribution to ensure the change is done correctly.', SystemHealthLib::MYSQL_ALLOWED_PACKET),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL

					),
					'innodb-lock-timeout' => array(
						'name' => __('MySQL InnoDB Lock Timeout'),
						'fn' => [$this, 'sqlLockTimeout'],
						'description' => __('You need to set cache limits in MySQL or MariaDB. You need to edit MySQL / MariaDB configuration files (this changes from distribution to distribution: /etc/mysql/mysql.conf.d/mysqld.cnf in Ubuntu, /etc/my.cnf in Red Hat) and under the section [innodb] add: innodb_lock_wait_timeout="%s". You will need to restart the engine after the configuration change is done.<br><br>We recommend looking at the specifics of your linux distribution to ensure the change is done correctly.', SystemHealthLib::MYSQL_INNODB_LOCK_WAIT_TIMEOUT),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL

					),
					array(
						'name' => __('Backups Enabled'),
						'new' => false,
						'fn' => array('SystemHealthLib', 'backups'),
						'description' => __('You are required to have daily backups enabled on the system, at System / Settings / Backup Configuration you can enable this feature.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
					'pdf-path-to-bin' => array(
						'name' => __('PDF (WKHTMLTOPDF) path to bin'),
						'fn' => array($this, 'pdfBinPath'),
						'description' => __('We use a third party software to create PDF documents (wkhtmltopdf.org), please make sure you install this software and define under System / Settings / PDF Configurations the full path to the binary that generates the PDF documents.'),
						'criticality' => SystemHealthLib::SYSTEM_HEALTH_CRITICAL
					),
				)
			),
		);
	}

	/**
	 * Internal method to get final value of a health check status.
	 * 
	 * @param  array $check Check config.
	 * @return bool Result status true if passed or false otherwise
	 */
	protected function _processCheck($check)
	{
		$callable = $check['fn'];

		$args = $this->_getValue($callable);
		$status = (boolean) $this->initCheckFunction($callable, [$args]);

		if (!$status) {
			$exportValue = print_r($args, true);
			CakeLog::write('SystemHealth', "{$check['name']} ({$check['criticality']}) check failed with value {$exportValue}");
		}

		return $status;
	}

	/**
	 * Get value for health check in the current application instance.
	 */
	protected function _getValue($callable)
	{
		$fnForValue = $callable;
		$fnForValue[1] = $fnForValue[1] . '_value';

		return $this->initCheckFunction($fnForValue);
	}

	/**
	 * Get Table data.
	 * 
	 * @return array Table Data.
	 */
	public function getData()
	{
		$this->loadChecks();
		
		$data = $this->checkStatuses;
		foreach ($data as $groupKey => $group) {
			foreach ($group['checks'] as $checkKey => $check) {
				$data[$groupKey]['checks'][$checkKey]['status'] = $this->_processCheck($check);
				$value = $this->_getValue($check['fn']);
				$data[$groupKey]['checks'][$checkKey]['value'] = ($value === false) ? '-' : $value;

				unset($data[$groupKey]['checks'][$checkKey]['fn']);
			}
		}

		return $data;
	}

	/**
	 * Get boolean value of all critical statuses.
	 */
	public function checkCriticalStatuses($options = [])
	{
		$options = array_merge([
			'skip' => [] // array of key slugs for system checks to skip
		], $options);

		$this->loadChecks();

		$ret = true;
		foreach ($this->checkStatuses as $group) {
			foreach ($group['checks'] as $key => $check) {
				$doCheck = $check['criticality'] == SystemHealthLib::SYSTEM_HEALTH_CRITICAL;
				$doCheck &= !in_array($key, $options['skip']);

				if ($doCheck) {
					$ret &= $this->_processCheck($check);
				}
			}
		}

		return (bool) $ret;
	}

	public function initCheckFunction($fn, $args = [])
	{
		if (is_callable($fn)) {
			return call_user_func_array($fn, $args);
		}

		return false;
	}

	/**
	 * Initialize a model that does not extend AppModel, used for update process to scope out
	 * of the application entirely.
	 * 
	 * By default use Setting model.
	 */
	protected function _blankModel($name = 'BootstrapSetting', $table = 'settings')
	{
		$modelConfig = ['table' => $table, 'name' => $name, 'ds' => 'default'];

		return (new Model($modelConfig));
	}

	/**
	 * Check for a strict sql mode. If a single mode contains string "STRICT" or is missing string "NO_ENGINE_SUBSTITUTION", check fails.
	 */
	private function sqlStrict($value)
	{
		$sqlModesString = $value;

		if ($sqlModesString !== false) {
			$sqlMode = explode(',', $sqlModesString);

			//check for a "STRICT" occurence
			$strictExists = strpos($sqlModesString, 'STRICT');
			if ($strictExists !== false) {
				return false;
			}

			if (in_array('NO_ENGINE_SUBSTITUTION', $sqlMode)) {
				return true;
			}
			elseif (empty($sqlMode[0])) {
				return true;
			}
		}

		return false;
	}

	public function sqlStrict_value()
	{
		$sqlMode = $this->_blankModel()->query("SELECT @@sql_mode;");
		if (isset($sqlMode[0][0]['@@sql_mode'])) {
			return $sqlMode[0][0]['@@sql_mode'];
		}

		return false;
	}

	private function sqlPacket($value)
	{
		$maxAllowedPacket = $value;

		if ($maxAllowedPacket !== false && $maxAllowedPacket >= SystemHealthLib::MYSQL_ALLOWED_PACKET) {
			return true;
		}

		return false;
	}

	public function sqlPacket_value()
	{
		$select = $this->_blankModel()->query("SELECT @@max_allowed_packet;");

		if (isset($select[0][0]['@@max_allowed_packet'])) {
			return $select[0][0]['@@max_allowed_packet'];
		}

		return false;
	}

	private function sqlLockTimeout($value)
	{
		$lockTimeout = $value;

		if ($lockTimeout !== false && $lockTimeout >= SystemHealthLib::MYSQL_INNODB_LOCK_WAIT_TIMEOUT) {
			return true;
		}

		return false;
	}

	public function sqlLockTimeout_value()
	{
		$select = $this->_blankModel()->query("SELECT @@innodb_lock_wait_timeout;");

		if (isset($select[0][0]['@@innodb_lock_wait_timeout'])) {
			return $select[0][0]['@@innodb_lock_wait_timeout'];
		}

		return false;
	}

	public function cronsHourly()
	{
		// $hoursAgo = CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('-2 hours'));
		$hoursAgoTolerance = CakeTime::format(CakeTime::fromString('-2 hours'), '%Y-%m-%d %H:%M:%S');
		
		$data = $this->_blankModel('BootstrapCron', 'cron')->find('count', array(
			'conditions' => array(
				// IMPORTANT:
				// here we are using hard-defined values instead of Cron model constans to not run "spl_autoload_call" on Cron which loads AppModel
				// which then might break update process if there was a change in AppModel file between 2 versions.
				'BootstrapCron.type' => 'hourly',
				'BootstrapCron.status' => 'success',
				'BootstrapCron.created >' => $hoursAgoTolerance
			),
			'order' => array('BootstrapCron.created' => 'DESC')
		));

		if ($data < 1) {
			return false;
		}

		return true;
	}

	/**
	 * Check that a Daily CRON was successfully processed in the last 30 hours.
	 * 
	 * @return boolean True on success.
	 */
	private function cronsDaily()
	{
		// $today = CakeTime::format(strtotime('now'), '%Y-%m-%d');
		// $yesterday = CakeTime::format(strtotime('-1 day'), '%Y-%m-%d');
		$hoursAgoTolerance = CakeTime::format(CakeTime::fromString('-30 hours'), '%Y-%m-%d %H:%M:%S');

		$data = $this->_blankModel('BootstrapCron', 'cron')->find('count', array(
			'conditions' => array(
				// IMPORTANT:
				// here we are using hard-defined values instead of Cron model constans to not run "spl_autoload_call" on Cron which loads AppModel
				// which then might break update process if there was a change in AppModel file between 2 versions.
				'BootstrapCron.type' => 'daily',
				'BootstrapCron.status' => 'success',
				'BootstrapCron.created >=' => $hoursAgoTolerance
			),
			'order' => array('BootstrapCron.created' => 'DESC'),
			'limit' => 1
		));

		if ($data < 1) {
			return false;
		}

		return true;
	}

	private function cronsYearly()
	{
		$data = $this->_blankModel('BootstrapCron', 'cron')->find('count', array(
			'conditions' => array(
				// IMPORTANT:
				// here we are using hard-defined values instead of Cron model constans to not run "spl_autoload_call" on Cron which loads AppModel
				// which then might break update process if there was a change in AppModel file between 2 versions.
				'BootstrapCron.type' => 'yearly',
				'YEAR(BootstrapCron.created)' => date('Y'),
				'BootstrapCron.status' => 'success'
			)
		));

		if (!empty($data)) {
			return true;
		}

		return false;
	}

	private function checkCronUrl()
	{
		$data = $this->_blankModel('BootstrapCron', 'cron')->find('first', array(
			'fields' => array(
				'url'
			),
			'order' => array(
				'id' => 'DESC'
			)
		));

		$conds = empty($data);
		$conds = $conds || $data['BootstrapCron']['url'] === null;
		$conds = $conds || $data['BootstrapCron']['url'] === Router::fullBaseUrl();

		// if there is not enough data or url is actually matching what is should then its okay
		if ($conds) {
			return true;
		}

		return false;
	}

	private function password() {
		$count = $this->_blankModel('BootstrapUser', 'users')->find('count', array(
			'conditions' => array(
				'BootstrapUser.id' => ADMIN_ID,
				'BootstrapUser.password' => '$2a$10$WhVO3Jj4nFhCj6bToUOztun/oceKY6rT2db2bu430dW5/lU0w9KJ.'
			),
			'recursive' => -1
		));

		return (boolean) !$count;
	}

	public function salt() {
		$salt = Configure::read('Security.salt');

		return $salt && $salt !== 'I92H10xrOR1V3lRcrng0ChYxP4325Cg1UHK0x97G76spq8nyew';
	}

	public function pdfBinPath()
	{
		$setting = $this->_blankModel()->find('first', [
			'conditions' => [
				'variable' => 'PDF_PATH_TO_BIN'
			],
			'recursive' => -1
		]);

		if (empty($setting)) {
			return false;
		}

		return self::pdfBinaryCheck($setting['BootstrapSetting']['value']);
	}

	public static function pdfBinaryCheck($path)
	{
		if (empty($path) || strpos(strtolower($path), 'phar://') !== false) {
			CakeLog::write('debug', 'PdfBinaryCheck: Path is empty.');
			return false;
		}

		if (!self::procOpen()) {
			CakeLog::write('debug', 'PdfBinaryCheck: proc_open is not allowed.');
			return false;
		}

		if (!class_exists('\Knp\Snappy\Pdf')) {
			CakeLog::write('debug', 'PdfBinaryCheck: Snappy lib is missing.');
			return false;
		}

		if (!file_exists($path)) {
			CakeLog::write('debug', 'PdfBinaryCheck: File does not exist.');
			return false;
		}

		$Pdf = new \Knp\Snappy\Pdf($path);

		$Pdf->setOptions([
			'orientation' => 'portrait',
			'dpi' => 100,
		]);

		$url = Router::url(['admin' => false, 'plugin' => null, 'controller' => 'settings', 'action' => 'testPdf'], true);

		try {
			$generatedPdf = $Pdf->getOutput($url);
		} catch (Exception $e) {
			// log the error
			AppErrorHandler::logException($e);

			CakeLog::write('debug', 'PdfBinaryCheck: Snappy failed with message - ' . $e->getMessage());

			return false;
		}

		//first chars check
		$pdfMagic = "\x25\x50\x44\x46\x2D";
		$pdfCheck = substr($generatedPdf, 0, strlen($pdfMagic)) === $pdfMagic;
		// preg_match("/^%PDF-1./", $generatedPdf) === 1; //this is another way of check

		if (!$pdfCheck) {
			CakeLog::write('debug', 'PdfBinaryCheck: Output file is not a pdf.');
			return false;
		}

		//file size check
		$sizeCheck = mb_strlen($generatedPdf, '8bit') > 4000;
		if (!$sizeCheck) {
			CakeLog::write('debug', 'PdfBinaryCheck: Output file is too small to be correct.');
			return false;
		}

		return true;
	}

	public function pdfBinPath_value()
	{
		$setting = $this->_blankModel()->find('first', [
			'conditions' => [
				'variable' => 'PDF_PATH_TO_BIN'
			],
			'recursive' => -1
		]);

		if (!empty($setting)) {
			return $setting['BootstrapSetting']['value'];
		}

		return false;
	}

	/**
	 * Get the label for a system health check status.
	 * 
	 * @param  int|null     $status Status of a check
	 * @return string|array         Label for the status or the array of labels if status is NULL.
	 */
	public static function getSystemHealthStatuses($status = null)
	{
		$statuses = array(
			self::SYSTEM_HEALTH_NOT_OK => __('Not OK'),
			self::SYSTEM_HEALTH_OK => __('OK')
		);

		if ($status === null) {
			return $statuses;
		}

		return $statuses[$status];
	}

	/**
	 * Get the label for a system health check criticality.
	 * 
	 * @param  int|null     $type Criticality of a check
	 * @return string|array       Label for the criticalities or the array of labels value is NULL.
	 */
	public static function getSystemHealthCriticality($type = null)
	{
		$types = array(
			self::SYSTEM_HEALTH_DESIRED => __('Desired'),
			self::SYSTEM_HEALTH_CRITICAL => __('Critical')
		);

		if (empty($type)) {
			return $types;
		}

		return $types[$type];
	}

	/**
	 * Checks PHP version.
	 * @return boolean True when supports.
	 */
	public static function phpVersion()
	{
		if (!version_compare(PHP_VERSION, self::PHP_VERSION_REQUIRED, '>=')) {
			return false;
		}

		return true;
	}

	public static function phpVersion_value()
	{
		return PHP_VERSION;
	}

	public static function mysql($version = null)
	{
		$ret = true;

		if (!$version) {
			return false;
		}

		$version = strtolower($version);
		$isMariaDB = strpos($version, 'mariadb') !== false;

		if ($isMariaDB) {
			// i.e. 5.5.5-10.1.21-MariaDB or 10.1.21-MariaDB or 10.1.26-MariaDB-0+deb9u1
			$versionParts = explode('-', $version);
			$mariadbKey = array_search('mariadb', $versionParts);
			if ($mariadbKey !== 0) {
				$versionKey = $mariadbKey-1;
			}
			else {
				$versionKey = count($versionParts)-2;
			}

			$version = $versionParts[$versionKey];
			$ret &= version_compare($version, self::MARIADB_VERSION_REQUIRED, '>=');
		}
		else {
			$ret &= version_compare($version, self::MYSQL_VERSION_REQUIRED, '>=');
		}

		return (bool) $ret;
	}

	public static function mysql_value()
	{
		$ds = ConnectionManager::getDataSource('default');

		// check if mysql extension is loaded @see Mysql::enabled()
		if (!$ds->enabled()) {
			return false;
		}

		// first check the server version via PDO
		$version = $ds->getVersion();
		if (empty($version) || is_bool($version)) {
			// fallback for getting the version but from a global variable
			$version = $ds->query("SELECT @@version;");
			$version = $version[0][0]['@@version'];
		}

		if (!$version) {
			return false;
		}

		return $version;
	}

	public static function backups()
	{
		$ret = true;

		if (!defined('BACKUPS_ENABLED') || empty(BACKUPS_ENABLED)) {
			$ret = false;
		}

		return $ret;
	}

	public static function backups_value()
	{
		return (static::backups()) ? __('Enabled') : __('Disabled');
	}

	public static function openssl()
	{
		$ret = extension_loaded('openssl');
		$ret &= defined('OPENSSL_TLSEXT_SERVER_NAME') && OPENSSL_TLSEXT_SERVER_NAME;
		$ret &= defined('OPENSSL_VERSION_NUMBER') && OPENSSL_VERSION_NUMBER >= 0x009080bf;

		return $ret;
	}

	public static function openssl_value()
	{
		return OPENSSL_VERSION_TEXT;
	}

	public static function curl() {
		return extension_loaded('curl');
	}

	public static function ldap() {
		return extension_loaded('ldap') && function_exists('ldap_connect');
	}

	public static function mail() {
		return function_exists('mail');
	}

	public static function fileinfo() {
		return extension_loaded('fileinfo');
	}

	public static function mbstring() {
		return extension_loaded('mbstring');
	}

	public static function gd() {
		return extension_loaded('gd');
	}

	public static function exif() {
		return extension_loaded('exif');
	}

	public static function zlib() {
		return extension_loaded('zlib');
	}

	public static function maxExecutionTime($value) {
		return $value >= self::PHP_MAX_EXECUTION_TIME;
	}
	public static function maxExecutionTime_value() {
		return ini_get('max_execution_time');
	}

	public static function maxInputVars($value) {
		return $value >= self::PHP_MAX_INPUT_VARS;
	}
	public static function maxInputVars_value() {
		return ini_get('max_input_vars');
	}

	public static function procOpen() {
		return function_exists('proc_open');
	}
	public static function procOpen_value() {
		return self::procOpen() ? __('Enabled') : __('Disabled');
	}

	public static function memoryLimit($value) {
		return ((int) $value) >= ((int) self::PHP_MEMORY_LIMIT);
	}
	public static function memoryLimit_value() {
		return ini_get('memory_limit');
	}

	public static function writeTmp() {
		return is_writable(TMP);
	}

	public static function writeCache() {
		return is_writable(CACHE);
	}
	public static function writeLogs() {
		return is_writable(LOGS);
	}
	public static function writeFiles() {
		return is_writable(WWW_ROOT . 'files' . DS);
	}
	public static function writeMedia() {
		return is_writable(WWW_ROOT . 'media' . DS);
	}
	public static function writeBackups() {
		return is_writable(WWW_ROOT . 'backups' . DS);
	}
	public static function writeQueue() {
		return is_writable(self::getDataPath());
	}

	/**
     * queue email data path
     * 
     * @return String
     */
    public static function getDataPath() {
        return APP . 'Vendor' . DS . 'queue' . DS;
    }

	public static function postSize($value) {
		$postMaxSize = self::returnBytes($value);
		if (($postMaxSize/1024/1024) >= self::PHP_UPLOAD_LIMIT) {
			return true;
		}

		return false;
	}
	public static function postSize_value() {
		return ini_get('post_max_size');
	}

	public static function uploadFilesize($value) {
		$postMaxSize = self::returnBytes($value);
		if (($postMaxSize/1024/1024) >= self::PHP_UPLOAD_LIMIT) {
			return true;
		}

		return false;
	}
	public static function uploadFilesize_value() {
		return ini_get('upload_max_filesize');
	}

	public static function pharDataClass() {
		return class_exists('PharData') && class_exists('Phar');
	}

	public static function zipArchiveClass() {
		return class_exists('ZipArchive');
	}

	public static function intl() {
		return extension_loaded('intl');
	}

	public static function simplexml() {
		return extension_loaded('simplexml');
	}

	public static function allow_url_fopen() {
		return ini_get('allow_url_fopen') == 1;
	}

	/**
	 * Convert post_max_size value to bytes.
	 */
	public static function returnBytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		$val = (int) $val;

		switch($last) {
			// El modificador 'G' está disponble desde PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

}