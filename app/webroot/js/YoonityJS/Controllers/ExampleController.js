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
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		ExampleController: function(scopes)
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

				$index: function(params)
				{
					this.setLayout('//Templates/html/Layouts/example.html', true);
					this.addTag('test_tag', 'Example text added by addTag() function');
					this.addElement(
						'another_test_tag', 
						'//Templates/html/Elements/example_element.html', 
						{'test_tag_in_element': 'Hello world! (This text was added by addElement() function inside ExampleController.'}, 
						true
					);
					this.addTag('another_test_tag_in_element', '<i>Some cursive text added by addTag() function.</i>');
					this.addTag('testovanie', [
					{
						'text': 'Hello',
						'second_text': 'World!'
					},
					{
						'text': 'Hey',
						'second_text': 'there!'
					}]);
				},

				$fillTemplate: function(params)
				{
					this.addTag('menu_items', [
					{
						'menu_item_name': 'jedna'
					},
					{
						'menu_item_name': 'dva'
					},
					{
						'menu_item_name': 'tri'
					}]);
				}
			};
		}
	}
});
