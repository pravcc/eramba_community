<?php
use Phinx\Seed\AbstractSeed;

/**
 * AwarenessProgram seed.
 */
class AwarenessProgramSeed extends AbstractSeed
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
                'title' => 'Awareness Training',
                'description' => '',
                'recurrence' => '10',
                'reminder_apart' => '1',
                'reminder_amount' => '3',
                'redirect' => 'https://yahoo.com',
                'ldap_connector_id' => '2',
                'video' => NULL,
                'video_extension' => NULL,
                'video_mime_type' => NULL,
                'video_file_size' => NULL,
                'questionnaire' => NULL,
                'text_file' => 'text-1.txt',
                'text_file_extension' => 'txt',
                'uploads_sort_json' => '[{"type":"text-file"},{"type":"video-file"},{"type":"questionnaire-file"}]',
                'welcome_text' => 'Welcome to this training',
                'welcome_sub_text' => 'Welcome to this training',
                'thank_you_text' => 'Welcome to this training',
                'thank_you_sub_text' => 'Welcome to this training',
                'email_subject' => 'Welcome to this training',
                'email_body' => 'Welcome to this training',
                'email_reminder_custom' => '0',
                'email_reminder_subject' => '',
                'email_reminder_body' => '',
                'status' => 'stopped',
                'awareness_training_count' => '0',
                'active_users' => '338',
                'active_users_percentage' => '99',
                'ignored_users' => '3',
                'ignored_users_percentage' => '1',
                'compliant_users' => '0',
                'compliant_users_percentage' => '0',
                'not_compliant_users' => '338',
                'not_compliant_users_percentage' => '100',
                'stats_update_status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 14:54:25',
                'modified' => '2017-04-11 16:50:30',
            ],
            [
                'id' => '2',
                'title' => 'Encryption Standards',
                'description' => 'We want everyone to know what our encryption standards are.',
                'recurrence' => '150',
                'reminder_apart' => '2',
                'reminder_amount' => '5',
                'redirect' => 'https://yahoo.com',
                'ldap_connector_id' => '2',
                'video' => NULL,
                'video_extension' => NULL,
                'video_mime_type' => NULL,
                'video_file_size' => NULL,
                'questionnaire' => 'default-1.csv',
                'text_file' => 'text-2.txt',
                'text_file_extension' => 'txt',
                'uploads_sort_json' => '[{"type":"text-file"},{"type":"video-file"},{"type":"questionnaire-file"}]',
                'welcome_text' => 'Welcome Header Text',
                'welcome_sub_text' => 'Welcome Header Text',
                'thank_you_text' => 'Welcome Header Text',
                'thank_you_sub_text' => 'Welcome Header Text',
                'email_subject' => 'Action Required: please complete this training! ',
                'email_body' => 'Dear %NAME%,

Please come and complete our mandatory training about: %AWARENESSPROGRAM_DESCRIPTION%

Thank you!',
                'email_reminder_custom' => '0',
                'email_reminder_subject' => '',
                'email_reminder_body' => '',
                'status' => 'stopped',
                'awareness_training_count' => '0',
                'active_users' => '339',
                'active_users_percentage' => '99',
                'ignored_users' => '2',
                'ignored_users_percentage' => '1',
                'compliant_users' => '0',
                'compliant_users_percentage' => '0',
                'not_compliant_users' => '339',
                'not_compliant_users_percentage' => '100',
                'stats_update_status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 13:52:54',
                'modified' => '2017-04-11 13:52:54',
            ],
            [
                'id' => '3',
                'title' => 'Acceptable use of Laptops',
                'description' => 'NA',
                'recurrence' => '150',
                'reminder_apart' => '2',
                'reminder_amount' => '5',
                'redirect' => 'https://yahoo.com',
                'ldap_connector_id' => '2',
                'video' => NULL,
                'video_extension' => NULL,
                'video_mime_type' => NULL,
                'video_file_size' => NULL,
                'questionnaire' => 'default-2.csv',
                'text_file' => 'text-2.txt',
                'text_file_extension' => 'txt',
                'uploads_sort_json' => '[{"type":"video-file"},{"type":"text-file"},{"type":"questionnaire-file"}]',
                'welcome_text' => 'Welcome Header Text',
                'welcome_sub_text' => 'Welcome Header Text',
                'thank_you_text' => 'Welcome Header Text',
                'thank_you_sub_text' => 'Welcome Header Text',
                'email_subject' => 'Actionn required: awareness training',
                'email_body' => 'Hello %NAME%,

',
                'email_reminder_custom' => '0',
                'email_reminder_subject' => '',
                'email_reminder_body' => '',
                'status' => 'stopped',
                'awareness_training_count' => '0',
                'active_users' => '339',
                'active_users_percentage' => '99',
                'ignored_users' => '2',
                'ignored_users_percentage' => '1',
                'compliant_users' => '0',
                'compliant_users_percentage' => '0',
                'not_compliant_users' => '339',
                'not_compliant_users_percentage' => '100',
                'stats_update_status' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 15:54:52',
                'modified' => '2017-04-11 15:54:52',
            ],
        ];

        $table = $this->table('awareness_programs');
        $table->insert($data)->save();
    }
}
