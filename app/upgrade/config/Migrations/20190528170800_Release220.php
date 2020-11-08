<?php
use Phinx\Migration\AbstractMigration;

class Release220 extends AbstractMigration
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

            App::uses('ConnectionManager', 'Model');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('Setting')->deleteCache(null);

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();
            
            ClassRegistry::flush();

            if (Configure::read('Eramba.version') === 'e2.1.2') {
                // autoload the correct plugin model because ClassRegistry mapping may not be updated yet
                ClassRegistry::init('LdapSync.LdapSynchronizationSystemLog');
                ClassRegistry::init('LdapSynchronizationSystemLog');

                $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
                // sync new compliance pacakge regulator filters
                $ret &= $AdvancedFilter->syncDefaultIndex(null, [
                    'LdapSynchronizationSystemLog'
                ]);
            }

            ClassRegistry::init('Setting')->deleteCache(null);
        }

        if (!$ret) {
            App::uses('CakeLog', 'Log');
            $log = "Error occured when processing database synchronization for new template.";
            CakeLog::write('error', "{$log} \n" . print_r($status, true));

            throw new Exception($log, 1);
            return false;
        }
    }

    public function up()
    {
        $this->bumpVersion('e2.2.0');
    }

    public function down()
    {
        $this->bumpVersion('e2.1.2');
    }
}
