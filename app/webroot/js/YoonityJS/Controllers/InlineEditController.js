"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/CrudController'
	],
	
	namespace: 'Controllers',
	
	class: {

		InlineEditController: function(scopes)
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

				initPopovers: function(params)
				{
					this.setVar('init', true);

					//handle popover size
					$(document).on('inserted.bs.popover', '.inline-edit-popover', function(e) {
						var $tip = $(this).data('bs.popover').$tip;
						$tip.addClass('popover-inline-edit');
					});
					
					$(document).on('shown.bs.popover', '.inline-edit-popover', function(e) {
						var $popover = $(this);
						var $tip = $($popover.data('bs.popover').$tip);

						var bottomPosition = $('body').height() - $tip.offset().top - $tip.outerHeight();
						$tip.css({
							top: 'auto',
							bottom: bottomPosition + 'px'
						});

						$tip.on('click', '.popover-cancel', function() {
							$popover.popover('hide');
							return false;
						});

						$tip.on('click', '.popover-submit', function() {
							$popover.closest('tr').find('td').css({pointerEvents: 'none'});
						});

						new YoonityJS.InitTemplate({template: $tip});
					});
				},

				setVar: function(path, val)
				{
					YoonityJS.Globals.vars.set('InlineEdit.' + path, val, true);
				},

				getVar: function(path)
				{
					return YoonityJS.Globals.vars.get('InlineEdit.' + path);
				},

				$open: function(params)
				{
					var init = this.getVar('init');

					// init popovers
					if (!init) {
						this.initPopovers();
					}

					// if there is stored popover from previous inline edit finish it
					this.$closeActivePopover();

					var $popover = $(params.target);

					// store popover in global storage
					this.setVar('popover', $popover);

					// show popover
					$popover.popover('show');
				},

				$closeActivePopover: function()
				{
					if (this.getVar('popover')) {
						$(this.getVar('popover')).popover('hide');
					}
				}
			};
		}
	}
});
