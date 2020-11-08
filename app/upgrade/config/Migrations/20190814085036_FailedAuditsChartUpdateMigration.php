<?php
use Phinx\Migration\AbstractMigration;

class FailedAuditsChartUpdateMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App') && AppModule::loaded('Reports')) {
            App::uses('ReportsModule', 'Reports.Lib');

            $ReportsModule = new ReportsModule();

            $ReportsModule->updateReports([
                'CompliancePackageInstanceItemDefaultReport',
                'SecurityServiceSectionDefaultReport',
            ]);
        }
    }

    public function down()
    {
    }
}
