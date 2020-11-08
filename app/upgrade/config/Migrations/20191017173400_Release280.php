<?php
use Phinx\Migration\AbstractMigration;

class Release280 extends AbstractMigration
{
    public $newVersion = 'c2.8.0';
    public $previousVersion = 'c2.7.2';

    protected function bumpVersion($value) {
        $ret = true;

        $this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

        if (class_exists('App')) {
            $status = [];
            App::uses('Configure', 'Core');

            if (class_exists('Configure')) {
                Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
            }

            // testing handler for exception
            if (Configure::read('Eramba.TRIGGER_UPDATE_FAIL') === true) {
                $status['ConfiguredFailTriggered'] = true;
                throw new Exception("This is a test exception for failed update.", 1);
                return false;
            }

            App::uses('ConnectionManager', 'Model');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('Setting')->deleteCache(null);

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();
            
            ClassRegistry::flush();

            if (Configure::read('Eramba.version') === $this->previousVersion) {
                $reportsFolderPath = APP . DS . 'Module' . DS . 'Reports';
                if (is_dir($reportsFolderPath) === true) {
                    App::uses('CakeLog', 'Log');
                    $log = "App Module Reports still exists in your application, Community version doesnt allow this!";
                    CakeLog::write('error', "{$log}");

                    throw new Exception($log, 1);
                    return false;
                }
            }

            ClassRegistry::init('Setting')->deleteCache(null);
        }

        if (!$ret) {
            App::uses('CakeLog', 'Log');
            $log = "Error occured when processing database synchronization for " . $this->newVersion;
            CakeLog::write('error', "{$log} \n" . print_r($status, true));

            throw new Exception($log, 1);
            return false;
        }
    }

    public function up()
    {
        $this->bumpVersion($this->newVersion);
    }

    public function down()
    {
        $this->bumpVersion($this->previousVersion);
    }
}
