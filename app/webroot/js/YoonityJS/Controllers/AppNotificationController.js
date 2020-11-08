"use strict";

YoonityJS.classFile({
    uses: [
        '//Controllers/CrudController'
    ],
    
    namespace: 'Controllers',
    
    class: {
        AppNotificationController: function(scopes)
        {
            return {
                Extends: scopes.Controllers.CrudController,

                // widget base jquery element 
                elem: null,
                showDropzone: false,

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
                    YoonityJS.Globals.vars.set('AppNotification.' + path, val, true);
                },

                /**
                 * Get global wizard variable.
                 */
                getVar: function(path)
                {
                    return YoonityJS.Globals.vars.get('AppNotification.' + path);
                },

                $init: function(params)
                {
                    var _this = this;

                    $('#app-notification-list-ul').on('click', '.app-notification-item', function () {
                        if ($(this).data('redirect-url')) {
                            window.location.href = $(this).data('redirect-url');
                        }

                        $(this).removeClass('unseen');
                    });

                    $('#app-notification-list-ul').on('scroll', function() {
                        var nextPageExists = ($('.app-notification-list-next').length && $('.app-notification-list-next').last().find('.app-notification-list-next-request').length);
                        var scrollPositionTrigger = $(this).scrollTop() + $(this).innerHeight() + 20 >= $(this)[0].scrollHeight;

                        if (nextPageExists && scrollPositionTrigger && !_this.getVar('locked')) {
                            _this.setVar('locked', true);

                            new YoonityJS.Init({
                                object: $('.app-notification-list-next').last().find('.app-notification-list-next-request')
                            });
                        }
                    });
                },

                $toggleClick: function(params)
                {
                    var _this = this;

                    if (!this.getVar('init')) {
                        this.setVar('init', true);

                        $('#app-notification-list-toggle .app-notification-list-unseen-count').remove();

                        new YoonityJS.Init({
                            object: $('#app-notification-list-ul')
                        });
                    }
                },

                $unlockLoad: function(params)
                {
                    this.setVar('locked', false);
                },
            };
        }
    }
});
