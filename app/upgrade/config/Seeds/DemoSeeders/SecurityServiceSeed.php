<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityService seed.
 */
class SecurityServiceSeed extends AbstractSeed
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
                'name' => 'Active Directory User Reviews - (Exits)',
                'objective' => 'Ensure that those employees that have left the company have no valid account in the AD and that his/her last login is previous to it\'s last day in the office.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- HR needs to provide the list of employees that left the company since the begining of the year and their last day in the company as columns A and B. The name of the employee must be its login name (john.foo)
- AD team needs to provide the list of disable accounts, the date they were disabled and their last successfull login as column A and B.
Analysis:
- For each row in the list of employees, validate the account has been disabled and no logins existed after its last day of work.
Output:
- A merge of both spreadsheets showing all accounts are disabled and no logins occured after the last login.',
                'audit_success_criteria' => 'All accounts have been disabled before the employee left the organisation.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '1',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:36',
                'modified' => '2017-04-11 12:45:39',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '2',
                'name' => 'Regular Vulnerability Scanning - External',
                'objective' => 'Ensure that every month, a vulnerability scanning is made.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '3',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from Nessus  

Analysis: 
- Sort all findings by their criticality, take only those tagged as "High"
- Identify a ticket on the system that addresses the vuln and confirm it has been corrected on the scan that follows.

Output:
- A spreadsheet that includes the vuln id, the ticket used to mitigate the issue, the date it was fixed and the monthly scan that shows the issue i no longer recurring.',
                'audit_success_criteria' => 'All "high" finidings mitigated withing 60 days.',
                'maintenance_metric_description' => 'Every month a Nessus scan must be executed against core systems (use templates on Nessus). Store this report as is later used for audits.',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '12',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 16:56:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '3',
                'name' => 'CCTV',
                'objective' => 'Monitor access and specific areas in offices in order to prevent incidents or document evidence.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '2',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- CCTV recordings from four random offices

Analysis:
- Validate recordings exist for up to 90 days

Output:
- Evidence from the analysis',
                'audit_success_criteria' => 'All videocamara running and 90 days of recording exists',
                'maintenance_metric_description' => 'NA',
                'opex' => '3000',
                'capex' => '50000',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:46:07',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '4',
                'name' => 'Fire and Motion Detectors',
                'objective' => 'Ensure that fire and unauthorized access in branch offices was prevented and controlled',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- Monthly maintanence report from our supplier  

Analysis: 
- Make sure the monthly service has been completed
- Make sure none fire extinguishers is not expired.

Evidence: 
- Screenshot of every fire extinguisher
- Sensors report reviewed',
                'audit_success_criteria' => 'All fire detectors and motion detectors are operating, fire extinguishers are not expired.',
                'maintenance_metric_description' => 'Our fire sensor supplier must perform a monthyl report.',
                'opex' => '3000',
                'capex' => '10000',
                'resource_utilization' => '5',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:48:59',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '5',
                'name' => 'Security Awareness Trainings',
                'objective' => 'Ensure that all employees are aware of our top ten organisational risks.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- HR must provide the list of current employees.
- Awareness reports from eramba

Analysis: 
- Compare HR records against the list of completed trainings, tag those employees that miss a training for the calendar year.

Output:
- Spreadhseet used for analysis',
                'audit_success_criteria' => 'The number of non-compliant employees should not exceed %5.',
                'maintenance_metric_description' => 'Send a reminder to all employees asking them to ensure they comply with our awareness trainings.',
                'opex' => '1000',
                'capex' => '2000',
                'resource_utilization' => '5',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '1',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 14:03:12',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '6',
                'name' => 'End-Point Hardware Inventory',
                'objective' => 'Control that hardware (laptops and computers) is built according to our organisational standards. ',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input
- Random selection of 10 computers at each office
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'maintenance_metric_description' => 'NA',
                'opex' => '12000',
                'capex' => '15000',
                'resource_utilization' => '100',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 14:03:25',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '7',
                'name' => 'Badge Reviews',
                'objective' => 'Verification of active cards in the system.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- List of employees that left the company in the last year
- List of badged assinged (report on the system) to each employee

Analysis:
- Compare both inputs and make sure that there are no active badges assigned to employees who have left the organisation.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'There are no active cards assigned to ex-employees.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '7',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:49:54',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '8',
                'name' => 'Database Administrator Account Reviews',
                'objective' => 'Ensure the database user logins on the following systems: ERP01, SAP, CRM05 do not show connections outside those defined on our policy (applications and system administrators)',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- List of the database engine system accounts for all three systems: CRM, ERP and SAP
- Connection log for these three systems from Splunk
- Current list of system administrators (database)

Analysis:
- Review connection logs to make sure only application users and system administrators have connected to the databases.

Output:
- Spreadsheet validating each database connection.',
                'audit_success_criteria' => 'Only system administrators and applications service accounts have connected to the database.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '4',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-11 12:51:24',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '9',
                'name' => 'NDA and Policy Signing Reviews',
                'objective' => 'Verify that employees and contractors have signed the NDA and security policies.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '4',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- Random selection of %5 from the list of new employees in this calendar year
- List of people that have signed our NDA

Anallysis:
- For each employee in the sample, ensure there is a signed NDA.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All employees NDA\'s and security policies must be signed',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '3',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-12 07:47:44',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '10',
                'name' => 'Datacenter Security',
                'objective' => 'Ensure that data rooms, server rooms and datacenters comply with our policies and standards.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- Datacenter standards
- Review all data-centers, data-rooms and server-rooms

Analysis:
- Visit each site and make sure they comply with standards

Output:
- Spreadsheet with the analysis, for each standard requirement a Yes / No.',
                'audit_success_criteria' => 'All data rooms must comply with %100 of the requirements',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '7',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:37',
                'modified' => '2017-04-10 13:28:37',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '11',
                'name' => 'DMZ Firewall Reviews',
                'objective' => 'Ensure that every rule in the DMZ has followed change management procedures correctly.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- Firewall change logs from Splunk
- All network changes on a spreadsheet report

Analysis:
- For each firewall change on the DMZ, review what changes where approved for that day.

Output:
- Every line on the firewall change matches a change that was approved on that day.',
                'audit_success_criteria' => 'All changes on the firewall recorded on Splunk must have a change ticket associated',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '30',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 14:26:22',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '12',
                'name' => 'Standard Server Build - Linux',
                'objective' => 'Verify that Linux Servers are built according to the server security standard configuration.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input
- Random selection of 10 system on each datacenter created in the last month.
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '13',
                'name' => 'Standard Server Build - Windows',
                'objective' => 'Verify that Windows Servers are built according to the server security standard configuration.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input
- Random selection of 10 system on each datacenter created in the last month.
- Official build guide for end-point systems

Analysis:
- Validate that the system was built according to the guide

Output:
- Spreadsheet with the systems reviewed and result (correct / not correct)',
                'audit_success_criteria' => 'All systems must be aligned with our build standards.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '14',
                'name' => 'SAP Application - Account Reviews',
                'objective' => 'Ensure that each user in SAP belongs to the correct area and that each role has been granted for a ticket',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '3',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '15',
                'name' => 'System Patching',
                'objective' => 'Critical Systems are patched as per our policies and standards',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- Patching status from WSUS for the list of critical systems
- Patching policies

Analysis:
- All critical systems are patched according to our policies and standards

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'All critical patches have been applied in time',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '16',
                'name' => 'Contractor Reviews',
                'objective' => 'Verify that all contractors are still working for the organisation and have a signed NDA',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '3',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '17',
                'name' => 'WPA2 Secured Wifi Networks',
                'objective' => 'Ensure TACACs is used to centralise the authentication of all Wifi points and WPA2 Enterprise is the only authentication method.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- All APs configurations
- Splunk logs with all AP changes

Analysis:
- Ensure TACACs and WPA2 is the only authentication / encryption method.
- Review all Splunk configuration changes for APs, ensure no-change is realted to AP settings.

Output:
- Spreadsheet with the analysis',
                'audit_success_criteria' => 'APs only use TACACs and WPA2',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '1',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '18',
                'name' => 'Alternative Power Sources',
                'objective' => 'Engine electricity generators are available in the event of a general power loss.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- Simulate a power loss by bypassing main electricity inputs, the engine should start inmediately and no power cut should occur on the main datacenter as UPS would hold the short lack of power.

Output:
- Record what was observed on the test',
                'audit_success_criteria' => 'No power cut on any system in the DC',
                'maintenance_metric_description' => 'Review oil and fuel levels on the engine, swith on the engine and leave it on for at least 10 minutes. Document what was found.',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '1',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '19',
                'name' => 'Operating System Administrator Account Reviews',
                'objective' => 'Ensure system engineering is the only team that has access to critical systems: CRM, ERP and SAP',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- List of the system accounts for all three systems: CRM, ERP and SAP
- Connection log for these three systems from Splunk
- Current list of system administrators (unix)

Analysis:
- Review connection logs to make sure only application users and system administrators have connected to the databases.

Output:
- Spreadsheet validating each database connection.',
                'audit_success_criteria' => 'Only system administrators and applications service accounts have connected to the database.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '3',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '20',
                'name' => 'AD Group Reviews',
                'objective' => 'Ensure changes on critical AD groups has followed Change Procedures',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input:
- List of critical AD groups
- Splunk logs with all group changes on these groups
- Change request report for group assignations

Analysis:
- Review each log on Splunk has a ticket asociated

Output:
- Spreadsheet Analysis',
                'audit_success_criteria' => 'All group modifications have a change request ticket asociated.',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '4',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '21',
                'name' => 'Service Accounts Reviews',
                'objective' => 'Review all service accounts defined in the AD. A service account is one used by applications where the password expiration configuration is set to never expire.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
                'audit_metric_description' => 'Input: 
- List of AD accounts where the "password expiration field" is set to "never".  

Analysis: 
- Review that each account has a ticket, expiration and they are still valid.',
                'audit_success_criteria' => 'No service accounts without a valid ticket and expiration',
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '4',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '22',
                'name' => 'High Privilege Service Accounts',
                'objective' => 'Ensure that passwords (accounts without a specific owner that give administrator access to the application or system) are kept safe. ',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '1',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:38',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '23',
                'name' => 'Backups',
                'objective' => 'Ensure Critical Systems (ERP, CRM, SAP) have backups and restores have been tested as per policies and standards.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '1',
                'capex' => '1',
                'resource_utilization' => '1',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:38',
                'modified' => '2017-04-10 13:28:39',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '24',
                'name' => 'VPN Access',
                'objective' => 'VPN acces is available to all our employees',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '0',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:39',
                'modified' => '2017-04-10 13:28:39',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '25',
                'name' => 'AD Policy Password Configuration',
                'objective' => 'Ensure that passwords in SOX systems (So, BD, SAP AD) are configured according to the defined policies.',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '1',
                'documentation_url' => '',
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
                'maintenance_metric_description' => 'NA',
                'opex' => '0',
                'capex' => '0',
                'resource_utilization' => '10',
                'audits_all_done' => '0',
                'audits_last_missing' => '1',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '0',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 13:28:39',
                'modified' => '2017-04-16 00:00:07',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
            [
                'id' => '26',
                'name' => 'Encryption',
                'objective' => '',
                'security_service_type_id' => '4',
                'service_classification_id' => NULL,
                'user_id' => '4',
                'documentation_url' => '',
                'audit_metric_description' => 'Random review of laptops making sure they are encrypted',
                'audit_success_criteria' => 'All laptops encrypted',
                'maintenance_metric_description' => 'NA',
                'opex' => '15000',
                'capex' => '0',
                'resource_utilization' => '15',
                'audits_all_done' => '0',
                'audits_last_missing' => '0',
                'audits_last_passed' => '1',
                'audits_improvements' => '0',
                'audits_status' => '0',
                'maintenances_all_done' => '0',
                'maintenances_last_missing' => '0',
                'maintenances_last_passed' => '0',
                'ongoing_security_incident' => '0',
                'security_incident_open_count' => '1',
                'control_with_issues' => '0',
                'ongoing_corrective_actions' => '0',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:05:02',
                'modified' => '2017-04-11 13:05:02',
                'deleted' => '0',
                'deleted_date' => NULL,
            ],
        ];

        $table = $this->table('security_services');
        $table->insert($data)->save();
    }
}
