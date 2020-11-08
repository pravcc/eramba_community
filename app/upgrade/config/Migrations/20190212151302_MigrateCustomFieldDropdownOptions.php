<?php
use Phinx\Migration\AbstractMigration;

class MigrateCustomFieldDropdownOptions extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('AppModule', 'Lib');

            if (AppModule::loaded('CustomFields')) {
                App::uses('CustomField', 'CustomFields.Model');

                $results = $this->fetchAll("SELECT `CustomFieldValues`.`id`, `CustomFieldOptions`.`value` 
                    FROM `custom_fields` AS `CustomFields`, `custom_field_options` AS `CustomFieldOptions`, `custom_field_values` AS `CustomFieldValues` 
                    WHERE `CustomFieldValues`.`custom_field_id`=`CustomFields`.`id` AND `CustomFields`.`type`=" . CustomField::TYPE_DROPDOWN . " AND `CustomFieldValues`.`custom_field_id`=`CustomFieldOptions`.`custom_field_id` AND `CustomFieldValues`.`value`=`CustomFieldOptions`.`id`");

                $conn = $this->getAdapter()->getConnection();
                foreach ($results as $result) {
                    $id = $result['id'];
                    $value = $conn->quote($result['value']);
                    $this->query("UPDATE `custom_field_values` SET `value`={$value} WHERE `id`={$id}");
                }
            }
        }
    }

    public function down()
    {
    }
}
