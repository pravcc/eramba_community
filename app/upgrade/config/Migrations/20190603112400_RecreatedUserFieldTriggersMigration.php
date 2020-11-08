<?php
use Phinx\Migration\AbstractMigration;

class RecreatedUserFieldTriggersMigration extends AbstractMigration
{
    public function up()
    {
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_delete;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_delete;");

        $this->query("CREATE TRIGGER `user_fields_users_after_insert` AFTER INSERT ON `user_fields_users` FOR EACH ROW INSERT INTO `user_fields_objects` 
            SET
                model = NEW.model,
                foreign_key = NEW.foreign_key,
                field = NEW.field,
                object_id = NEW.user_id,
                object_key = concat('User-', NEW.user_id),
                object_model = 'User',
                created = NEW.created,
                modified = NEW.modified;");

        $this->query("CREATE TRIGGER `user_fields_users_after_delete` AFTER DELETE ON `user_fields_users` FOR EACH ROW DELETE FROM `user_fields_objects` 
            WHERE 
                model = OLD.model
                AND foreign_key = OLD.foreign_key
                AND field = OLD.field
                AND object_id = OLD.user_id
                AND object_model LIKE 'User';");

        $this->query("CREATE TRIGGER `user_fields_groups_after_insert` AFTER INSERT ON `user_fields_groups` FOR EACH ROW INSERT INTO `user_fields_objects` 
            SET
                model = NEW.model,
                foreign_key = NEW.foreign_key,
                field = NEW.field,
                object_id = NEW.group_id,
                object_key = concat('Group-', NEW.group_id),
                object_model = 'Group',
                created = NEW.created,
                modified = NEW.modified;");

        $this->query("CREATE TRIGGER `user_fields_groups_after_delete` AFTER DELETE ON `user_fields_groups` FOR EACH ROW DELETE FROM `user_fields_objects` 
            WHERE 
                model = OLD.model
                AND foreign_key = OLD.foreign_key
                AND field = OLD.field
                AND object_id = OLD.group_id
                AND object_model LIKE 'Group';");
        
        if (class_exists('App')) {
            App::uses('UserFieldsShell', 'UserFields.Console/Command');
            $UserFieldsShell = new UserFieldsShell();
            $UserFieldsShell->startup();

            if (!$UserFieldsShell->sync_existing_objects()) {
                throw new Exception("An error occurred during sync existing user fields objects", 1);
            }
        }
    }

    public function down()
    {
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_delete;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_delete;");
    }
}

