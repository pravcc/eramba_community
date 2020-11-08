<?php
use Phinx\Migration\AbstractMigration;

class ChangeForeignKeyInSecurityServiceAudits extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->execute("UPDATE `security_service_audits` SET `user_id`=1 WHERE (`user_id` IS NULL OR `user_id`=0)");

        $table = $this->table('security_service_audits');
        $table->dropForeignKey('user_id');
        $table->changeColumn('user_id', 'integer', [
            'default' => 1,
            'limit' => 11,
            'null' => false
        ]);
        $table->addForeignKey(
            'user_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->update();
    }

    public function down()
    {
        $table = $this->table('security_service_audits');
        $table->dropForeignKey('user_id');
        $table->changeColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true
        ]);
        $table->addForeignKey(
            'user_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'NO_ACTION'
            ]
        );
        $table->update();
    }
}
