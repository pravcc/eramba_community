<?php
App::uses('CakePdf', 'CakePdf.Pdf');
App::uses('File', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('Component', 'Controller');

class PdfComponent extends Component {

	protected $CakePdf;

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * Filename wrapper method.
	 */
	protected function _getFileName($name = null) {
		if(!$name){
			$name = md5(time());
		}

		return $name;
	}

	/**
	 * Wrapper method renders a View and also downloads a PDF file.
	 *
	 * @deprecated Use in your controller: `return $this->Pdf->renderPdfItem($name, $viewVars);`
	 */
	public function renderPdf($name, $template = false, $layout = null, $viewVars = null, $download = true) {
		$name = $this->_getFileName($name);

		$pdf = $this->getPdfContent($template, $layout, $viewVars);

		$this->downloadPdf($pdf, $name, $download);
	}

	/**
	 * Render and download a pdf.
	 * 
	 * @param  array  $options Options.
	 */
	public function renderPdfItem($name, $viewVars = [], $options = array()) {
		$options = am(array(
			'template' => null,
			'layout' => 'pdf',
			'download' => true
		), $options);

		extract($options);

		$this->controller->layout = $layout;;

		// for debugging the pdf rendering is disabled an view is rendered in a browser
		if (Configure::read('debug')) {
			$this->controller->set($viewVars);
		}
		// otherwise lets render the pdf and download it
		else {
			$this->controller->autoRender = false;

			$name = Inflector::slug($name, '-');
			$name = $this->_getFileName($name);

			$pdf = $this->getPdfContent($template, $layout, $viewVars);
			$this->downloadPdf($pdf, $name, $download);
		}
	}

	/**
	 * Upgraded method to render multiple views as a single PDF file and keep conventions.
	 * No need to configure controller autoRender and layout.
	 * 
	 * @param  array  $options Same as renderPdf, viewVars must be multiple keyed format for each one of the views.
	 * 
	 * @todo  Migrate the original single rendering here too and make the old function deprecated.
	 */
	public function renderPdfGroup($options = array()) {
		$options = am(array(
			'name' => null,
			'template' => false,
			'layout' => 'pdf',
			'items' => null,
			'download' => true
		), $options);

		extract($options);
		$viewVars = array(
			'items' => $items,
			'singleElementToRender' => $template
		);

		$this->controller->autoRender = false;
		$this->controller->layout = 'pdf';

		$name = $this->_getFileName($name);

		$pdf = $this->getPdfContent('..'.DS.'Elements'.DS.'pdf'.DS.'export_multiple', $layout, $viewVars);

		$this->downloadPdf($pdf, $name, $download);
	}

	/**
	 * Renders a View content via PdfView and returns the HTML output.
	 */
	public function getPdfContent($template = false, $layout = null, $viewVars = null) {
		$originalDebugConfig = Configure::read('debug');
		Configure::write('debug', 0);

		$pdfView = new PdfView($this->controller);
		$pdfView->set($viewVars);

		// checking if view template exists without throwing a 500 error
		// try {
		// 	$pdfView->getViewFileName($template);
		// } catch (Exception $e) {
		// 	$pdfView->subDir = null;
		// }

		// use a default view path in case its not defined
		if ($template === null) {
			$pdfView->subDir = null;
		}

		$render = $pdfView->render($template, $layout);

		Configure::write('debug', $originalDebugConfig);

		return $render;
	}

	private function downloadPdf($pdf, $name, $download) {
		$originalDebugConfig = Configure::read('debug');
		Configure::write('debug', 0);

		$this->controller->response->body($pdf);
   		$this->controller->response->type('pdf');
   		if($download){
   			$this->controller->response->download($name.'.pdf');
   		}

   		Configure::write('debug', $originalDebugConfig);
	}

}
