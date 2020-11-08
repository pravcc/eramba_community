<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('LdapAuthenticate', 'Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

class LdapAuthenticateTest extends CakeTestCase {

	public $fixtures = array('core.user');

	public function setUp() {
		parent::setUp();
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = new LdapAuthenticate($this->Collection, array(
			'form_fields' => array('username' => 'user', 'password' => 'password'),
			'userModel' => 'User',
			'LdapConnector' => null
		));
		// $password = Security::hash('password', null, true);
		$User = ClassRegistry::init('User');
		// $User->updateAll(array('password' => $User->getDataSource()->value($password)));
		$this->response = $this->getMock('CakeResponse');
	}

/**
 * test applying settings in the constructor
 *
 * @return void
 */
	public function testConstructor() {
		$object = new LdapAuthenticate($this->Collection, array(
			'userModel' => 'AuthUser',
			'form_fields' => array('username' => 'user', 'password' => 'password'),
			'LdapConnector' => null
		));
		$this->assertEquals('AuthUser', $object->settings['userModel']);
		$this->assertEquals(array('username' => 'user', 'password' => 'password'), $object->settings['form_fields']);
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoData() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array();
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoUsername() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('User' => array('password' => 'foobar'));
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}
}