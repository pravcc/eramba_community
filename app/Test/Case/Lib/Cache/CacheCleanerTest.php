<?php
App::uses('CacheCleaner', 'Lib/Cache');
App::uses('Cache', 'Cache');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

/**
 * Test case
 */
class CacheCleanerTest extends CakeTestCase {

    public $testFolderPath = CACHE . 'test' . DS;

    public function setUp() {
        parent::setUp();

        $this->removeTestFolder();
    }

    public function testDeleteCache() {
        $folder = new Folder($this->testFolderPath, true);

        $this->assertEqual(count($folder->tree()[1]), 0);

        Cache::config('test', am(
            [
                'duration'=> '+1 day',
                'prefix' => false,
                'path' => $this->testFolderPath,
            ],
            Configure::read('cacheOptions')
        ));

        Cache::write('test', 'test info', 'test');

        $this->assertEqual(count($folder->tree()[1]), 1);

        $data = Cache::read('test', 'test');

        $this->assertNotEmpty($data);

        $file = new File($this->testFolderPath . 'test');
        $file->open('r');
        flock($file->handle, LOCK_SH);

        CacheCleaner::deleteCache($this->testFolderPath);

        $this->assertEqual(count($folder->tree()[1]), 0);
    }

    public function tearDown() {
        parent::tearDown();

        $this->removeTestFolder();
    }

    protected function removeTestFolder() {
        $folder = new Folder($this->testFolderPath);
        $folder->delete();
    }

}
