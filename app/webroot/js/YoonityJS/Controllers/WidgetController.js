"use strict";

YoonityJS.classFile({
    uses: [
        '//Controllers/CrudController'
    ],
    
    namespace: 'Controllers',
    
    class: {
        WidgetController: function(scopes)
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
                    YoonityJS.Globals.vars.set('Widget.' + path, val, true);
                },

                /**
                 * Get global wizard variable.
                 */
                getVar: function(path)
                {
                    return YoonityJS.Globals.vars.get('Widget.' + path);
                },

                /**
                 * Init wizard.
                 */             
                $init: function(params)
                {
                    var _this = this;

                    this.elem = $(this.Registry.getObject('request').getObject());

                    var $widget = this.elem;

                    $(window).on('dragover', function(e) {
                        _this.dragover();
                    });

                    $(window).on('dragleave', function(e) {
                        _this.dragleave();
                    });

                    $widget.find('.btn-widget-add-file').on('click', function() {
                        $widget.find('.dropzone-widget-add').trigger('click');
                    });

                    var uploadButton = Ladda.create($widget.find('.btn-widget-add-file')[0]);

                    // Defaults
                    Dropzone.autoDiscover = false;

                    var attachmentsDropzone = new Dropzone('#' + $widget.attr('id') + ' .dropzone-widget-add', {
                        url: $widget.data('dropzone-url'),
                        paramName: 'file',
                        dictDefaultMessage: $widget.data('dropzone-default-message'),
                        maxFilesize: $widget.data('dropzone-max-filesize'), // MB
                        init: function()
                        {
                            this.on('dragover', function(event) {
                                _this.dragover();
                            });

                            this.on('dragleave', function(event) {
                                _this.dragleave();
                            });

                            this.on('drop', function(event) {
                                _this.dragleave();
                            });
                        },
                        addedfile: function(file)
                        {
                        },
                        sending: function(file)
                        {
                            uploadButton.start();
                        },
                        uploadprogress: function(file, progress)
                        {
                            if (!uploadButton.isLoading()) {
                                uploadButton.start();
                            }
                            uploadButton.setProgress(progress / 100);
                        },
                        complete: function(file)
                        {
                            uploadButton.stop();
                        },
                        success: function(file)
                        {
                            // notification
                            new PNotify({
                                title: file.name + ' ' + $widget.data('dropzone-success-message'),
                                addclass: 'bg-success',
                                // text: message,
                                timeout: 4000
                            });

                            // reload list
                            new YoonityJS.Init({
                                object: $widget.find('.widget-story-list')
                            });
                        },
                        error: function(file, message)
                        {
                            //notification
                            new PNotify({
                                title: $widget.data('dropzone-error-message'),
                                addclass: 'bg-danger',
                                text: message,
                                timeout: 4000
                            });
                        },
                    });
                },

                dragover: function()
                {
                    this.showDropzone = true;

                    this.elem.find('.dropzone-widget-add').removeClass('hidden');
                },

                dragleave: function()
                {
                    var _this = this;

                    this.showDropzone = false;

                    setTimeout(function() {
                        if (!_this.showDropzone) {
                            _this.elem.find('.dropzone-widget-add').addClass('hidden');
                        }
                    }, 200);
                },

            };
        }
    }
});
