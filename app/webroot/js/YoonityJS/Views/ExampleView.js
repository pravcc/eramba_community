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
 * @package       YoonityJS.Views
 * @since         YoonityJS v 1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

"use strict";

YoonityJS.classFile({
	uses: [
		'//Views/AppView'
	],
	
	namespace: 'Views',
	
	class: {
		ExampleView: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Libs.View,
				
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
				}
			};
		}
	}
});

