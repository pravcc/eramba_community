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
 * @package       YoonityJS.Config
 * @since         YoonityJS v 1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

"use strict";

(function(Config)
{
	//
	// Set actual version of your application developed on this framework.
	// You need to update this number after every change in your application 
	// so your js files won't be loaded from cache for the first time after update.
	// Recommended way how to version your application is to use a semantic versioning.
	// 
	// Version of your application can be also set with ?app_v={version} added behind YoonityJS file's url (/YoonityJS-1.0.0.js?app_v=1.0.0)
	// By this you can force your application to load this config file right after new changes are applied. Otherwise new version of this config file will
	// be loaded automatically every day.
	if (!Config.appVersion) {
		Config.appVersion = "1.0.0";
	}

	//
	// Whether or not is your application in debug mode.
	// If true, this will automatically overwrite useCache and logProgress settings.
	// Production version needs to be always set to false.
	Config.debugMode = false;
	//

	//
	// Whether or not to use cache while loading files
	Config.useCache = true;

	//
	// Whether or not to log framework and requests lifecycle
	// 
	// @type boolean
	Config.logProgress = false;
	
	//
	// Whether or not to show Progress Bar during the request
	// 
	// @type boolean
	Config.showProgressBar = true;

	//
	// Whether or not to delay Progress Bar (when request is faster then this time, progress bar will stay displayed untill this time expires)
	// 
	// @type integer (miliseconds)
	Config.progressBarDelay = 150;

	//
	// Whether or not to show loader during the request
	// 
	// @type boolean
	Config.showLoader = true;

	//
	// Where the loader suppose to appear
	// Options: full|target|corner
	// 
	// @type string
	Config.loaderType = "target";

	//
	// Whether or not to delay loader (when request is faster then this time, loader will stay displayed untill this time expires)
	// 
	// @type integer (miliseconds)
	Config.loaderDelay = 150;

	//
	// No image url
	Config.noImageFileUrl = "";

	//
	// Path where vendors are stored
	Config.vendorsPath = "Vendors/";

	//
	// Path where plugins are stored
	Config.pluginsPath = "Plugins/";

	//
	// Which plugins should framework load (which you want to use)
	// 
	// @type object Key: Value pairs where Key is name of plugin 
	// and value is object of inner objects with js and css urls
	Config.plugins = {
	};

	//
	// Which vendors should framework load (which you want to use)
	// 
	// @type object Key: Value pairs where Key is name of vendor library 
	// and value is object of inner objects with js and css urls
	Config.vendors = {
	};

})(YoonityJS.Config);
