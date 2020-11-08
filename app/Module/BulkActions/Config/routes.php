<?php
Router::connect('/bulkActions/apply/*', array('plugin' => 'bulkActions', 'controller' => 'bulkActions', 'action' => 'apply'));
Router::connect('/bulkActions/submit/*', array('plugin' => 'bulkActions', 'controller' => 'bulkActions', 'action' => 'submit'));

Router::connect('/bulkActions/edit/*', array('plugin' => 'bulkActions', 'controller' => 'bulkActions', 'action' => 'edit'));
Router::connect('/bulkActions/delete/*', array('plugin' => 'bulkActions', 'controller' => 'bulkActions', 'action' => 'delete'));