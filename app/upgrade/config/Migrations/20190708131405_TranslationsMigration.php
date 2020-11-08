<?php
use Phinx\Migration\AbstractMigration;

class TranslationsMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('translations')
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('folder', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => '1',
                'limit' => 11,
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

        $defaultTranslation = [
            [
                'name' => 'Default (English)',
                'folder' => 'eng',
                'status' => 1,
                'type' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Spanish (Spain)',
                'folder' => 'spa',
                'status' => 1,
                'type' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'French (France)',
                'folder' => 'fra',
                'status' => 1,
                'type' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Portuguese (Portugal)',
                'folder' => 'por',
                'status' => 1,
                'type' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Norwegian Bokmål (Norway)',
                'folder' => 'nob',
                'status' => 1,
                'type' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('translations');
        $table->insert($defaultTranslation);
        $table->saveData();

        // Insert new setting groups
        $settingGroupRows = [
            [
                'slug' => 'TRANSLATION',
                'parent_slug' => 'LOC',
                'name' => 'Languages',
                'icon_code' => null,
                'notes' => null,
                'url' => '{"plugin":"translations","controller":"translations","action":"index"}',
                'modal' => 0,
                'hidden' => 0,
                'order' => 0
            ],
            [
                'slug' => 'DEFAULT_TRANSLATION',
                'parent_slug' => 'LOC',
                'name' => 'Default Language',
                'icon_code' => null,
                'notes' => null,
                'url' => null,
                'modal' => 1,
                'hidden' => 1,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($settingGroupRows);
        $table->saveData();

        App::uses('CakeText', 'Utility');

        // Insert new settings
        $settingRows = [
            'active' => 1,
            'name' => 'Default Language',
            'variable' => 'DEFAULT_TRANSLATION',
            'value' => 1,
            'default_value' => 1,
            'values' => null,
            'type' => 'select',
            'options' => null,
            'hidden' => 1,
            'required' => 0,
            'setting_group_slug' => 'DEFAULT_TRANSLATION',
            'setting_type' => 'constant',
            'order' => 0,
            'modified' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s')
        ];

        $table = $this->table('settings');
        $table->insert($settingRows);
        $table->saveData();

        $this->_syncFilters();
    }

    public function down()
    {
        $this->dropTable('translations');

        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = 'TRANSLATION')");

        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = 'DEFAULT_TRANSLATION')");

        $this->query("DELETE FROM `settings` WHERE (`variable` = 'DEFAULT_TRANSLATION')");
    }

    protected function _syncFilters()
    {
        if (class_exists('App')) {
            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
                
            AppModule::load('Translations');
            
            // autoload the correct plugin model because ClassRegistry mapping may not be updated yet
            ClassRegistry::init('Translations.Translation');
            ClassRegistry::init('Translation');
      
            $AdvancedFilter->syncDefaultIndex(null, [
                'Translation'
            ]);
        }
    }
}

