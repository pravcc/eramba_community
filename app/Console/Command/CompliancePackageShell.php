<?php
/**
 * CompliancePackage Shell.
 */
App::uses('AppShell', 'Console/Command');

class CompliancePackageShell extends AppShell {

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
            ->description(__("Compliance Package"));
    }

    public function sync_items() {
        $this->_loginAdmin();

        $this->out('Compliance Package Items synchronization started...');

        $ret = $this->_syncPackageItems();

        if ($ret) {
            $this->out('Compliance Package Items were successfully synced.');
        }
        else {
            $this->error('Compliance Package Items were not synced successfully. Please try again.');
        }

        return $ret;
    }

    protected function _syncPackageItems() {
        $CompliancePackage = ClassRegistry::init('CompliancePackage');

        return $CompliancePackage->syncPackageItems();
    }

}
