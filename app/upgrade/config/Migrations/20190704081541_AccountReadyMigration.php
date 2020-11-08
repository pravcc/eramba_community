<?php
use Phinx\Migration\AbstractMigration;

class AccountReadyMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('users')
            ->addColumn('account_ready', 'integer', [
                'after' => 'default_password',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();

        if (class_exists('App')) {
            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
            $User = ClassRegistry::init('User');

            $users = $User->find('list', [
                'fields' => [
                    'id', 'id'
                ],
                'recursive' => -1
            ]);
            
            $ret = true;

            // check users that are synced already and update their account_ready value
            foreach ($users as $userId) {
                $countFilters = $AdvancedFilter->find('count', [
                    'conditions' => [
                        'AdvancedFilter.user_id' => $userId,
                        'AdvancedFilter.model' => 'Legal'
                    ],
                    'recursive' => -1
                ]);

                if ($countFilters) {
                    $ret &= $User->updateAll([
                        'User.account_ready' => '1'
                    ], [
                        'User.id' => $userId
                    ]);
                }
            }

            if (!$ret) {
                throw new Exception('Users failed to update for the Account Ready feature', 1);
                return false;
            }
        }
    }

    public function down()
    {

        $this->table('users')
            ->removeColumn('account_ready')
            ->update();
    }
}

