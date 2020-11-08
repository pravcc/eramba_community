<?php
App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('Attachment', 'Attachments.Model');

class AttachmentsMgtComponent extends Component
{

	public function startup(Controller $controller)
	{
		$this->controller = $controller;
	}

	/**
	 * Prepares an attachment and forces a download to the client.
	 * 
	 * @param  int $id 			Attachment ID.
	 * @return CakeResponse     Response.
	 */
	public function download($id)
	{
		$this->_prepareFile($id);

		// Return response object to prevent controller from trying to render a view
		return $this->controller->response;
	}

	/**
	 * Configure a CakeResponse $response for a file.
	 * 
	 * @param  int $id Attachment ID.
	 * @return void
	 */
	protected function _prepareFile($id)
	{
		$Attachment = ClassRegistry::init('Attachments.Attachment');

		$file = $Attachment->getFile($id);

		$fileName = str_replace('/files/uploads/', '', $file['Attachment']['filename']);

		$this->controller->response->file(Attachment::UPLOADS_PATH . $fileName, [
			'download' => true,
			'name' => basename($file['Attachment']['name'])
		]);
	}

	/**
	 * Check if attachment is allowed to download.
	 * 
	 * @param int $id Attachment id.
	 * @return boolean
	 */
	public function isAllowedToDownload($id)
	{
		$attachment = ClassRegistry::init('Attachments.Attachment')->getFile($id);

		if (empty($attachment) || !$this->_checkModelName($attachment['Attachment']['model'])) {
			return false;
		}

		return true;
	}

	/**
	 * Check if model name is in match witch controller model name.
	 * 
	 * @param string $modelName
	 * @return boolean
	 */
	protected function _checkModelName($modelName)
	{
		$attachmentModel = pluginSplit($modelName)[1];
		$controllerModel = pluginSplit($this->_getControllerModelName())[1];

		return $attachmentModel == $controllerModel;
	}

	/**
	 * Get attachment model name assigned to this controller.
	 * 
	 * @return string Model name.
	 */
	protected function _getControllerModelName()
	{
		$model = $this->controller->modelClass;

		if (!empty($this->settings['model'])) {
			$model = $this->settings['model'];
		}

		return $model;
	}
}
