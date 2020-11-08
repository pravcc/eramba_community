<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('LdapSync', 'LdapSync.Lib');
App::uses('LdapSynchronization', 'LdapSync.Model');

class LdapSyncCrudHelper extends CrudHelper
{
    public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar', 'AdvancedFilters.AdvancedFilters'];

    public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

    public function beforeLayoutToolbarRender($event)
    {
        $this->_setToolbar();
    }

    protected function _setToolbar()
    {
        //
        // Add Settings and LDAP synchronization dropdown
        if (empty($this->LayoutToolbar->config('settings'))) {
            $this->LayoutToolbar->addItem(__('Settings'), '#', [
                'slug' => 'settings',
            ]);
        }
        $this->LayoutToolbar->addItem(__('LDAP Account Sync (Beta)'), '#', [
            'slug' => 'ldap_sync',
            'parent' => 'settings'
        ]);
        //
        
        //
        // Add add new button
        $toolbarLdapSyncs = [];
        $toolbarLdapSyncs[] = [];
        $this->LayoutToolbar->addItem(__('Add'), '#', [
            'slug' => 'ldap_sync_add_new',
            'parent' => 'ldap_sync',
            'icon' => 'plus2',
            'data-yjs-request' => 'crud/showForm',
            'data-yjs-target' => 'modal',
            'data-yjs-event-on' => 'click',
            'data-yjs-datasource-url' =>  Router::url([
                'plugin' => 'ldap_sync',
                'controller' => 'ldapSynchronizations',
                'action' => 'add'
            ])
        ]);
        //

        //
        // Add LDAP synchronizations
        $LdapSynchronization = ClassRegistry::init('LdapSynchronization');
        $ldapSynchronizations = $LdapSynchronization->find('all', [
            'order' => [
                'LdapSynchronization.name' => 'ASC'
            ]
        ]);

        if (!empty($ldapSynchronizations)) {
            $this->LayoutToolbar->addItem(__('Saved'), '#', [
                'slug' => 'ldap_sync_saved',
                'parent' => 'ldap_sync',
                'icon' => 'three-bars',
            ]);
        }

        foreach ($ldapSynchronizations as $ls) {
            $ldapSync = $ls['LdapSynchronization'];

            //
            // Add option for ldap synchronization item
            $editBtn = [__('Edit'), '#', [
                'slug' => 'ldap_sync-' . $ldapSync['name'] . '-edit',
                'parent' => 'ldap_sync-' . $ldapSync['name'],
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-event-on' => 'click',
                'data-yjs-datasource-url' =>  Router::url([
                    'plugin' => 'ldap_sync',
                    'controller' => 'ldapSynchronizations',
                    'action' => 'edit',
                    $ldapSync['id']
                ])
            ]];

            $auditTrailsUrl = $this->AdvancedFilters->filterUrl('ldapSynchronizationSystemLogs', ['foreign_key' => $ldapSync['id']], [
                'plugin' => 'ldap_sync'
            ]);
            $auditTrailsBtn = [__('Audit Trails'), $auditTrailsUrl, [
                'slug' => 'ldap_sync-' . $ldapSync['name'] . '-audit-trails',
                'parent' => 'ldap_sync-' . $ldapSync['name'],
                'target' => '_blank'
            ]];
            // $forceSyncBtn = [__('Force Sync'), '#', [
            //     'slug' => 'ldap_sync-' . $ldapSync['name'] . '-force-sync',
            //     'parent' => 'ldap_sync-' . $ldapSync['name'],
            //     'data-yjs-request' => 'crud/showForm',
            //     'data-yjs-target' => 'modal',
            //     'data-yjs-modal-size-width' => 80,
            //     'data-yjs-event-on' => 'click',
            //     'data-yjs-datasource-url' =>  Router::url([
            //         'plugin' => 'ldap_sync',
            //         'controller' => 'ldapSynchronizations',
            //         'action' => 'forceSync',
            //         $ldapSync['id']
            //     ])
            // ]];
            // $simulateSyncBtn = [__('Simulate Sync'), '#', [
            //     'slug' => 'ldap_sync-' . $ldapSync['name'] . '-simulate-sync',
            //     'parent' => 'ldap_sync-' . $ldapSync['name'],
            //     'data-yjs-request' => 'crud/showForm',
            //     'data-yjs-target' => 'modal',
            //     'data-yjs-modal-size-width' => 80,
            //     'data-yjs-event-on' => 'click',
            //     'data-yjs-datasource-url' =>  Router::url([
            //         'plugin' => 'ldap_sync',
            //         'controller' => 'ldapSynchronizations',
            //         'action' => 'simulateSync',
            //         $ldapSync['id']
            //     ])
            // ]];
            $deleteBtn = [__('Delete'), '#', [
                'slug' => 'ldap_sync-' . $ldapSync['name'] . '-delete',
                'parent' => 'ldap_sync-' . $ldapSync['name'],
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-event-on' => 'click',
                'data-yjs-datasource-url' =>  Router::url([
                    'plugin' => 'ldap_sync',
                    'controller' => 'ldapSynchronizations',
                    'action' => 'delete',
                    $ldapSync['id']
                ])
            ]];
            //
            
            $this->LayoutToolbar->addItem($ldapSync['name'], '#', [
                'slug' => 'ldap_sync-' . $ldapSync['name'],
                'parent' => 'ldap_sync_saved'
            ], [
                $editBtn,
                $auditTrailsBtn,
                // $forceSyncBtn,
                // $simulateSyncBtn,
                $deleteBtn
            ]);
        }
        //
        
        if (!empty($ldapSynchronizations)) {
            $this->LayoutToolbar->addItem(__('Simulate Sync'), '#', [
                'slug' => 'ldap_sync_simulate_sync',
                'parent' => 'ldap_sync',
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-modal-size-width' => 80,
                'data-yjs-event-on' => 'click',
                'data-yjs-datasource-url' =>  Router::url([
                    'plugin' => 'ldap_sync',
                    'controller' => 'ldapSynchronizations',
                    'action' => 'simulateSync'
                ])
            ]);

            $this->LayoutToolbar->addItem(__('Force Sync'), '#', [
                'slug' => 'ldap_sync_force_sync',
                'parent' => 'ldap_sync',
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-modal-size-width' => 80,
                'data-yjs-event-on' => 'click',
                'data-yjs-datasource-url' =>  Router::url([
                    'plugin' => 'ldap_sync',
                    'controller' => 'ldapSynchronizations',
                    'action' => 'forceSync'
                ])
            ]);
        }
    }
}
