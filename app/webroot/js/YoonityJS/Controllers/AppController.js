/**
 * YoonityJS : MVC Framework for JavaScript FrontEnd Development (http://yoonityjs.org)
 * Copyright (c) Viktor Huszár (http://viktor.huszar.sk)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Viktor Huszár (http://viktor.huszar.sk)
 * @link          http://yoonityjs.org YoonityJS Project
 * @package       YoonityJS.Controllers
 * @since         YoonityJS v 1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

"use strict";

YoonityJS.classFile({
	uses: [
		'//Libs/Controller'
	],
	
	namespace: 'Controllers',
	
	class: {
		AppController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Libs.Controller,
				
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

				$load: function(params)
				{
					// Prepare form data for server side processing (BackEnd)
					var formData = this.handleForm();

					//
					// Load and fill model from datasource
					this.loadModel();
					scopes.addActionScope(function(_this)
					{
						_this.getModel().process({
							data: formData
						});
					}, this);
					//
				},

				/**
				 * (Deprecated) Pass-through function for load - use $load() method directly instead and set serverMethod to GET
				 * @param  {[type]} params [description]
				 * @return {[type]}        [description]
				 */
				$showForm: function(params)
				{
					_this.Registry.getObject('request').setServerMethod('GET');
					_this.$load(params);
				},

				/**
				 * (Deprecated) Pass-through function for load - use $load() method directly instead and set serverMethod to POST
				 * @param  {[type]} params [description]
				 * @return {[type]}        [description]
				 */
				$submitForm: function(params)
				{
					_this.Registry.getObject('request').setServerMethod('POST');
					_this.$load(params);
				},

				$showModal: function(params)
				{
					var modalId = this.Registry.getObject('request').getModalId();
					if (modalId) {
						this.Registry.getObject('modals').show(modalId);
					}
				},

				$hideModal: function(params)
				{
					var modalId = this.Registry.getObject('request').getModalId();
					if (modalId) {
						this.Registry.getObject('modals').hide(modalId);
					}
				},

				$closeModal: function(params)
				{
					var modalId = this.Registry.getObject('request').getModalId();
					if (modalId) {
						this.Registry.getObject('modals').close(modalId);
					}
				},

				$setCookie: function(params)
				{
					var
						name = params.name ? params.name : null,
						value = params.value ? params.value : null,
						days = params.days ? params.days : null;

					if (name != null && value != null && days != null) {
						YoonityJS.Globals.cookies.setCookie(name, value, days);
					}
				},

				$getCookie: function(params)
				{
					var
						name = params.name ? params.name : null,
						value = null;
					if (name != null) {
						var value = YoonityJS.Globals.cookies.getCookie(name);
					}

					return value;
				},

				$toggleFields: function(params)
				{
					var
						obj = this.Registry.getObject('request').getObject(),
						value = $(obj).val(),
						onValue = params.onValue ? params.onValue : 0,
						fieldsClass = params.fieldsClass ? params.fieldsClass : false,
						parentClass = params.parentClass ? params.parentClass : false,
						$elem = parentClass !== false ? $("[data-yjs-field-class='" + fieldsClass + "']").closest(parentClass) : $(fieldsClass);
					
					if (value == onValue) {
						$elem.show();
					} else {
						$elem.hide();
					}
				},

				$updateFormFieldValue: function(params)
				{
					_this.$submitForm(params);
					
					scopes.addActionScope(function(_this)
					{
						var obj = _this.Registry.getObject('request').getObject();
						if ($(obj).attr('type') === 'checkbox' || $(obj).attr('type') === 'radio') {
							if (_this.getModel().get('value') === 'checked') {
								if ($(obj).prop('checked') == false) {
									//$(obj).prop('checked', true);
								}
							} else if (_this.getModel().get('value') === 'unchecked') {
								if ($(obj).prop('checked') == true) {
									//$(obj).prop('checked', false);
								}
							} else {
								$(obj).val(_this.getModel().get('value'));
							}
						} else {
							$(obj).val(_this.getModel().get('value'));
						}
						
					}, this);
				},

				$triggerRequest: function(params)
				{
					var elems = [];
					for (var param in params) {
						if (params.hasOwnProperty(param)) {
							var selector = params[param];
							if (selector.indexOf('#') == 0) {
								var elem = document.getElementById(selector.substr(1));
								if (elem) {
									elems.push(elem);
								}
							} else if (selector.indexOf('.') == 0) {
								var elemsTemp = document.getElementsByClassName(selector.substr(1));
								for (var i = 0; i < elemsTemp.length; ++i) {
									elems.push(elemsTemp[i]);
								}
							}
						}
					}

					for (var e in elems) {
						new YoonityJS.Init({
							object: elems[e]
						});
					}
				},

				$showNotification: function(params)
				{
					var
						message = params.message ? params.message : false,
						type = params.type ? params.type : false;

					if (message) {
						this.Registry.getObject('notifications').show(message, type);
					}
				},

				$showTooltip: function(params)
				{
					$('#modal_1').removeClass('modal-custom-fixed');
					$('#modal_dialog_1').removeClass('modal-lg');
					this.setLayout('//Templates/html/Layouts/modal_tooltip.html', true);
				}
			};
		}
	}
});
