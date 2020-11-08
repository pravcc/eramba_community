<?php
use Phinx\Migration\AbstractMigration;

class Release41 extends AbstractMigration
{
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

            $cacheDisable = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);

            App::uses('ConnectionManager', 'Model');
            App::uses('DashboardShell', 'Dashboard.Console/Command');
            App::uses('ObjectStatusShell', 'ObjectStatus.Console/Command');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('Setting')->deleteCache(null);

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();

            // $DashboardShell = new DashboardShell();
            // $DashboardShell->startup();
            // $ret &= $DashboardShell->sync();
            
            if (Configure::read('Eramba.version') === 'e1.0.6.040') {
                $ObjectStatusShell = new ObjectStatusShell();
                $ObjectStatusShell->startup();
                $ret &= $ObjectStatusShell->sync_all_statuses();
            }

            Configure::write('Cache.disable', $cacheDisable);

            ClassRegistry::init('Setting')->deleteCache(null);
        }

        if (!$ret) {
            App::uses('CakeLog', 'Log');
            $log = "Error occured when processing database synchronization for release 1.0.6.041.";
            CakeLog::write('error', "{$log} \n" . print_r($status, true));

            throw new Exception($log, 1);
            return false;
        }
    }

    public function up()
    {
        $this->bumpVersion('e1.0.1.022');
    }

    public function down()
    {
        $this->bumpVersion('e1.0.1.021');
    }
}
