"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/CrudController'
	],
	
	namespace: 'Controllers',
	
	class: {
		WizardController: function(scopes)
		{
			return {
				Extends: scopes.Controllers.CrudController,

				constructor: function(params)
				{
					// Call parent constructor
					this._parent.constructor(params);

					var properties = {
						Registry: null
					};

					YoonityJS.Class.InitProperties.call(this, properties, params);
				},

				/**
				 * Set global wizard variable.
				 */
				setVar: function(path, val)
				{
					YoonityJS.Globals.vars.set('Wizarde.' + path, val, true);
				},

				/**
				 * Get global wizard variable.
				 */
				getVar: function(path)
				{
					return YoonityJS.Globals.vars.get('Wizarde.' + path);
				},

				/**
				 * Set event callback.
				 */
				on: function(eventName, callback)
				{
					var events = this.getVar('events');

					if (!events.hasOwnProperty(eventName)) {
						events[eventName] = [];
					}

					events[eventName].push(callback);

					this.getVar('events', events);
				},

				/**
				 * Trigger event.
				 */
				trigger: function(eventName)
				{
					var events = this.getVar('events');

					if (events.hasOwnProperty(eventName)) {
						events[eventName].forEach(function(callback) {
							callback();
						});
					}
				},

				/**
				 * Init wizard.
				 */				
				$init: function(params)
				{
					var _this = this;

					this.setVar('elem', $('#wizarde'));

					var $wizard = this.getVar('elem');

					this.setVar('events', {});

					if (typeof params.reset !== 'undefined' && params.reset == '1' || !this.getVar('currentIndex')) {
						this.setVar('currentIndex', 1);
					}

					this.setVar('maxIndex', $wizard.find('.wizarde-header-tab').length);
					this.setVar('preventChange', false);

					$wizard.find('.wizarde-header-tab').each(function(index) {
						$(this).attr('data-wizarde-index', index + 1);
					});
					$wizard.find('.wizarde-content-tab').each(function(index) {
						$(this).attr('data-wizarde-index', index + 1);
						$(this).addClass('wizarde-content-tab-' + (index + 1));
					});

					var currentIndex = this.getVar('currentIndex');

					for (var i = 1; i < currentIndex; i++) {
						this.setTabAsDone(i);
					}
					this.changeTab(currentIndex);

					this.setButtons();
				},

				/**
				 * Change active tab.
				 */
				changeTab: function(index)
				{
					var $wizard = this.getVar('elem');

					$wizard.find('.wizarde-header-tab.current').removeClass('current');
					$wizard.find('.wizarde-content-tab.current').removeClass('current');

					$wizard.find('.wizarde-header-tab[data-wizarde-index=' + index + ']').removeClass('done');

					$wizard.find('.wizarde-header-tab[data-wizarde-index=' + index + ']').addClass('current');
					$wizard.find('.wizarde-content-tab[data-wizarde-index=' + index + ']').addClass('current');

					this.setVar('currentIndex', index);
				},

				/**
				 * Set tab as done.
				 */
				setTabAsDone: function(index)
				{
					var $wizard = this.getVar('elem');

					$wizard.find('.wizarde-header-tab[data-wizarde-index=' + index + ']').addClass('done');
				},

				/**
				 * Toggle wizard buttons, set request data for next button.
				 */
				setButtons: function()
				{
					var currentIndex = this.getVar('currentIndex');
					var maxIndex = this.getVar('maxIndex');

					if (currentIndex == 1) {
						$('.wizard-prev-btn').hide();
					} else {
						$('.wizard-prev-btn').show();
					}

					if (currentIndex == maxIndex) {
						$('.wizard-submit-btn').show();
						$('.wizard-next-btn').hide();
					} else {
						$('.wizard-submit-btn').hide();
						$('.wizard-next-btn').show();
					}

					// $('.wizard-next-btn').attr('data-yjs-datasource-url', 'post::' + this.getVar('elem').find('form').data('wizarde-validation-url').replace(/__STEP__/g, currentIndex))
					// 	.attr('data-yjs-forms', this.getVar('elem').find('form').data('yjs-form'))
					// 	.attr('data-yjs-target', '.wizarde-content-tab-' + currentIndex)
					// 	.attr('data-yjs-initialized-events', '');

					// new YoonityJS.InitElement({
					// 	element: $('.wizard-next-btn')
					// });
				},

				/**
				 * Go to preview step.
				 */
				$prev: function(params)
				{
					if (this.getVar('preventChange')) {
						return;
					}

					var currentIndex = this.getVar('currentIndex');
					var newIndex = currentIndex - 1;

					if (newIndex >= 1) {
						this.setTabAsDone(currentIndex);
						this.changeTab(newIndex);
						this.setButtons();
					}
				},

				/**
				 * Go to next step. Executes step validation.
				 */
				$next: function(params)
				{
					if (this.getVar('preventChange')) {
						return;
					}

					var _this = this;

					var currentIndex = this.getVar('currentIndex');
					var maxIndex = this.getVar('maxIndex');
					var newIndex = currentIndex + 1;

					if (newIndex <= maxIndex) {
						//prevent multiple action click
						this.setVar('preventChange', true);

						this.$validateTab();

						this.addCallback('shutdown', function(content) {
							if ($(content).find('.has-error').length == 0 && !$(content).hasClass('has-error')) {
								var currentIndex = _this.getVar('currentIndex');
								var newIndex = currentIndex + 1;

								_this.setTabAsDone(currentIndex);
								_this.changeTab(newIndex);
								_this.setButtons();
							}

							_this.setVar('preventChange', false);
						});
					}
				},

				/**
				 * Step validation.
				 */
				$validateTab: function(params)
				{
					var _this = this;

					this.trigger('beforeValidate');

					this.addCallback('shutdown', function(content) {
						_this.trigger('afterValidate');
					});

					this.$load(params);
				},

			};
		}
	}
});
