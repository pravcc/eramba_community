"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/WizardController'
	],
	
	namespace: 'Controllers',
	
	class: {
		AwarenessWizardController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.WizardController,

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
					this._parent.$init(params);

					var _this = this;

					this.on('beforeValidate', function() {
						_this.beforeValidate();
					});

					this.on('afterValidate', function() {
						_this.afterValidate();
					});
				},

				$triggerNext: function()
				{
					var currentIndex = this.getVar('currentIndex');

					var $request = $(document.createElement('div'))
						.attr('data-yjs-datasource-url', 'post::' + this.getVar('elem').find('form').data('wizarde-validation-url').replace(/__STEP__/g, currentIndex))
						.attr('data-yjs-request', 'awarenessWizard/next')
						.attr('data-yjs-forms', this.getVar('elem').find('form').data('yjs-form'))
						.attr('data-yjs-target', '.wizarde-content-tab-' + currentIndex);

					new YoonityJS.Init({
						object: $request
					});
				},

				beforeValidate: function()
				{
					var currentIndex = _this.getVar('currentIndex');

					//awareness
					if (currentIndex == 3) {
						_this.setVar('AwarenessProgramTextFile', $('#AwarenessProgramTextFile'));
						_this.setVar('AwarenessProgramVideo', $('#AwarenessProgramVideo'));
						_this.setVar('AwarenessProgramQuestionnaire', $('#AwarenessProgramQuestionnaire'));
					}
				},

				afterValidate: function()
				{
					var currentIndex = _this.getVar('currentIndex');

					//awareness
					if (currentIndex == 3) {
						$('#AwarenessProgramTextFile').replaceWith(_this.getVar('AwarenessProgramTextFile'));
						$('#AwarenessProgramVideo').replaceWith(_this.getVar('AwarenessProgramVideo'));
						$('#AwarenessProgramQuestionnaire').replaceWith(_this.getVar('AwarenessProgramQuestionnaire'));
					}
				},
			};
		}
	}
});
