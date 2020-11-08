<?php
App::uses('File', 'Utility');

/**
 * BackupRestore Test Case
 */
class BackupRestoreControllerTest extends ControllerTestCase {

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.CakeSession', 'app.Setting', 'app.SettingGroup', 'app.User', 'app.Workflow', 
        'app.LdapConnectorAuthentication', 'app.LdapConnector',
    );

    private $fixtureTables = array(
        'cake_sessions', 'settings', 'setting_groups', 'users', 'workflows', 
        'ldap_connector_authentication', 'ldap_connectors'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();

        $this->BackupRestore = ClassRegistry::init('BackupRestore.BackupRestore');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        parent::tearDown();
    }

    private function dropAllTables() {
        $db = ConnectionManager::getDataSource('test');
        $data = $db->query('SHOW TABLES');

        $tables = array();

        foreach ($data as $item) {
            $tableName = $item['TABLE_NAMES']['Tables_in_eramba_test'];
            if (!in_array($tableName, $this->fixtureTables)) {
                $tables[] = $tableName;
            }
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 0; DROP TABLE ' . implode(',', $tables) . ';');
    }

    /**
     * @return void
     */
    public function testIndex() {
        $db = ConnectionManager::getDataSource('test');

        $data = array(
            'BackupRestore' => array(
                'ZipFile' => array(
                    'name' => 'eramba_backup_test.zip',
                    'type' => 'application/octet-stream',
                    'tmp_name' => './../Module/BackupRestore/Test/Fixture/eramba_backup_test.zip',
                    'error' => 0,
                    'size' => 227606
                )
            )
        );

        $beforeTablesCount = count($db->query('SHOW TABLES'));

        $result = $this->testAction(
            '/backupRestore/index',
            array('return' => 'vars', 'method' => 'post', 'data' => $data)
        );

        $afterTablesCount = count($db->query('SHOW TABLES'));

        $this->assertNotEqual($afterTablesCount, $beforeTablesCount);

        $this->WorkflowLog = ClassRegistry::init('WorkflowLog');
        $log = $this->WorkflowLog->find('first', array(
            'conditions' => array('WorkflowLog.id' => 1)
        ));

        $this->assertEqual(!empty($log), true);

        $this->dropAllTables();
    }

    /**
     * @return void
     */
    public function testIndexWrongUpload() {
        $db = ConnectionManager::getDataSource('test');
        
        $data = array(
            'BackupRestore' => array(
                'ZipFile' => array(
                    'name' => 'picture.png',
                    'type' => 'image/png',
                    'tmp_name' => './../Module/BackupRestore/Test/Fixture/picture.png',
                    'error' => 0,
                    'size' => 123456
                )
            )
        );

        $beforeTablesCount = count($db->query('SHOW TABLES'));

        $result = $this->testAction(
            '/backupRestore/index',
            array('return' => 'vars', 'method' => 'post', 'data' => $data)
        );

        $afterTablesCount = count($db->query('SHOW TABLES'));

        $this->assertEqual($afterTablesCount, $beforeTablesCount);
    }
}
