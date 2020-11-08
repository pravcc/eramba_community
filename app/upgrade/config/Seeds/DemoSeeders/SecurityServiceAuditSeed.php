<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServiceAudit seed.
 */
class SecurityServiceAuditSeed extends AbstractSeed
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
                'security_service_id' => '1',
                'audit_metric_description' => 'Input: 
- HR needs to provide the list of employees that left the company since the begining of the year and their last day in the company as columns A and B. The name of the employee must be its login name (john.foo)
- AD team needs to provide the list of disable accounts, the date they were disabled and their last successfull login as column A and B.
Analysis:
- For each row in the list of employees, validate the account has been disabled and no logins existed after its last day of work.
Output:
- A merge of both spreadsheets showing all accounts are disabled and no logins occured after the last login.',
                'audit_success_criteria' => 'All accounts have been disabled before the employee left the organisation.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-03-15',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '2',
                'security_service_id' => '2',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from Nessus  

Analysis: 
- Sort all findings by their criticality, take only those tagged as "High"
- Identify a ticket on the system that addresses the vuln and confirm it has been corrected on the scan that follows.

Output:
- A spreadsheet that includes the vuln id, the ticket used to mitigate the issue, the date it was fixed and the monthly scan that shows the issue i no longer recurring.',
                'audit_success_criteria' => 'All "high" finidings mitigated withing 60 days.',
                'result' => '1',
                'result_description' => 'Audit went well, attached is the evidence of this control review.',
                'user_id' => '3',
                'planned_date' => '2017-01-02',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-11',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:47:13',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '3',
                'security_service_id' => '3',
                'audit_metric_description' => 'Input:
- CCTV recordings from four random offices

Analysis:
- Validate recordings exist for up to 90 days

Output:
- Evidence from the analysis',
                'audit_success_criteria' => 'All videocamara running and 90 days of recording exists',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-01-04',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '4',
                'security_service_id' => '4',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from our supplier  

Analysis: 
- Make sure the monthly service has been completed
- Make sure none fire extinguishers is not expired.

Evidence: 
- Screenshot of every fire extinguisher
- Sensors report reviewed',
                'audit_success_criteria' => 'All fire detectors and motion detectors are operating, fire extinguishers are not expired.',
                'result' => '1',
                'result_description' => 'All audits went well.',
                'user_id' => '3',
                'planned_date' => '2017-01-26',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-11',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:49:07',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '5',
                'security_service_id' => '5',
                'audit_metric_description' => 'Input: 
- HR must provide the list of current employees.
- Awareness reports from eramba

Analysis: 
- Compare HR records against the list of completed trainings, tag those employees that miss a training for the calendar year.

Output:
- Spreadhseet used for analysis',
                'audit_success_criteria' => 'The number of non-compliant employees should not exceed %5.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-06-30',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '6',
                'security_service_id' => '6',
                'audit_metric_description' => 'Input
- Random selection of 10 computers at each office
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-04',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '7',
                'security_service_id' => '7',
                'audit_metric_description' => 'Input:
- List of employees that left the company in the last year
- List of badged assinged (report on the system) to each employee

Analysis:
- Compare both inputs and make sure that there are no active badges assigned to employees who have left the organisation.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'There are no active cards assigned to ex-employees.',
                'result' => '1',
                'result_description' => 'Audit went well.',
                'user_id' => '3',
                'planned_date' => '2017-02-05',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-30',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:50:03',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '8',
                'security_service_id' => '8',
                'audit_metric_description' => 'Input: 
- List of the database engine system accounts for all three systems: CRM, ERP and SAP
- Connection log for these three systems from Splunk
- Current list of system administrators (database)

Analysis:
- Review connection logs to make sure only application users and system administrators have connected to the databases.

Output:
- Spreadsheet validating each database connection.',
                'audit_success_criteria' => 'Only system administrators and applications service accounts have connected to the database.',
                'result' => '1',
                'result_description' => 'Audit went well',
                'user_id' => '4',
                'planned_date' => '2017-03-13',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-11',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:51:34',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '9',
                'security_service_id' => '9',
                'audit_metric_description' => 'Input:
- Random selection of %5 from the list of new employees in this calendar year
- List of people that have signed our NDA

Anallysis:
- For each employee in the sample, ensure there is a signed NDA.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All employees NDA\'s and security policies must be signed',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-04-11',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '10',
                'security_service_id' => '10',
                'audit_metric_description' => 'Input:
- Datacenter standards
- Review all data-centers, data-rooms and server-rooms

Analysis:
- Visit each site and make sure they comply with standards

Output:
- Spreadsheet with the analysis, for each standard requirement a Yes / No.',
                'audit_success_criteria' => 'All data rooms must comply with %100 of the requirements',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-06-19',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '11',
                'security_service_id' => '11',
                'audit_metric_description' => 'Input:
- Firewall change logs from Splunk
- All network changes on a spreadsheet report

Analysis:
- For each firewall change on the DMZ, review what changes where approved for that day.

Output:
- Every line on the firewall change matches a change that was approved on that day.',
                'audit_success_criteria' => 'All changes on the firewall recorded on Splunk must have a change ticket associated',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-24',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '12',
                'security_service_id' => '12',
                'audit_metric_description' => 'Input
- Random selection of 10 system on each datacenter created in the last month.
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-09-04',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '13',
                'security_service_id' => '13',
                'audit_metric_description' => 'Input
- Random selection of 10 system on each datacenter created in the last month.
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-09-04',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '14',
                'security_service_id' => '14',
                'audit_metric_description' => 'Input: 
- List of users in SAP application 
- List of all user account modifications since the begining of the year
- List of change requests for the queue assigned to SAP Account management

Method: 
- HR &amp; Finance managers must validate current users are corectly assigned
- A change request must exist for each account modification

Output:
- Spreadsheet with analysis',
                'audit_success_criteria' => 'Every user in SAP Application must be validated for his manager and all changes must have gone trough the correct process.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-03-19',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '15',
                'security_service_id' => '15',
                'audit_metric_description' => 'Input:
- Patching status from WSUS for the list of critical systems
- Patching policies

Analysis:
- All critical systems are patched according to our policies and standards

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All critical patches have been applied in time',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-11-02',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '16',
                'security_service_id' => '16',
                'audit_metric_description' => 'Input:
- Database of current contractors, their login account and stakeholders in the organisation
- NDA signatures report

Analysis:
- Ensure all current contractors are valid by getting a sign-off from the applicable stakeholder
- Ensure each contractor has a signed NDA
- Ensure each contractor has a default expiration date

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All contractors have ongoing activities, NDA signed and a default expiration date.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-01-20',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '17',
                'security_service_id' => '17',
                'audit_metric_description' => 'Input:
- All APs configurations
- Splunk logs with all AP changes

Analysis:
- Ensure TACACs and WPA2 is the only authentication / encryption method.
- Review all Splunk configuration changes for APs, ensure no-change is realted to AP settings.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'APs only use TACACs and WPA2',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-02-04',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '18',
                'security_service_id' => '18',
                'audit_metric_description' => 'Input:
- Simulate a power loss by bypassing main electricity inputs, the engine should start inmediately and no power cut should occur on the main datacenter as UPS would hold the short lack of power.

Output:
- Record what was observed on the test',
                'audit_success_criteria' => 'No power cut on any system in the DC',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-14',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '19',
                'security_service_id' => '19',
                'audit_metric_description' => 'Input: 
- List of the system accounts for all three systems: CRM, ERP and SAP
- Connection log for these three systems from Splunk
- Current list of system administrators (unix)

Analysis:
- Review connection logs to make sure only application users and system administrators have connected to the databases.

Output:
- Spreadsheet validating each database connection.',
                'audit_success_criteria' => 'Only system administrators and applications service accounts have connected to the database.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-03-19',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '20',
                'security_service_id' => '20',
                'audit_metric_description' => 'Input:
- List of critical AD groups
- Splunk logs with all group changes on these groups
- Change request report for group assignations

Analysis:
- Review each log on Splunk has a ticket asociated

Output:
- Spreadsheet Analysis',
                'audit_success_criteria' => 'All group modifications have a change request ticket asociated.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-01-01',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '21',
                'security_service_id' => '21',
                'audit_metric_description' => 'Input: 
- List of AD accounts where the "password expiration field" is set to "never".  

Analysis: 
- Review that each account has a ticket, expiration and they are still valid.',
                'audit_success_criteria' => 'No service accounts without a valid ticket and expiration',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-06-16',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '22',
                'security_service_id' => '22',
                'audit_metric_description' => 'Input:
- List of access to see secured passwords on cluster A and B
- List of change requests to open secured passwords

Analysis:
- Review logs and make sure that if a password was accessed on cluster A, the same happened for cluster B.
- Ensure change requests existed for each password access

Output:
- Spreadsheet with analysis
',
                'audit_success_criteria' => 'Passwords are not accessed by one team without the approval of the other.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-03-05',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '23',
                'security_service_id' => '23',
                'audit_metric_description' => 'Input:
- Get Splunk logs for backup records on all critical systems
- Get the list of planned restored tests
- Get the list of all change requests for backups/restores on critical systems

Analysis:
- For each splunk log, ensure that if a backup failed, the next backup did work
- Get the list of restore tests and validate they have been executed (by reviewing change requests) properly

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All backups work correctly and restores (due a need or test) have been performed as per policies.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-01-28',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '24',
                'security_service_id' => '24',
                'audit_metric_description' => 'Input: 
- VPN server configurations
- List of VPN connections from Splunk
- List of employees that left the organisation during the last year

Analysis:
- Configurations on VPN  servers enforce that only AD valid accounts can login
- The comparison in between VPN logs and employees that left the organisation show no ex-employee ever connected to the VPN

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'Only employees can VPN to the organisation',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-10',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:39',
                'modified' => '2017-04-10 13:28:39',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '25',
                'security_service_id' => '25',
                'audit_metric_description' => 'Input: 
- Updated policies from AD, ERM, CRM and SAP
- Screenshots from AD, where we can see the password configuration (GPO) 
- RSPARAM report from SAP 
- Screenshots from SOX Databases and operating systems where we can see that users (persons) are authenticating with AD
- Review Splunk logs for GPO changes  

Analysis: 
- Ensure ERM, CRP and SAP all authenticate against AD
- Ensure AD GPO password policy matches out corporate policy standards for passwords
- Review that GPO changes did not affect password settings

Output:
- Spreadsheet with analysis',
                'audit_success_criteria' => 'All critical systems authenticate using AD and AD has the correct password policy enforced.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-04-15',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:39',
                'modified' => '2017-04-10 13:28:39',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '26',
                'security_service_id' => '1',
                'audit_metric_description' => 'Input: 
- HR needs to provide the list of employees that left the company since the begining of the year and their last day in the company as columns A and B. The name of the employee must be its login name (john.foo)
- AD team needs to provide the list of disable accounts, the date they were disabled and their last successfull login as column A and B.
Analysis:
- For each row in the list of employees, validate the account has been disabled and no logins existed after its last day of work.
Output:
- A merge of both spreadsheets showing all accounts are disabled and no logins occured after the last login.',
                'audit_success_criteria' => 'All accounts have been disabled before the employee left the organisation.',
                'result' => '1',
                'result_description' => 'The audit went well, all accounts have been disabled.',
                'user_id' => '2',
                'planned_date' => '2017-02-11',
                'start_date' => '2017-04-11',
                'end_date' => '2017-04-11',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:44:21',
                'modified' => '2017-04-11 12:45:22',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '27',
                'security_service_id' => '2',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from Nessus  

Analysis: 
- Sort all findings by their criticality, take only those tagged as "High"
- Identify a ticket on the system that addresses the vuln and confirm it has been corrected on the scan that follows.

Output:
- A spreadsheet that includes the vuln id, the ticket used to mitigate the issue, the date it was fixed and the monthly scan that shows the issue i no longer recurring.',
                'audit_success_criteria' => 'All "high" finidings mitigated withing 60 days.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-11',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:46:32',
                'modified' => '2017-04-11 12:46:32',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '28',
                'security_service_id' => '4',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from our supplier  

Analysis: 
- Make sure the monthly service has been completed
- Make sure none fire extinguishers is not expired.

Evidence: 
- Screenshot of every fire extinguisher
- Sensors report reviewed',
                'audit_success_criteria' => 'All fire detectors and motion detectors are operating, fire extinguishers are not expired.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-10-11',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:48:11',
                'modified' => '2017-04-11 12:48:11',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '29',
                'security_service_id' => '8',
                'audit_metric_description' => 'Input: 
- List of the database engine system accounts for all three systems: CRM, ERP and SAP
- Connection log for these three systems from Splunk
- Current list of system administrators (database)

Analysis:
- Review connection logs to make sure only application users and system administrators have connected to the databases.

Output:
- Spreadsheet validating each database connection.',
                'audit_success_criteria' => 'Only system administrators and applications service accounts have connected to the database.',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-11-11',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:51:00',
                'modified' => '2017-04-11 12:51:00',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '30',
                'security_service_id' => '26',
                'audit_metric_description' => 'Random review of laptops making sure they are encrypted',
                'audit_success_criteria' => 'All laptops encrypted',
                'result' => NULL,
                'result_description' => '',
                'user_id' => NULL,
                'planned_date' => '2017-09-11',
                'start_date' => NULL,
                'end_date' => NULL,
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:05:02',
                'modified' => '2017-04-11 13:05:02',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('security_service_audits');
        $table->insert($data)->save();
    }
}
