<?php
use Phinx\Migration\AbstractMigration;

class SecurityServiceUrlText extends AbstractMigration
{

    public function up()
    {

        $this->table('security_services')
            ->changeColumn('documentation_url', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('security_services')
            ->changeColumn('documentation_url', 'string', [
                'default' => null,
                'length' => 100,
                'null' => false,
            ])
            ->update();
    }
}

