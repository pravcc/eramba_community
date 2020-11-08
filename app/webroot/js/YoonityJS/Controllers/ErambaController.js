"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		ErambaController: function(scopes)
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

				$toggleSidebar: function(params)
				{
					var
						cookieName = 'sidebarExpanded',
						cookieDays = 365,
						$body = $(document.body),
						readOnly = params.readOnly && params.readOnly === 'true' ? true : false,
						expanded = YoonityJS.Globals.cookies.getCookie(cookieName) === 'true' ? true : false,
						sidebarNotExpandedClass = 'sidebar-xs';

					if (!readOnly) {
						// Reverse sidebar state
						expanded = expanded ? false : true;
						// Save state to cookies
						YoonityJS.Globals.cookies.setCookie(cookieName, expanded, cookieDays);
					}

					if (!expanded && !$body.hasClass(sidebarNotExpandedClass)) {
						$body.addClass(sidebarNotExpandedClass);
					} else if (expanded && $body.hasClass(sidebarNotExpandedClass)) {
						$body.removeClass(sidebarNotExpandedClass);
					}
				},

				$toggleElement: function(params)
				{
					var
						elem = this.Registry.getObject('request').getObject(),
						type = params.type || null;

					if (type === 'show') {
						$(elem).show();
					} else if (type === 'hide') {
						$(elem).hide();
					} else if (type === 'toggle') {
						$(elem).toggle();
					}
				},

				$redirectLocation: function(params)
				{
					window.location.href = $(_this.Registry.getObject('request').getObject()).attr('data-yjs-data-url');
				},

				$navbar: function(params)
				{
					var _this = this;

					$(window).on('resize', function() {
						_this.fixedNavbar();
					});

					_this.fixedNavbar();
				},

				fixedNavbar: function()
				{
					var navbarHeight = $('#navbar > .navbar').outerHeight() + 'px';

					$('#navbar').css({height: navbarHeight});
				},

				$sidebar: function(params)
				{
					var _this = this;

					$(window).on('resize', function() {
						_this.fixedSidebar();
					});

					_this.fixedSidebar();
				},

				fixedSidebar: function()
				{
					var minHeight = 600;
					var fixed = true;

					if ($(window).height() < minHeight) {
						fixed = false;
					}

					if (fixed) {
						$('#sidebar').addClass('sidebar-fixed');
					}
					else {
						$('#sidebar').removeClass('sidebar-fixed');
					}
				}
			};
		}
	}
});
