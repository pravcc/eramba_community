<?php
use Phinx\Migration\AbstractMigration;

class CreateServiceContractsOwners extends AbstractMigration
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
        $table = $this->table('service_contracts_owners');
        $table->addColumn('service_contract_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false
        ])->addForeignKey(
            'service_contract_id',
            'service_contracts',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->addColumn('owner_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false
        ])->addForeignKey(
            'owner_id',
            'users',
            'id',
            [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ]
        );
        $table->create();
    }
}
