<?php
App::uses('BackupRestore', 'BackupRestore.Model');
App::uses('File', 'Utility');

/**
 * BackupRestore Test Case
 */
class BackupRestoreTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    // public $fixtures = array(
    //  'app.backup_restore'
    // );

/**
 * setUp method
 *
 * @return void
 */
    public function setUp() {
        parent::setUp();

        $this->testFilePath = TMP . 'test_backup_db.sql';
        $this->BackupRestoreTest = ClassRegistry::init('BackupRestore.BackupRestore');
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
        unset($this->BackupRestore);

        $file = new File($this->testFilePath);
        if ( $file ) {
            $file->delete();
        }

        parent::tearDown();
    }

/**
 * testBackupDatabase method
 *
 * @return void
 */
    public function testBackupDatabase() {
        $bak = $this->BackupRestoreTest->backupDatabase($this->testFilePath, true);

        $this->assertTrue($bak);
        $this->assertTrue(file_exists($this->testFilePath));
    }

/**
 * testRestoreDatabase method
 *
 * @return void
 */
    // public function testRestoreDatabase() {
    //  $this->markTestIncomplete('testRestoreDatabase not implemented.');
    // }

}
