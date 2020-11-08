<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');

/**
 * Audits CRON listener.
 */
class AuditsCronListener extends CronCrudListener
{
	public function beforeHandle(CakeEvent $event)
	{
		$this->SecurityService = ClassRegistry::init('SecurityService');
		$this->BusinessContinuityPlan = ClassRegistry::init('BusinessContinuityPlan');
		$this->Goal = ClassRegistry::init('Goal');
	}

	public function yearly(CakeEvent $event)
	{
		$ret = true;

        // Audits and Maintenances dates for controls
        $items = $this->SecurityService->find('all', array(
            'contain' => array(
                'SecurityServiceAuditDate',
                'SecurityServiceMaintenanceDate'
            )
        ));

        foreach ($items as $item) {
            $this->SecurityService->set($item);

            $dates = $item['SecurityServiceAuditDate'];
            $ret &= $this->SecurityService->saveAuditsJoins($dates, $item['SecurityService']['id'], true);
            
            $this->SecurityService->set($item);

            $dates = $item['SecurityServiceMaintenanceDate'];
            $ret &= $this->SecurityService->saveMaintenancesJoins($dates, $item['SecurityService']['id'], true);
        }

        $this->SecurityService->clear();

        // BCPlans
        $items = $this->BusinessContinuityPlan->find('all', array(
            'contain' => array(
                'BusinessContinuityPlanAuditDate'
            )
        ));

        foreach ($items as $item) {
            $this->BusinessContinuityPlan->set($item);

            $dates = $item['BusinessContinuityPlanAuditDate'];
            $ret &= $this->BusinessContinuityPlan->saveAuditsJoins($dates, $item['BusinessContinuityPlan']['id'], true);
        }

        $this->BusinessContinuityPlan->clear();

        // Goals
        $items = $this->Goal->find('all', array(
            'contain' => array(
                'GoalAuditDate'
            )
        ));

        foreach ($items as $item) {
            $this->Goal->set($item);

            $dates = $item['GoalAuditDate'];
            $ret &= $this->Goal->saveAuditsJoins($dates, $item['Goal']['id'], true);
        }

        $this->Goal->clear();
		
		if (!$ret) {
			throw new CronException(__('Yearly audits and maintenances update failed.'));
		}
	}
}
