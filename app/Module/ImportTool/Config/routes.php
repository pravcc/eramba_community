<?php
Router::connect('/importTool/upload/*', array('plugin' => 'importTool', 'controller' => 'importTool', 'action' => 'upload'));
Router::connect('/importTool/preview/*', array('plugin' => 'importTool', 'controller' => 'importTool', 'action' => 'preview'));
Router::connect('/importTool/download-template/*', array('plugin' => 'importTool', 'controller' => 'importTool', 'action' => 'downloadTemplate'));