<?php
/**
 * Acl DataAsset Shell.
 */
App::uses('AppShell', 'Console/Command');

class DataAssetShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
    public $args;


/**
 * Constructor
 */
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

/**
 * Start up
 *
 * @return void
 **/
    public function startup() {
        parent::startup();
    }

    public function getOptionParser() {
        return parent::getOptionParser()
            ->description(__("Data Assets"));
    }

    public function add_instances() {
        $this->_loginAdmin();

        $this->out('Data Asset Instances synchronization started...');

        $ret = $this->_addMissingInstances();

        if ($ret) {
            $this->out('Data Asset Instances added to all assets that were missing it.');
        }
        else {
            $this->error('Data Asset Instances was not successfully added to all asset that were missing it.');
        }

        return $ret;
    }

    protected function _addMissingInstances() {
        $Asset = ClassRegistry::init('Asset');
        if (!method_exists($Asset, 'addMissingInstances')) {
            trigger_error('Asset model class is preloaded with previous file version and cannot sync data assets at this runtime execution.');
            $Instance = ClassRegistry::init('DataAssetInstance');

            $assets = $Asset->find('all', ['recursive' => -1]);

            $ret = true;

            foreach ($assets as $asset) {
                $ret &= $Asset->createDataAssetInstance($asset['Asset']['id']);
            }

            return $ret;
        }

       return $Asset->addMissingInstances();
    }

}
