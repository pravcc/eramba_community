<?php
use Phinx\Migration\AbstractMigration;

class VisualisationConstraints extends AbstractMigration
{

    public function up()
    {
        $this->table('visualisation_settings_users')
            ->dropForeignKey([], 'visualisation_settings_users_ibfk_1')
            ->update();
        $this->table('visualisation_share')
            ->dropForeignKey([], 'visualisation_share_ibfk_1')
            ->update();

        $this->table('visualisation_settings_users')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('visualisation_share')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('visualisation_settings_users')
            ->dropForeignKey(
                'aros_acos_id'
            );

        $this->table('visualisation_share')
            ->dropForeignKey(
                'aros_acos_id'
            );

        $this->table('visualisation_settings_users')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();

        $this->table('visualisation_share')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();
    }
}

