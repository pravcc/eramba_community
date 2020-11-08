<?php
use Phinx\Migration\AbstractMigration;

class BusinessContinuityPlanAuditVisualisationFix extends AbstractMigration
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
        $result = $this->fetchAll("SELECT * FROM `visualisation_settings` WHERE `model` = 'BusinessContinuityPlanAudit' ORDER BY `id` DESC");

        if (!empty($result) && count($result) > 1 && !empty($result[0]['id'])) {
            $this->query("DELETE FROM `visualisation_settings` WHERE `id` = {$result[0]['id']}");
        }
    }
}
