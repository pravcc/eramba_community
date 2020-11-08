<?php
Router::connect('/backupRestore/index/*', array('plugin' => 'backupRestore', 'controller' => 'backupRestore', 'action' => 'index'));
Router::connect('/backupRestore/getBackup/*', array('plugin' => 'backupRestore', 'controller' => 'backupRestore', 'action' => 'getBackup'));
