<?php
/**
 * LdapConnectorAuthentication Fixture
 */
class LdapConnectorAuthenticationFixture extends CakeTestFixture {

	public $table = 'ldap_connector_authentication';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'auth_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false),
		'auth_users_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'auth_awareness' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false),
		'auth_awareness_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'auth_policies' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false),
		'auth_policies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'auth_users_id' => array('column' => 'auth_users_id', 'unique' => 0),
			'auth_awareness_id' => array('column' => 'auth_awareness_id', 'unique' => 0),
			'auth_policies_id' => array('column' => 'auth_policies_id', 'unique' => 0)
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
			'auth_users' => '0',
			'auth_users_id' => '4',
			'auth_awareness' => '1',
			'auth_awareness_id' => '4',
			'auth_policies' => '1',
			'auth_policies_id' => '4',
			'modified' => '2016-10-16 22:17:59'
		),
	);

}
