<?php
/**
 * User Fixture
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'surname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'login' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 45, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '0-non active, 1-active'),
		'blocked' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
		'local_account' => array('type' => 'integer', 'null' => true, 'default' => '1', 'length' => 3, 'unsigned' => false),
		'api_allow' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'login' => array('column' => 'login', 'unique' => 1),
			'email' => array('column' => 'email', 'unique' => 1),
			'group_id' => array('column' => 'group_id', 'unique' => 0)
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
			'name' => 'Admin',
			'surname' => 'Admin',
			'group_id' => '10',
			'email' => 'info@eramba.org',
			'login' => 'admin',
			'password' => '$2a$10$.IarQkXifyHrXhjfjvKRM.z589chQ1PNU86TCpM6ARDsTLngntQ16',
			'language' => 'eng',
			'status' => '1',
			'blocked' => '0',
			'local_account' => '1',
			'api_allow' => '0',
			'created' => '2013-10-14 16:19:04',
			'modified' => '2016-08-19 13:08:53'
		),
		array(
			'id' => '4',
			'name' => 'User',
			'surname' => 'User',
			'group_id' => '10',
			'email' => 'user@eramba.org',
			'login' => 'user',
			'password' => '$2a$10$.IarQkXifyHrXhjfjvKRM.z589chQ1PNU86TCpM6ARDsTLngntQ16',
			'language' => 'eng',
			'status' => '1',
			'blocked' => '0',
			'local_account' => '1',
			'api_allow' => '0',
			'created' => '2014-07-16 13:29:16',
			'modified' => '2016-05-19 11:03:23'
		),
		array(
			'id' => '5',
			'name' => 'John',
			'surname' => 'Foo',
			'group_id' => '10',
			'email' => 'john@Acme.com',
			'login' => 'john.foo',
			'password' => '$2a$10$N8QudnmXh9GD1cEgldxwD.sq9PXDvZdj/OqNQkcFn5XZKhYw0/wpS',
			'language' => 'eng',
			'status' => '1',
			'blocked' => '0',
			'local_account' => '1',
			'api_allow' => '0',
			'created' => '2014-07-16 13:29:41',
			'modified' => '2016-05-16 12:14:56'
		),
		array(
			'id' => '8',
			'name' => 'Gene',
			'surname' => 'Simmons',
			'group_id' => '10',
			'email' => 'gene@Acme.com',
			'login' => 'gene.simmons',
			'password' => '$2a$10$4IW0aozKeVNJiJFQD5L2t.KZOzFagQd2SMijgi2p.HZMalvf35jYO',
			'language' => 'eng',
			'status' => '1',
			'blocked' => '0',
			'local_account' => '0',
			'api_allow' => '0',
			'created' => '2015-02-10 13:34:05',
			'modified' => '2016-01-21 07:25:24'
		),
		array(
			'id' => '9',
			'name' => 'Laura',
			'surname' => 'Links',
			'group_id' => '11',
			'email' => 'laura@Acme.com',
			'login' => 'laura.links',
			'password' => '$2a$10$0mfQ5bDa0W7A2TVvEDcZJOcqHoSyKCjVD73b7cOZCXqXjfxUHdOYe',
			'language' => '',
			'status' => '1',
			'blocked' => '0',
			'local_account' => '1',
			'api_allow' => '0',
			'created' => '2015-03-03 09:04:23',
			'modified' => '2015-03-18 10:58:03'
		),
	);

}
