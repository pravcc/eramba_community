<?php
$answersCount = count($complianceAudit['ComplianceAuditSetting']);
$auditees = [];
$done = true;

foreach ($complianceAudit['ComplianceAuditSetting'] as $setting) {
    foreach ($setting['Auditee'] as $auditee) {
        if (empty($auditees[$auditee['id']])) {
            $auditees[$auditee['id']] = [
                'id' => $auditee['id'],
                'name' => $auditee['full_name'],
                'answers_assigned' => 0,
                'answers_missing' => 0,
            ];
        }

        $auditees[$auditee['id']]['answers_assigned']++;
        if (empty($setting['ComplianceAuditAuditeeFeedback'])) {
            $auditees[$auditee['id']]['answers_missing']++;
            $done = false;
        }
    }
}

if ($done) {
    echo __('All items have been responded, is now possible to submit this questionnaire!');
}
else {
    echo __('This questionnaire is composed of %s, of which:', $this->ComplianceAudits->itemsI18n($answersCount));

    foreach ($auditees as $auditee) {
        $item = sprintf(__n('%s is assigned to %s', '%s are assigned to %s', $auditee['answers_assigned']), $this->ComplianceAudits->itemsI18n($auditee['answers_assigned']), $auditee['name']);

        $itemMissing = '';
        if ($auditee['answers_missing'] == 0) {
            $itemMissing = __('All have been responded');
        }
        else {
            $itemMissing = sprintf(__n('%s is missing a response', '%s are missing a response', $auditee['answers_missing']), $this->ComplianceAudits->itemsI18n($auditee['answers_missing']));
        }

        echo '</br>' . $item . ' (' . $itemMissing . ')';
    }
}
?>
