<?php
/**
 * Acl AssociativeDelete Shell.
 */
App::uses('AppShell', 'Console/Command');

class AssociativeDeleteShell extends AppShell {

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
            ->description(__("Associative Delete"));
    }

    public function delete_remaining_items() {
        $this->_loginAdmin();

        $this->out('Associative Delete processing not deleted OA items...');

        $ret = $this->_deleteRemainingVAItems();

        if ($ret) {
            $this->out('All associated OA items have been deleted succesfully.');
        }
        else {
            $this->error('Associated OA items have not been deleted succesfully.');
        }

        return $ret;
    }

    public function delete_remaining_compliance_items() {
        $this->_loginAdmin();

        $this->out('Associative Delete processing not deleted compliance items...');

        $ret = true;//$this->_deleteRemainingComplianceItems();

        if ($ret) {
            $this->out('All associated compliance items have been deleted succesfully.');
        }
        else {
            $this->error('Associated compliance items have not been deleted succesfully.');
        }

        return $ret;
    }

/**
 * Delete compliance items of deleted parent item.
 *
 * @return boolean
 **/
    protected function _deleteRemainingComplianceItems() {
        $ThirdParty = ClassRegistry::init('ThirdParty');
        $CompliancePackage = ClassRegistry::init('CompliancePackage');
        $CompliancePackageItem = ClassRegistry::init('CompliancePackageItem');

        $ret = true;

        $deletedCompliancePackageIds = $CompliancePackage->find('list', [
            'conditions' => [
                'CompliancePackage.deleted' => true
            ],
            'fields' => [
                'CompliancePackage.id'
            ],
            'recursive' => -1
        ]);

        if (!empty($deletedCompliancePackageIds)) {
            $packageItems = $CompliancePackageItem->find('all', [
                'conditions' => [
                    'CompliancePackageItem.compliance_package_id' => $deletedCompliancePackageIds,
                    'CompliancePackageItem.deleted' => false
                ],
                'fields' => [
                    'CompliancePackageItem.id'
                ],
                'recursive' => -1
            ]);

            foreach ($packageItems as $packageItem) {
                $ret &= (boolean) $CompliancePackageItem->delete($packageItem['CompliancePackageItem']['id']);
            }
        }

        $deletedThirdPartyIds = $ThirdParty->find('list', [
            'conditions' => [
                'ThirdParty.deleted' => true
            ],
            'fields' => [
                'ThirdParty.id'
            ],
            'recursive' => -1
        ]);

        if (!empty($deletedThirdPartyIds)) {
            $packages = $CompliancePackage->find('all', [
                'conditions' => [
                    'CompliancePackage.third_party_id' => $deletedThirdPartyIds,
                    'CompliancePackage.deleted' => false
                ],
                'fields' => [
                    'CompliancePackage.id'
                ],
                'recursive' => -1
            ]);

            foreach ($packages as $package) {
                $ret &= (boolean) $CompliancePackage->delete($package['CompliancePackage']['id']);
            }
        }

        return $ret;
    }

/**
 * Delete VA findings, VA feedbacks associated to deleted VA items.
 *
 * @return boolean
 **/
    protected function _deleteRemainingVAItems() {
        $VendorAssessment = ClassRegistry::init('VendorAssessments.VendorAssessment');
        $VendorAssessmentFinding = ClassRegistry::init('VendorAssessments.VendorAssessmentFinding');
        $VendorAssessmentFeedback = ClassRegistry::init('VendorAssessments.VendorAssessmentFeedback');

        $ret = true;

        $deletedVaIds = $VendorAssessment->find('list', [
            'conditions' => [
                'VendorAssessment.deleted' => true
            ],
            'fields' => [
                'VendorAssessment.id'
            ],
            'recursive' => -1
        ]);

        if (empty($deletedVaIds)) {
            return $ret;
        }

        $findings = $VendorAssessmentFinding->find('all', [
            'conditions' => [
                'VendorAssessmentFinding.vendor_assessment_id' => $deletedVaIds,
                'VendorAssessmentFinding.deleted' => false
            ],
            'resursive' => -1
        ]);

        foreach ($findings as $item) {
            $ret &= (boolean) $VendorAssessmentFinding->delete($item['VendorAssessmentFinding']['id']);
        }

        $feedbacks = $VendorAssessmentFeedback->find('all', [
            'conditions' => [
                'VendorAssessmentFeedback.vendor_assessment_id' => $deletedVaIds,
                'VendorAssessmentFeedback.deleted' => false
            ],
            'resursive' => -1
        ]);

        foreach ($feedbacks as $item) {
            $ret &= (boolean) $VendorAssessmentFeedback->delete($item['VendorAssessmentFeedback']['id']);
        }

        return $ret;
    }

}
