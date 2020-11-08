"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/WizardController'
	],
	
	namespace: 'Controllers',
	
	class: {
		VendorAssessmentWizardController: function(scopes)
		{
			return {
				Extends: scopes.Controllers.WizardController,

				constructor: function(params)
				{
					// Call parent constructor
					this._parent.constructor(params);

					var properties = {
						Registry: null
					};

					YoonityJS.Class.InitProperties.call(this, properties, params);
				},

				$init: function(params)
				{
					this._parent.$init(params);

					var _this = this;

					this.toggleAuditees();
					$('#wizarde').on('change', '#VendorAssessmentAuditor', function() {
						_this.toggleAuditees();
					});

					this.toggleQuestionnaireDesc();
					this.toggleDeleteBtn();
					$('#wizarde').on('change', '#questionnaire-input', function() {
						_this.toggleQuestionnaireDesc();
						_this.toggleDeleteBtn();
					});

					this.on('afterValidate', function() {
						_this.afterValidate();
					});

					this.toggleRecurrence();
					$('#wizarde').on('change', '.vendor-assessment-recurrence', function() {
						_this.toggleRecurrence();
					});
				},

				$triggerNext: function()
				{
					var currentIndex = this.getVar('currentIndex');

					var $request = $(document.createElement('div'))
						.attr('data-yjs-datasource-url', 'post::' + this.getVar('elem').find('form').data('wizarde-validation-url').replace(/__STEP__/g, currentIndex))
						.attr('data-yjs-request', 'vendorAssessmentWizard/next')
						.attr('data-yjs-forms', this.getVar('elem').find('form').data('yjs-form'))
						.attr('data-yjs-target', '.wizarde-content-tab-' + currentIndex);

					new YoonityJS.Init({
						object: $request
					});
				},

				$deleteQuestionnaire: function()
				{
					var questionnaireId = $('#questionnaire-input').val();

					var $parentRequestElem = $(this.Registry.getObject('request').getObject());

					var $request = $(document.createElement('div'))
						.attr('data-yjs-datasource-url', $parentRequestElem.data('href') + '/' + questionnaireId)
						.attr('data-yjs-target', 'modal')
						.attr('data-yjs-request', 'crud/showForm');

					new YoonityJS.Init({
						object: $request
					});
				},

				afterValidate: function()
				{
					var currentIndex = this.getVar('currentIndex');

					if (currentIndex == 1) {
						this.toggleAuditees();
					}
					else if (currentIndex == 2) {
						this.toggleQuestionnaireDesc();
						this.toggleDeleteBtn();
					}
					else if (currentIndex == 4) {
						this.toggleRecurrence();
					}
				},

				toggleAuditees: function()
				{
					var auditorVal = $('#VendorAssessmentAuditor').val();
					var auditeeVal = $('#VendorAssessmentAuditee').val();

					$('#VendorAssessmentAuditee option').prop('disabled', false);
					
					if (auditorVal !== null) {
						auditorVal.forEach(function(val) {
							$('#VendorAssessmentAuditee option[value=' + val + ']').prop('disabled', true);
							$('#VendorAssessmentAuditee option[value=' + val + ']').prop('selected', false);
						});

						$('#VendorAssessmentAuditee').select2({});
					}
				},

				toggleQuestionnaireDesc: function()
				{
					var questionnaireId = $('#questionnaire-input').val();

					$('.questionnaire-desc').addClass('hidden');
					$('.questionnaire-desc-' + questionnaireId).removeClass('hidden');
				},

				toggleDeleteBtn: function()
				{
					var questionnaireId = $('#questionnaire-input').val();

					if (questionnaireId == '') {
						$('.questionnaire-delete-btn').addClass('hidden');
					}
					else {
						$('.questionnaire-delete-btn').removeClass('hidden');
					}
				},

				toggleRecurrence: function()
				{
					var recurrence = $('.vendor-assessment-recurrence').is(':checked');

					if (!recurrence) {
						$('#VendorAssessmentRecurrencePeriod').attr('disabled', 'disabled');

						if ($('.vendor-assessment-recurrence-auto-load').data('switchery')) {
							$('.vendor-assessment-recurrence-auto-load').data('switchery').disable();
						}
						else {
							$('.vendor-assessment-recurrence-auto-load').attr('disabled', true);
						}
					}
					else {
						$('#VendorAssessmentRecurrencePeriod').removeAttr('disabled');

						if ($('.vendor-assessment-recurrence-auto-load').data('switchery')) {
							$('.vendor-assessment-recurrence-auto-load').data('switchery').enable();
						}
					}
				},
			};
		}
	}
});
