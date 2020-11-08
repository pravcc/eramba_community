"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		CrudController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.AppController,

				allowedSelect2TagNames: ['INPUT', 'SELECT'],
				
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

				$showForm: function(params)
				{
					//
					// Add callbacks for forms
					_this.addCallback('beforeRender', function() {
						_this.formBeforeInitActions();
					});
					_this.addCallback('shutdown', function(content) {
						_this.formInitialization(content);
					});
					//
					
					_this._parent.$showForm(params);
				},

				$submitForm: function(params)
				{
					//
					// Add callbacks for forms
					_this.addCallback('beforeRender', function() {
						_this.formBeforeInitActions();
					});
					_this.addCallback('shutdown', function(content) {
						_this.formInitialization(content);
					});
					//
					
					_this._parent.$submitForm(params);
				},

				$load: function(params)
				{
					//
					// Add callbacks for forms
					_this.addCallback('beforeRender', function() {
						_this.formBeforeInitActions();
					});
					_this.addCallback('shutdown', function(content) {
						_this.formInitialization(content);
					});
					//
					
					_this._parent.$load(params);
				},

				$submitDataAndShowForm: function(params)
				{
					// Prepare form data for server side processing (BackEnd)
					var formData = this.handleForm();

					//
					// Load model and set form data
					this.loadModel();
					scopes.addActionScope(function(_this) {
						for (var f in formData) {
							if (formData.hasOwnProperty(f)) {
								_this.getModel().set(f, formData[f]);
							}
						}
					}, this);
					//

					//
					// Call parent method
					scopes.addActionScope(function(_this) {
						_this.$showForm({});
					}, this);
					//
				},

				$loadAndResize: function(params)
				{
					this._parent.$load({});
					this.addCallback('shutdown', function(content) {
						$(window).resize();
					});
				},

				$smallTooltip: function(params)
				{
					//
					// Load and fill model from datasource
					this.loadModel();
					scopes.addActionScope(function(_this)
					{
						_this.getModel().process({
							method: 'GET'
						});
					}, this);
					//
					
					scopes.addActionScope(function(_this)
					{
						var
							id = params.id ? params.id : null,
							header = _this.getModel().get('tooltipHeader'),
							paragraphs = _this.getModel().get('tooltipParagraphs'),
							buttons = _this.getModel().get('tooltipButtons');

						var
							title = '<div>' + header + '</div>',
							content = '<div class="content-body">';
						for (var p in paragraphs) {
							if (paragraphs.hasOwnProperty(p)) {
								content += '<h6 class="text-semibold">' + paragraphs[p]['heading'] + '</h6>' +
												'<p>' + paragraphs[p]['text'] + '</p>';
							}
						}
						content += '</div>';

						content += '<div class="text-right mt-20">';
						var showMoreBtn = null;
						for (var btn in buttons) {
							if (buttons.hasOwnProperty(btn)) {
								var cssClass = '';
								if (btn === 'gotIt') {
									cssClass = 'btn btn-link btn-xs gotit-btn';
								} else if (btn === 'showMore') {
									cssClass = 'btn bg-grey-800 btn-xs show-more-btn';

									showMoreBtn = buttons[btn];
								}

								content += '<button class="' + cssClass + '">' + buttons[btn]['name'] + '</button>';
							}
						}
						content += '</div>';

						$(id).on('mouseover', function (e) {
					        // Add animation class to panel element
					        $(this).velocity("callout.swing", { stagger: 500, duration: 500 });
					        e.preventDefault();
					    });

				    	$(id).popover({
					    	html: true,
							title: title,
							//content: 'And here\'s some amazing content. It\'s very engaging. Right?',
							content: content,
							template: '<div class="popover popover-custom">' +
											'<div class="arrow"></div>' +
											'<h3 class="popover-title bg-primary"></h3>' + 
											'<div class="popover-content"></div>' + 
											'</div>',
							trigger: 'click',
							placement: 'left'
						}).on('inserted.bs.popover', function()
						{
							var
								popover = $(this).data('bs.popover'),
								$element = popover.$element,
								$tip = popover.$tip;
							$tip.find('.gotit-btn').on('click', function()
							{
								$element.popover('hide');
							});

							$tip.find('.show-more-btn').on('click', function()
							{
								$element.popover('hide');

								$(this).data('yjs-request', 'crud/largeTooltip');
								$(this).data('yjs-target', 'modal');
								$(this).data('yjs-datasource-url', showMoreBtn['path']);
								var YoonityJSObject = new YoonityJS.Init({
									object: this
								});
							});
						}).on('hidden.bs.popover', function()
						{
							$(this).popover('disable');
							$(this).off('mouseover');

							$(this).data('yjs-request', 'crud/showForm');
							$(this).data('yjs-target', 'modal');
							$(this).data('yjs-datasource-url', 'legals/add');
							$(this).data('yjs-event-on', 'click');
							var YoonityJSObject = new YoonityJS.InitElement({
								element: this
							});
						}).popover('show');
					}, this);
				},

				$largeTooltip: function(params)
				{
					this.loadModel();
					scopes.addActionScope(function(_this)
					{
						_this.getModel().process({
							method: 'GET'
						});
					}, this);

					//
					// Set layout for view
					scopes.addActionScope(function(_this)
					{
						_this.setLayout(_this.getModel().get('content'));
					}, this);
					//
				},

				$initExternals: function(params)
				{
					if (params.elem) {
						//
						// Add callbacks for forms
						_this.addCallback('beforeRender', function() {
							_this.formBeforeInitActions();
						});
						_this.addCallback('shutdown', function(content) {
							_this.formInitialization(params.elem);
						});
						//
					}
				},

				$addInputField: function(params)
				{
					var
						fieldName = params.fieldName ? params.fieldName : null,
						fieldsClass = params.fieldsClass ? params.fieldsClass : null,
						count = $('.' + fieldsClass).length ? $('.' + fieldsClass).length : 0;

					this.loadModel();
					scopes.addActionScope(function(_this)
					{
						_this.getModel().set('fieldName', fieldName);
						_this.getModel().set('fieldsClass', fieldsClass);
						_this.getModel().set('fieldsCount', count);
						_this.$showForm(params);
					}, this);
				},

				$removeInputField: function(params)
				{
					var fieldId = params.fieldId ? params.fieldId : null;

					$('#' + fieldId).remove();
				},

				formBeforeInitActions: function()
				{
					scopes.addActionScope(function(_this)
					{
						var
							target = _this.Registry.getObject('response').getTarget(),
							targetPlacement = _this.Registry.getObject('response').getTargetPlacement(),
							content = null;
						if (target === 'modal') {
							if (_this.Registry.getObject('modals').isCurrentModalReady()) {
								content = $(_this.Registry.getObject('modals').modals[_this.Registry.getObject('modals').id].modal);
							}
						} else {
							content = $(target);
						}

						if (targetPlacement === 'replace' && content && content.length) {
							content.find('.summernote-editor').each(function()
							{
								$(this).summernote('destroy');
							});
							content.find('.summernote-editor-custom').each(function()
							{
								$(this).summernote('destroy');
							});

							//
							// Destroy all previously initialized select2 instances
							content.find(".select2").each(function(i, e) {
								if (YoonityJS.Globals.inArray($(e).prop('tagName'), _this.allowedSelect2TagNames) &&
									!$(e).hasClass('select2-manual-init') &&
									$(e).data('select2')) {
									$(e).select2('destroy');
								}
							});
							//
						}
					}, this);
				},

				$bulkAction: function(params)
				{
					var filterSelector = $("#" + params.id);

					var $checkboxes = filterSelector.find(".bulk-action-checkbox:checked").not(":disabled");
					var ids = [];
					$checkboxes.each(function(i, e) {
						if ($(e).attr("name") == 'data[BulkAction][apply_id][]') {
							ids.push($(e).val());
						}
					});
					// Prepare form data for server side processing (BackEnd)
					var formData = this.handleForm();

					formData = {
						"BulkActions": 1,
						"applyIds": ids
					}

					//
					// Load model and set form data
					this.loadModel();
					scopes.addActionScope(function(_this) {
						for (var f in formData) {
							if (formData.hasOwnProperty(f)) {
								_this.getModel().set(f, formData[f]);
							}
						}
					}, this);
					//

					//
					// Call parent method
					scopes.addActionScope(function(_this) {
						_this.$showForm({});
					}, this);
					//
				},

				formInitialization: function(content)
				{
					//
					// Tabs
					var $activeTab = $(content).find(".tabbable > .nav > li.active > a");
					if (!$activeTab.length) {
						$activeTab = $(content).find(".tabbable > .nav > li:first > a");
					}

					if ($activeTab.length) {
						$activeTab.trigger("click");
					}
					//
					
					// Switchery toggles (simple checkboxes)
					$(content).find('.switchery').each(function() {
						$(this).data('switchery', new Switchery($(this)[0]));
					});

					$(content).find(".switch").bootstrapSwitch();

					// Datepicker
					$(content).find(".datepicker").each(function() {
						var
							options = {
								'dateFormat': 'yy-mm-dd'
							},
							attrs = $(this).data();

						for (var a in attrs) {
							if (a.indexOf('jsuiDp') == 0) {
								var attr = a.substr(6);
								options[attr.charAt(0).toLowerCase() + attr.slice(1)] = attrs[a];
							}
						}
						
						$(this).datepicker(options);
					});

					//
					// Select2
					var select2Init = function() {
						$(content).find(".select2").each(function(i, e) {
							if (YoonityJS.Globals.inArray($(e).prop('tagName'), _this.allowedSelect2TagNames) && !$(e).hasClass('select2-manual-init')) {
								$(e).select2($(e).data());
							}
						});

						$(content).find("[data-select2-readonly=1]").select2().select2('readonly', true);

						$(content).find('.select2-currency').select2({
							width: 'resolve',
							placeholder: function(item) {
								var placeholder = $(item.element).data("placeholder");

								return placeholder;
							},
							templateSelection: function(item) {
								var locations = $(item.element).data("locations");

								return $("<span>" + item.text + "<span class='hidden'>" + locations + "</span>" + "</span>");
							},
							templateResult: function(state) {
								var
									locations = $(state.element).data("locations"),
									ret = state.text;

								if (locations) {
									ret += ' <br /><small>' + locations + '</small>';
								}

								return $('<span>' + ret + '</span>');
							}
						});
					};
					select2Init();

					$(content).find(".tabbable > .nav > li > a").on('click', function()
					{
						// Small delay so tab content has time to show before select2 initialization
						setTimeout(function() { select2Init() }, 10);
					});
					//

					//checkbox uniform
					$(content).find(".uniform").uniform();

					//file uniform
					$(content).find(".file-styled").uniform({
						fileButtonClass: 'action btn btn-default'
					});

					// Default initialization
				    $(content).find(".styled").uniform({
				        radioClass: 'choice'
				    });

					//popover
					$(content).find("[data-popup=popover]").popover({
						container: 'body'
					}).on('inserted.bs.popover', function()
					{
						var $tip = $(this).data('bs.popover').$tip;
						$tip.addClass('popover-' + $(this).data('size'));
					});

					// Wysiwyg editor
					$(content).find(".summernote-editor").each(function()
					{
						$(this).summernote();

						// if field is disabled set the editor as disabled
						// as its not done automatically by summernote
						if ($(this).prop("disabled")) {
							$(this).summernote('disable');
						}
					});

					// new tab links
					$(content).on('click', '.new-tab-link', function(e)
					{
						window.open($(this).parent().attr('href'), '_blank');
						return false;
					});
				}
			};
		}
	}
});
