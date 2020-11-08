<?php
use Phinx\Migration\AbstractMigration;

class UserFieldsObjectsMigration extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {
        $this->table('user_fields_objects')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('field', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('object_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('object_key', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('object_model', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

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
    }

    public function down()
    {
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_users_after_delete;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_insert;");
        $this->query("DROP TRIGGER IF EXISTS user_fields_groups_after_delete;");

        $this->dropTable('user_fields_objects');
    }
}

