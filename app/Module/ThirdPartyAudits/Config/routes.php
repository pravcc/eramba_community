<?php
Router::connect('/portal/compliance-audits/login', array('plugin' => 'thirdPartyAudits', 'controller' => 'thirdPartyAudits', 'action' => 'login'));
Router::connect('/portal/compliance-audits', array('plugin' => 'thirdPartyAudits', 'controller' => 'thirdPartyAudits', 'action' => 'index'));
Router::connect('/portal/compliance-audits/:action', array('plugin' => 'thirdPartyAudits', 'controller' => 'thirdPartyAudits'));
Router::connect('/portal/compliance-audits/:action/*', array('plugin' => 'thirdPartyAudits', 'controller' => 'thirdPartyAudits'));