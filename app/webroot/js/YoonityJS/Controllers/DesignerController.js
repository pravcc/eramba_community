"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		DesignerController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.AppController,
				
				constructor: function(params)
				{
					// Save current object reference for inner scopes
					_this = this;
					
					// Call parent constructor
					_this._parent.constructor(params);

					var properties = {
						Registry: null
					};

					YoonityJS.Class.InitProperties.call(this, properties, params);
				},

				$addReportBlock: function(params)
				{
					var type = params.type ? params.type : null;

					if (type) {
						this.loadModel();
						scopes.addActionScope(function(_this)
						{
							_this._parent.$submitForm({});

							_this.addCallback('shutdown', function(content)
							{
								var $content = $(_this.getElemFromContentByClass(content, 'report-block'));
								$content.prop('data-report-block-type', type);

								_this.initBlock($content, this.Registry.getObject('response').getTarget());

								_this.$triggerRequest({
									0: '#report-block-edit-btn-' + $content.data('report-block-id')
								});
							});
						}, this);
					}
				},

				$addTemplateBlock: function(params)
				{
					var
						templateId = params.templateId ? params.templateId : null,
						type = params.type ? params.type : null,
						size = params.size ? params.size : null;

					if (templateId && type) {
						this.loadModel();
						scopes.addActionScope(function(_this)
						{
							_this.getModel().set('data[ReportBlock][report_template_id]', templateId);
							_this.getModel().set('data[ReportBlock][type]', type);

							_this._parent.$submitForm({});

							_this.addCallback('shutdown', function(content)
							{
								var $content = $(_this.getElemFromContentByClass(content, 'report-block'));
								_this.initBlock($content, this.Registry.getObject('response').getTarget());
							});
						}, this);
					}
				},

				$deleteTemplateBlock: function(params)
				{
					var blockId = params.blockId ? params.blockId : null;

					this._parent.$submitForm({});

					this.addCallback('shutdown', function(content)
					{
						$('#report-block-' + blockId).off().detach();
					});
				},

				$saveReport: function(params)
				{
					var
						reportId = params.reportId ? params.reportId : null,
						templateId = params.templateId ? params.templateId : null,
						$designerContent = $('#dsgn-content');

					if (reportId !== null && templateId !== null && $designerContent.length) {
						this.loadModel();
						scopes.addActionScope(function(_this)
						{
							_this.getModel().set('data[Report][id]', reportId);
							_this.getModel().set('data[ReportTemplate][id]', templateId);

							var order = 1;
							$designerContent.children('.block').each(function()
							{
								var id = $(this).children('.report-block').data('report-block-id');
								if (id) {
									_this.getModel().set('data[ReportTemplate][ReportBlock][' + id + '][id]', id);
									_this.getModel().set('data[ReportTemplate][ReportBlock][' + id + '][order]', order);

									++order;
								}
							});

							_this._parent.$submitForm({});
						}, this);
					}
				},


				$saveTemplate: function(params)
				{
					var
						templateId = params.templateId ? params.templateId : null,
						$designerContent = $('#dsgn-content');

					if (templateId !== null && $designerContent.length) {
						this.loadModel();
						scopes.addActionScope(function(_this)
						{
							_this.getModel().set('data[ReportTemplate][id]', templateId);

							var order = 1;
							$designerContent.children('.block').each(function()
							{
								var id = $(this).children('.report-block').data('report-block-id');
								if (id) {
									_this.getModel().set('data[ReportBlock][' + id + '][id]', id);
									_this.getModel().set('data[ReportBlock][' + id + '][order]', order);

									++order;
								}
							});

							_this._parent.$submitForm({});
						}, this);
					}
				},

				$loadDesignerContent: function(params)
				{
					this._parent.$load({});

					scopes.addActionScope(function(_this)
					{
						_this.addCallback('shutdown', function(content)
						{
							$(content).each(function()
							{
								var blockId = $(this).data('report-block-id');
								if (blockId) {
									_this.initBlock(this);
								}
							});
						});
					}, this);
				},

				$initDesignerContent: function(params)
				{
					var
						content = this.Registry.getObject('request').getObject(),
						_this = this;
					$(content).children('.report-block').each(function()
					{
						var blockId = $(this).data('report-block-id');
						if (blockId) {
							_this.initBlock(this);
						}
					});
				},

				$reloadBlock: function(params)
				{
					this._parent.$load({});

					scopes.addActionScope(function(_this)
					{
						_this.addCallback('shutdown', function(content)
						{
							var $content = $(_this.getElemFromContentByClass(content, 'report-block'));
							_this.initBlock($content, this.Registry.getObject('request').getObject());
						});
					}, this);
				},

				/**
				 * Initialize block element - add its wrap and put 
				 * the element into it and then add toolbar with control buttons
				 * 
				 * @param  JQuery element                  blockElem         Block element which should be initialized
				 * @param  mixed (JQuery elem or selector) oldBlockWrapElem  Old Wrap element which will be replaced 
				 * by the new one (leave this undefined if you want to replace block element with the new wrapped element with block element inside)
				 * @return void
				 */
				initBlock: function(blockElem, oldBlockWrapElem)
				{
					var
						blockId = $(blockElem).data('report-block-id'),
						blockSettingId = $(blockElem).data('report-block-setting-id'),
						blockSize = $(blockElem).data('report-block-size'),
						blockType = $(blockElem).data('report-block-type');

					var $blockWrap = $(document.createElement('div')).addClass('block report-block-width-' + blockSize).attr('id', 'report-block-' + blockId);

					if ($blockWrap.length) {
						var viewBlockUrl = 'reports/reportBlock' + YoonityJS.Globals.ucFirst(blockType) + 'Settings/view/' + blockSettingId;
						
						$blockWrap.attr('data-yjs-request', 'designer/reloadBlock');
						$blockWrap.attr('data-yjs-datasource-url', viewBlockUrl);
						$blockWrap.attr('data-yjs-target', 'self');
					} else {
						return;
					}

					//
					// Add toolbar buttons
					var
						$overlayElem = $(document.createElement('div')).addClass('overlay'),
						$backgroundElem = $(document.createElement('div')).addClass('background'),
						$toolbarElem = $(document.createElement('div')).addClass('toolbar');
					if (!$blockWrap.children('.overlay').length) {
						//
						// Add buttons to the toolbar
						var settingsUrl = 'reports/reportBlock' + YoonityJS.Globals.ucFirst(blockType) + 'Settings/edit/' + blockSettingId;
						$toolbarElem.append($(document.createElement('div')).addClass('t_btn').append('<i class="icon-gear"></i>')
							.attr('id', 'report-block-edit-btn-' + blockId)
							.attr('data-yjs-request', 'crud/showForm')
							.attr('data-yjs-event-on', 'click')
							.attr('data-yjs-datasource-url', settingsUrl)
							.attr('data-yjs-target', 'modal'));

						$toolbarElem.append($(document.createElement('div')).addClass('t_btn').append('<i class="icon-trash"></i>')
							.attr('data-yjs-request', 'crud/showForm')
							.attr('data-yjs-target', 'modal')
							.attr('data-yjs-datasource-url', 'reports/reportBlocks/delete/' + blockId)
							.attr('data-yjs-event-on', 'click'));
						//

						// Add background
						$overlayElem.append($backgroundElem);

						// Add toolbar
						$overlayElem.append($toolbarElem);

						$overlayElem.hide();

						$blockWrap.append($overlayElem);

						// Initialize YoonityJS template
						new YoonityJS.InitTemplate({
							template: $overlayElem
						});
					}
					//

					$blockWrap.on('mouseenter', function()
					{
						$overlayElem.show();
					});
					$blockWrap.on('mouseleave', function()
					{
						$overlayElem.hide();
					});

					if (oldBlockWrapElem) {
						$(oldBlockWrapElem).replaceWith($blockWrap.append($(blockElem)));
					} else {
						$(blockElem).replaceWith($blockWrap.append($(blockElem).clone(true)));
					}

					new YoonityJS.InitElement({
						element: $blockWrap
					});
				},

				getElemFromContentByClass: function(content, cssClass)
				{
					if (!Array.isArray(content)) {
						return content;
					} else {
						for (var i = 0; i < content.length; ++i) {
							if ($(content[i]).hasClass(cssClass)) {
								return content[i];
							}
						}
					}

					return false;
				}
			};
		}
	}
});
