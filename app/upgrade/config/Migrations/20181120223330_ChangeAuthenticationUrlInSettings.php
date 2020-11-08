<?php
use Phinx\Migration\AbstractMigration;

class ChangeAuthenticationUrlInSettings extends AbstractMigration
{
    protected $authSlug = 'AUTH';

    public function up()
    {
        $newUrl = '{"controller":"ldapConnectorAuthentications","action":"edit"}';
        $this->query("UPDATE `setting_groups` SET `url`='{$newUrl}' WHERE (`slug`='{$this->authSlug}')");
    }

    public function down()
    {
        $oldUrl = '{"controller":"ldapConnectors","action":"authentication"}';
        $this->query("UPDATE `setting_groups` SET `url`='{$oldUrl}' WHERE (`slug`='{$this->authSlug}')");
    }
}
