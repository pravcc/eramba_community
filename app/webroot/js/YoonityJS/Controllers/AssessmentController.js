"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/CrudController'
	],
	
	namespace: 'Controllers',
	
	class: {
		AssessmentController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.CrudController,

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

				$init: function(params)
				{
					var _this = this;

					// trigger text filter input change after keyup
					$('#assessment-filter-text').on('keyup', function() {
						$(this).trigger('change');
					});

					// submit form on submit btn
					$('.page-content').on('click', '#assessment-submit', function() {
						$('#assessment-form').submit();
					});

					// init chapters sidebar
					this.sidebarChaptersInit($('#assessment-sidebar'));
					$(window).on('resize', function() {
						_this.sidebarChaptersInit($('#assessment-sidebar'));
					});

					$('#assesment-content').on('change', '.vendor-assessment-question-options', function() {
						_this.toggleOptionWarning($(this).closest('.assessment-feedback'));
					});
				},

				$filter: function(params)
				{
					if (params.chapter || params.state) {
						this.$resetFilter();

						if (params.chapter) {
							$('#assessment-filter-chapter').val(params.chapter)	
						}

						if (params.state) {
							$('#assessment-filter-state').val(params.state)	
						}
					}

					this.filter();
				},

				filter: function()
				{
					$('.assessment-feedback').addClass('hidden');
					$('.assesment-chapter').addClass('hidden');

					var chapterFilter = $('#assessment-filter-chapter').val();
					var stateFilter = $('#assessment-filter-state').val();
					var textFilter = $('#assessment-filter-text').val().toLowerCase();

					$('.filter-' + chapterFilter + '.filter-' + stateFilter).parent().removeClass('hidden');

					if (textFilter != '') {
						$('.assessment-question').each(function() {
							var title = $(this).data('filter-title').toLowerCase();
							var desc = $(this).data('filter-desc').toLowerCase();

							if (!title.includes(textFilter) && !desc.includes(textFilter)) {
								$(this).parent().addClass('hidden');
							}
						});
					}

					$('.assesment-chapter').each(function() {
						if ($(this).find('.assessment-feedback').length != $(this).find('.assessment-feedback.hidden').length) {
							$(this).removeClass('hidden');
						}
					});

					this.toggleEmptyMessage();
					this.toggleResetButton();
				},

				$resetFilter: function(params)
				{
					$('#assessment-filter-chapter').val('chapter-all');
					$('#assessment-filter-state').val('state-all');
					$('#assessment-filter-text').val('');

					$('#assessment-filter-chapter').trigger('change');

					this.toggleResetButton();
				},

				toggleEmptyMessage: function(params)
				{
					if ($('.assessment-feedback').length === $('.assessment-feedback.hidden').length) {
						$('#assessment-filter-empty-message').removeClass('hidden');
					}
					else {
						$('#assessment-filter-empty-message').addClass('hidden');
					}
				},

				toggleResetButton: function(params)
				{
					if ($('#assessment-filter-chapter').val() == 'chapter-all'
						&& $('#assessment-filter-state').val() == 'state-all'
						&& $('#assessment-filter-text').val() == ''
					) {
						$('#assessment-filter-reset').addClass('hidden');
						$('#assessment-filter-icon').removeClass('icon-filter3').addClass('icon-filter4');
					}
					else {
						$('#assessment-filter-reset').removeClass('hidden');
						$('#assessment-filter-icon').removeClass('icon-filter4').addClass('icon-filter3');

					}
				},

				toggleOptionWarning: function($feedback)
				{
					var $input = $feedback.find('.vendor-assessment-question-options');

					if ($input.length < 1) {
						return;
					}

					var value = $input.val();

					$feedback.find('.vendor-assessment-option-warning').addClass('hidden');
					$feedback.find('.vendor-assessment-option-warning-' + value).removeClass('hidden');
				},

				$reloadSidebar: function(params)
				{
					var _this = this;

					this.addCallback('beforeRender', function() {
						// get view content from response
						var $content = $($.parseHTML(_this.Registry.getObject('templates').getLayout())[1]);

						// remember scroll position
						var scrollPosition = $('#assessment-sidebar-chapters').scrollTop();

						// insert new content
						$('#assessment-sidebar').prepend($content[0].outerHTML);

						// YoonityJS init template
						new YoonityJS.InitTemplate({template: $('.sidebar-content-wrapper:nth-child(1)')});
						_this.formInitialization($('.sidebar-content-wrapper:nth-child(1)'));

						// init chapters element
						_this.sidebarChaptersInit($('.sidebar-content-wrapper:nth-child(1)'));

						// remove old sidebar content
						$('.sidebar-content-wrapper:nth-child(2)').remove();

						// set stored scroll position
						$('#assessment-sidebar-chapters').scrollTop(scrollPosition);
					});

					this.$load(params);
				},

				sidebarChaptersInit: function($content)
				{
					var contentHeight = 0;

					$content.find('.sidebar-portal-group:not(#assessment-sidebar-chapters)').each(function() {
						contentHeight += $(this).outerHeight();
					});

					var elemHeight = $(window).height() - $('.main-navbar').outerHeight() - contentHeight;

					$content.find('#assessment-sidebar-chapters').css({
						overflowY: 'auto',
						height: elemHeight + 'px'
					});
				},
			};
		}
	}
});
