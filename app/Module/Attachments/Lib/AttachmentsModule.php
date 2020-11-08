<?php
App::uses('ModuleBase', 'Lib');
App::uses('Folder', 'Utility');

class AttachmentsModule extends ModuleBase
{
	public static function syncFullModelName()
	{
		$Attachment = ClassRegistry::init('Attachments.Attachment');

		$pluginsFolder = new Folder(APP . 'Module');
		$plugins = $pluginsFolder->read();

		$ret = true;
		foreach ($plugins[0] as $plugin) {
			$models = App::objects($plugin . '.Model');

			foreach ($models as $model) {
				$ret &= $Attachment->updateAll(['Attachment.model' => "'$plugin.$model'"], [
					'Attachment.model' => $model
				]);
			}
		}

		return $ret;
	}
}
