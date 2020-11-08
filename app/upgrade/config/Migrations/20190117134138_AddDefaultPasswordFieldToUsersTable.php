<?php
use Phinx\Migration\AbstractMigration;

class AddDefaultPasswordFieldToUsersTable extends AbstractMigration
{

    public function up()
    {

        $this->table('users')
            ->addColumn('default_password', 'integer', [
                'after' => 'api_allow',
                'default' => 1,
                'length' => 1,
                'null' => false,
            ])
            ->update();

        // Set 'default_password' field for existing users to 0
        $this->query("UPDATE `users` SET `default_password`=0 WHERE 1");
    }

    public function down()
    {

        $this->table('users')
            ->removeColumn('default_password')
            ->update();
    }
}

