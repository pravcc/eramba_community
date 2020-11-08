<?php
/**
 * SettingGroup Fixture
 */
class SettingGroupFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parent_slug' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'icon_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'notes' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hidden' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'unsigned' => false),
		'order' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'slug' => array('column' => 'slug', 'unique' => 1),
			'FK_setting_groups_setting_groups' => array('column' => 'parent_slug', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'slug' => 'ACCESSLST',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Access Lists',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"admin", "action":"acl", "0" :"aros", "1":"ajax_role_permissions"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '2',
			'slug' => 'ACCESSMGT',
			'parent_slug' => null,
			'name' => 'Access Management',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '3',
			'slug' => 'AUTH',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Authentication ',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"ldapConnectors","action":"authentication"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '4',
			'slug' => 'BANNER',
			'parent_slug' => 'SEC',
			'name' => 'Banners',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '1',
			'order' => '0'
		),
		array(
			'id' => '5',
			'slug' => 'BAR',
			'parent_slug' => 'DB',
			'name' => 'Backup & Restore',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"backupRestore","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '6',
			'slug' => 'BFP',
			'parent_slug' => 'SEC',
			'name' => 'Brute Force Protection',
			'icon_code' => null,
			'notes' => 'This setting allows you to protect the login page of eramba from being brute-force attacked.',
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '7',
			'slug' => 'CUE',
			'parent_slug' => 'LOC',
			'name' => 'Currency',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '8',
			'slug' => 'DASH',
			'parent_slug' => null,
			'name' => 'Dashboard',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '9',
			'slug' => 'DASHRESET',
			'parent_slug' => 'DASH',
			'name' => 'Reset Dashboards',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"resetDashboards"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '10',
			'slug' => 'DB',
			'parent_slug' => null,
			'name' => 'Database',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '11',
			'slug' => 'DBCNF',
			'parent_slug' => 'DB',
			'name' => 'Database Configurations',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '1',
			'order' => '0'
		),
		array(
			'id' => '12',
			'slug' => 'DBRESET',
			'parent_slug' => 'DB',
			'name' => 'Reset Database',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"resetDatabase"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '13',
			'slug' => 'DEBUG',
			'parent_slug' => null,
			'name' => 'Debug Settings and Logs',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '14',
			'slug' => 'DEBUGCFG',
			'parent_slug' => 'DEBUG',
			'name' => 'Debug Config',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '15',
			'slug' => 'ERRORLOG',
			'parent_slug' => 'DEBUG',
			'name' => 'Error Log',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"logs", "0":"error"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '16',
			'slug' => 'GROUP',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Groups ',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"groups","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '17',
			'slug' => 'LDAP',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'LDAP Connectors',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"ldapConnectors","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '18',
			'slug' => 'LOC',
			'parent_slug' => null,
			'name' => 'Localization',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '19',
			'slug' => 'MAIL',
			'parent_slug' => null,
			'name' => 'Mail',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '20',
			'slug' => 'MAILCNF',
			'parent_slug' => 'MAIL',
			'name' => 'Mail Configurations',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '21',
			'slug' => 'MAILLOG',
			'parent_slug' => 'DEBUG',
			'name' => 'Email Log',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"logs", "0":"email"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '22',
			'slug' => 'PRELOAD',
			'parent_slug' => 'DB',
			'name' => 'Pre-load the database with default databases',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '1',
			'order' => '0'
		),
		array(
			'id' => '23',
			'slug' => 'RISK',
			'parent_slug' => null,
			'name' => 'Risk',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '1',
			'order' => '0'
		),
		array(
			'id' => '24',
			'slug' => 'RISKAPPETITE',
			'parent_slug' => 'RISK',
			'name' => 'Risk appetite',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '25',
			'slug' => 'ROLES',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Roles',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"scopes","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '26',
			'slug' => 'SEC',
			'parent_slug' => null,
			'name' => 'Security',
			'icon_code' => 'icon-cog',
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '27',
			'slug' => 'SECKEY',
			'parent_slug' => 'SEC',
			'name' => 'Security Key',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '28',
			'slug' => 'USER',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'User Management',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"users","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '29',
			'slug' => 'CLRCACHE',
			'parent_slug' => 'DEBUG',
			'name' => 'Clear Cache',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"deleteCache"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '30',
			'slug' => 'CLRACLCACHE',
			'parent_slug' => 'DEBUG',
			'name' => 'Clear ACL Cache',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"deleteCache", "0":"acl"}',
			'hidden' => '1',
			'order' => '0'
		),
		array(
			'id' => '31',
			'slug' => 'LOGO',
			'parent_slug' => 'LOC',
			'name' => 'Custom Logo',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"customLogo"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '32',
			'slug' => 'HEALTH',
			'parent_slug' => 'SEC',
			'name' => 'System Health',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"settings","action":"systemHealth"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '33',
			'slug' => 'TZONE',
			'parent_slug' => 'LOC',
			'name' => 'Timezone',
			'icon_code' => null,
			'notes' => null,
			'url' => null,
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '34',
			'slug' => 'UPDATES',
			'parent_slug' => 'SEC',
			'name' => 'Updates',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"updates","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '35',
			'slug' => 'NOTIFICATION',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Notifications',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"notificationSystem","action":"listItems"}',
			'hidden' => '0',
			'order' => '0'
		),
		array(
			'id' => '36',
			'slug' => 'CRON',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Cron Jobs',
			'icon_code' => null,
			'notes' => null,
			'url' => '{"controller":"cron","action":"index"}',
			'hidden' => '0',
			'order' => '0'
		),
	);

}
