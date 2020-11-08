<?php
App::uses('ModuleBase', 'Lib');
App::uses('Folder', 'Utility');

class CommentsModule extends ModuleBase
{
	public static function syncFullModelName()
	{
		$Comment = ClassRegistry::init('Comments.Comment');

		$pluginsFolder = new Folder(APP . 'Module');
		$plugins = $pluginsFolder->read();

		$ret = true;
		foreach ($plugins[0] as $plugin) {
			$models = App::objects($plugin . '.Model');

			foreach ($models as $model) {
				$ret &= $Comment->updateAll(['Comment.model' => "'$plugin.$model'"], [
					'Comment.model' => $model
				]);
			}
		}

		return $ret;
	}
}
