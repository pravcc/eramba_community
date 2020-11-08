<?php
App::uses('AppHelper', 'View/Helper');
App::uses('MacroCollection', 'Macros.Lib');

class MacroHelper extends AppHelper
{
	public $helpers = ['Html'];

	/**
	 * Generates macro editor options.
	 * 
	 * @param MacroCollection|null $MacroCollection
	 * @return array elem options
	 */
	public function editorOptions($MacroCollection)
	{
		$macroList = '';

		if ($MacroCollection !== null) {
			$groupedList = $MacroCollection->getGroupedList();

			foreach ($groupedList as $group) {
				if (empty($group['macros']) || empty($group['slug'])) {
					continue;
				}

				$groupLink = $this->Html->link($group['name'], '#', [
					'data-toggle' => 'dropdown',
					// 'class' => 'dropdown-toggle'
				]);

				$groupMacrosList = $this->Html->tag('ul', $this->_getMacroList($group['macros']), [
					'class' => 'dropdown-menu'
				]);

				$macroList .= $this->Html->tag('li', $groupLink . $groupMacrosList, [
					'class' => 'dropdown-submenu'
				]);
			}

			if (!empty($groupedList['']['macros'])) {
				$macroList .= $this->_getMacroList($groupedList['']['macros']);
			}
		}

		$stmp = rand();
		$class = "summernote-text-report-{$stmp}";
		$macro = "MacrosButton{$stmp}";
		$macroToolbar = ($macroList !== '') ? "['macros', ['macros']]" : "";

		$btnLabel = __('Macros');

		$script = $this->Html->scriptBlock("
			var {$macro} = function(context) {
				var ui = $.summernote.ui;

				var button = ui.buttonGroup([
					ui.button({
						className: 'dropdown-toggle',
						contents: '{$btnLabel} <span class=\"note-icon-caret\"></span>',
						data: {
							toggle: 'dropdown'
						}
					}),
					ui.dropdown({
						className: 'dropdown-menu macro-dropdown',
						contents: '{$macroList}',
						click: function(e) {
							if (typeof $(e.target).data('value') !== 'undefined') {
								context.invoke('editor.insertText', $(e.target).data('value'));
							}
						}
					})
				]);
				
				return button.render();
			}
				
			$('.{$class}').summernote({
				toolbar: [
					['style', ['style', 'bold', 'italic', 'underline', 'clear']],
					['fontsize', ['fontsize']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['link', ['linkDialogShow', 'unlink']],
					['misc', ['undo', 'redo']],
					['styleTags', ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']],
					{$macroToolbar}
				],
				buttons: {
					macros: {$macro}
				},
				height: 250
			});
		");

		return [
			'options' => [
				'type' => 'textarea',
				'class' => [$class, 'summernote-editor-custom'],
				'cols' => 18,
				'rows' => 18,
			],
			'script' => $script
		];
	}

	protected function _getMacroList($macros)
	{
		$list = '';

		foreach ($macros as $macro) {
			$link = $this->Html->link($macro['label'], '#', [
				'data-value' => $macro['macro']
			]);
			$list .= $this->Html->tag('li', $link);
		}

		return $list;
	}
}