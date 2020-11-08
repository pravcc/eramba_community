<?php
use Phinx\Migration\AbstractMigration;

class BaseGroupsDescriptionMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $desc = __('This is a system message, this group might not have updated ACLs please make sure you edit and review them');

        // group ids from cake3_deploy.sql seed
        $defaultGroupIds = [
            10, 11, 12, 13
        ];

        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            ClassRegistry::init('Group')->updateAll(['Group.description' => '"' . $desc . '"'], [
                'Group.id' => $defaultGroupIds,
                'OR' => [
                    'Group.description IS NULL',
                    'Group.description' => '',
                ]
            ]);
        }
    }
}
