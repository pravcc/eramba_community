<?php
/**
 * LdapConnector Fixture
 */
class LdapConnectorFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'host' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'domain' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'port' => array('type' => 'integer', 'null' => false, 'default' => '389', 'unsigned' => false),
		'ldap_bind_dn' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_bind_pw' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_base_dn' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_auth_filter' => array('type' => 'string', 'null' => true, 'default' => '(| (sn=%USERNAME%) )', 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_auth_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_name_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_email_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_memberof_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_grouplist_filter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_grouplist_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_groupmemberlist_filter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_group_account_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_group_fetch_email_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_group_email_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ldap_group_mail_domain' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false, 'comment' => '0-disabled,1-active'),
		'workflow_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
		'workflow_owner_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'id' => '4',
			'name' => 'LDAP Acme',
			'description' => 'Auth against Acme AD',
			'host' => 'ad.eramba.org',
			'domain' => 'eramba.org',
			'port' => '389',
			'ldap_bind_dn' => 'CN=Joe Ramone,OU=People,DC=corp,DC=eramba,DC=org',
			'ldap_bind_pw' => '@todo',
			'ldap_base_dn' => 'DC=corp,DC=eramba,DC=org',
			'type' => 'authenticator',
			'ldap_auth_filter' => '(&(objectcategory=user)(sAMAccountName=%USERNAME%))',
			'ldap_auth_attribute' => 'sAMAccountName',
			'ldap_name_attribute' => 'displayName',
			'ldap_email_attribute' => 'mail',
			'ldap_memberof_attribute' => 'memberOf',
			'ldap_grouplist_filter' => '',
			'ldap_grouplist_name' => '',
			'ldap_groupmemberlist_filter' => '',
			'ldap_group_account_attribute' => '',
			'ldap_group_fetch_email_type' => 'email-attribute',
			'ldap_group_email_attribute' => '',
			'ldap_group_mail_domain' => '',
			'status' => '1',
			'workflow_status' => '4',
			'workflow_owner_id' => '1',
			'created' => '2016-01-21 06:19:37',
			'modified' => '2016-10-16 22:15:14'
		),
		array(
			'id' => '5',
			'name' => 'Acme',
			'description' => '',
			'host' => 'ad.eramba.org',
			'domain' => 'eramba.org',
			'port' => '389',
			'ldap_bind_dn' => 'CN=Joe Ramone,OU=People,DC=corp,DC=eramba,DC=org',
			'ldap_bind_pw' => '@todo',
			'ldap_base_dn' => 'DC=corp,DC=eramba,DC=org',
			'type' => 'group',
			'ldap_auth_filter' => '(| (sn=%USERNAME%) )',
			'ldap_auth_attribute' => '',
			'ldap_name_attribute' => '',
			'ldap_email_attribute' => '',
			'ldap_memberof_attribute' => '',
			'ldap_grouplist_filter' => '(objectCategory=group)',
			'ldap_grouplist_name' => 'cn',
			'ldap_groupmemberlist_filter' => '(&(objectCategory=user)(memberOf=CN=%GROUP%,OU=Groups,DC=corp,DC=eramba,DC=org))',
			'ldap_group_account_attribute' => 'sAMAccountName',
			'ldap_group_fetch_email_type' => 'email-attribute',
			'ldap_group_email_attribute' => 'mail',
			'ldap_group_mail_domain' => '',
			'status' => '1',
			'workflow_status' => '4',
			'workflow_owner_id' => '1',
			'created' => '2016-08-25 19:35:50',
			'modified' => '2016-10-16 22:15:59'
		),
	);

}
