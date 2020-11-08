<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceAudit seed.
 */
class ComplianceAuditSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'name' => 'HIPAA Trust (Short Version)',
                'third_party_id' => '5',
                'auditor_id' => '2',
                'third_party_contact_id' => '3',
                'start_date' => '2017-04-10',
                'end_date' => '2017-04-30',
                'auditee_title' => 'HIPAA Trust (Short Version)',
                'auditee_instructions' => '- Answer each question',
                'use_default_template' => '1',
                'email_subject' => '',
                'email_body' => '',
                'auditee_notifications' => '0',
                'auditee_emails' => '0',
                'auditor_notifications' => '0',
                'auditor_emails' => '0',
                'show_analyze_title' => '1',
                'show_analyze_description' => '1',
                'show_analyze_audit_criteria' => '1',
                'show_findings' => '1',
                'status' => 'started',
                'compliance_finding_count' => '0',
                'created' => '2017-04-10 16:02:16',
                'modified' => '2017-04-10 16:02:16',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '2',
                'name' => 'Supplier Assessments against PCI-DSS',
                'third_party_id' => '4',
                'auditor_id' => '2',
                'third_party_contact_id' => '3',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-30',
                'auditee_title' => 'Welcome to our Assessments around PCI-DSS',
                'auditee_instructions' => '- Do this
- Do that
- Etc',
                'use_default_template' => '1',
                'email_subject' => '',
                'email_body' => '',
                'auditee_notifications' => '0',
                'auditee_emails' => '0',
                'auditor_notifications' => '0',
                'auditor_emails' => '0',
                'show_analyze_title' => '1',
                'show_analyze_description' => '1',
                'show_analyze_audit_criteria' => '1',
                'show_findings' => '1',
                'status' => 'started',
                'compliance_finding_count' => '0',
                'created' => '2017-04-11 13:59:11',
                'modified' => '2017-04-12 21:29:47',
                'deleted' => '1',
                'deleted_date' => '2017-04-12 21:29:47',
            ],
            [
                'id' => '3',
                'name' => 'Copy of HIPAA Trust (Short Version)',
                'third_party_id' => '5',
                'auditor_id' => '2',
                'third_party_contact_id' => '4',
                'start_date' => '2017-04-12',
                'end_date' => '2017-04-30',
                'auditee_title' => 'HIPAA Trust (Short Version)',
                'auditee_instructions' => '- Answer each question',
                'use_default_template' => '1',
                'email_subject' => '',
                'email_body' => '',
                'auditee_notifications' => '0',
                'auditee_emails' => '0',
                'auditor_notifications' => '0',
                'auditor_emails' => '0',
                'show_analyze_title' => '1',
                'show_analyze_description' => '1',
                'show_analyze_audit_criteria' => '1',
                'show_findings' => '1',
                'status' => 'started',
                'compliance_finding_count' => '0',
                'created' => '2017-04-12 21:30:40',
                'modified' => '2017-04-12 21:31:47',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('compliance_audits');
        $table->insert($data)->save();
    }
}
