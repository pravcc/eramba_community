<?php
use Phinx\Migration\AbstractMigration;

class AwarenessProgramTextFrameSize extends AbstractMigration
{

    public function up()
    {

        $this->table('awareness_programs')
            ->addColumn('text_file_frame_size', 'integer', [
                'after' => 'text_file_extension',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('awareness_programs')
            ->removeColumn('text_file_frame_size')
            ->update();
    }
}

