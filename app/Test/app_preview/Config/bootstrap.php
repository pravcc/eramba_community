<?php
/**
 * @package       AppPreview.Config
 */
App::uses('AppModule', 'Lib');

$path = Configure::read('Eramba.Preview.PATH');

App::build(['Model' => [$path . 'Model' . DS]], App::PREPEND);
App::build(['Controller' => [$path . 'Controller' . DS]], App::PREPEND);
App::build(['View' => [$path . 'View' . DS]], App::PREPEND);
App::build(['View/Helper' => [$path . 'View' . DS . 'Helper' . DS]], App::PREPEND);

AppModule::instance('Workflows')->addToWhitelist('SectionItem');