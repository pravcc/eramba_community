<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('ObjectStatusView', 'ObjectStatus.Controller/Crud/View');
App::uses('ObjectStatusCollectionSeed', 'ObjectStatus.Lib');

/**
 * Search Listener
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class ObjectStatusListener extends CrudListener {

/**
 * Default configuration
 *
 * @var array
 */
    protected $_settings = [];

/**
 * Returns a list of all events that will fire in the controller during its lifecycle.
 * You can override this function to add you own listener callbacks
 *
 * We attach at priority 50 so normal bound events can run before us
 *
 * @return array
 */
    public function implementedEvents() {
        return [
            'Crud.beforeHandle' => ['callable' => 'beforeHandle', 'priority' => 50],
            'Crud.beforeFilter' => ['callable' => 'beforeFilter', 'priority' => 50],
            'Crud.afterSave' => ['callable' => 'afterSave', 'priority' => 50],
            'Crud.afterDelete' => ['callable' => 'afterDelete', 'priority' => 50],
            'Crud.afterImport' => 'afterImport',
            'AdvancedFilter.afterFind' => ['callable' => 'afterFilterFind', 'priority' => 55],
            'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 50],
        ];
    }

    public function beforeHandle(CakeEvent $event) {
        $model = $this->_model();
        $this->_ensureBehavior($model);
    }

    protected function _ensureBehavior(Model $model) {
        if ($model->Behaviors->loaded('ObjectStatus')) {
            return;
        }

        $model->Behaviors->load('ObjectStatus.ObjectStatus');
        $model->Behaviors->ObjectStatus->setup($model);
    }

    /**
     * Before filter action that changes things to apply trash.
     */
    public function beforeFilter(CakeEvent $e)
    {
        // and attach an event to the advanced filter object to additionally make changes to the final query
        $AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
        $this->attachListener($AdvancedFiltersObject);
    }

    public function attachListener(AdvancedFiltersObject $Filter)
    {
        $Filter->getEventManager()->attach($this);
    }
    
    public function afterSave(CakeEvent $event) {
        if (!empty($event->subject->success)) {
            $model = $this->_model();
            $model->Behaviors->ObjectStatus->triggerObjectStatus($model, null, $event->subject->id);
        }
    }

    public function afterDelete(CakeEvent $event) {
        if (!empty($event->subject->success)) {
            $model = $this->_model();
            $model->Behaviors->ObjectStatus->deleteObjectStatus($model, null, $event->subject->id);
        }
    }

    /**
     * Trigger object status recalculation after import.
     */
    public function afterImport(CakeEvent $event) {
        // debug('after import');
        // $model = $this->_model();
        // $model->triggerObjectStatus();
    }

    public function afterFilterFind(CakeEvent $event)
    {
        self::setObjectStatusData($event->result);
    }

    public static function setObjectStatusData($data)
    {
        $Seed = new ObjectStatusCollectionSeed();
        $Seed->seed($data);
    }

    /**
     * Before render callback that sets all required data into the view.
     * 
     * @param  CakeEvent $e
     * @return void
     */
    public function beforeRender(CakeEvent $e)
    {
        $this->_controller()->set('ObjectStatus', new ObjectStatusView($e->subject));
    }
}
