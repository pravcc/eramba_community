<?php
App::uses('MigrationShell', 'Migrations.Console/Command');

class EMigrationShell extends MigrationShell {
	protected function _generateTemplate($template, $vars) {
		extract($vars);
		ob_start();
		ob_implicit_flush(0);
		include dirname(__FILE__) . DS . 'Templates' . DS . $template . '.ctp';
		$content = ob_get_clean();

		return $content;
	}
}