;(function($){

    var plugin = {};

    var defaults = {
        input: null,
        url: null,
        requestType: 'GET',
        requestKey: ['selectedValues'],
        responseKey: null,
        assocInput: null,
        // functionName: function() {}
    }

    $.fn.erambaAutoComplete = function(options) {

        if(this.length == 0) return this;

        if(this.length > 1){
            this.each(function(){$(this).erambaAutoComplete(options)});
            return this;
        }

        var data = {};

        var elem = this;
        plugin.elem = this;

        var loadedAssocData = [];

        // var ...

        //init
        var init = function() {
            data = $.extend({}, defaults, elem.data(), options);

            if (data.input == null) {
                data.input = '#' + elem.attr('id');
            }

            if (typeof data.requestKey === 'string') {
                data.requestKey = [data.requestKey];
            }

            if (typeof data.responseKey === 'string') {
                data.responseKey = [data.responseKey];
            }

            bindEvents();
        }

        //PRIVATE FUNCTIONS

        var bindEvents = function() {
            elem.on('click, change', function() {
                // var selectedValues = $(this).val();
                var selectedValues = getSelectedValues();
                if (selectedValues != null) {
                    getRelatedData(selectedValues);
                }
            });
        }

        var getSelectedValues = function() {
            var selectedValues = {};
            var empty = true;
            var i = 0;
            $(data.input).each(function() {
                var values = $(this).val() || [];
                selectedValues[data.requestKey[i]] = JSON.stringify(values);
                if (values.length > 0) {
                    empty = false;
                }
                i++;
            });

            return (empty) ? null : selectedValues;
        }

        var getRelatedData = function(selectedValues) {
            $.ajax({
                url: data.url,
                type: data.requestType,
                dataType: "JSON",
                data: selectedValues,
            })
            .done(function(response) {
                var i = 0;
                $(data.assocInput).each(function() {
                    var responseData = response;
                    if (data.responseKey !== null) {
                        responseData = response[data.responseKey[i]];

                    }
                    updateAssocInput($(this), responseData);
                    i++;
                });
            });
        }

        var updateAssocInput = function(inputElem, responseData) {
            var assocInput = inputElem;
            var inputData = assocInput.val() || [];
            var finalData = [];

            if (responseData.length) {
                loadedAssocData = loadedAssocData.concat(responseData);
                finalData = inputData.concat(responseData);
            }
            else {
                finalData = $.map(inputData, function( n ) {
                    if (loadedAssocData.indexOf(n) != -1) {
                        return null;
                    }
                    return n;
                });
            }

            finalData = mergeTouchedData(finalData, assocInput);

            assocInput.val(finalData);
            assocInput.trigger('change');
        }

        var mergeTouchedData = function(data, input) {
            var name = input.prop('name');
            var removedData = [];

            if (typeof removedItems[name] !== 'undefined') {
                removedData = removedItems[name];
            }

            data = $(data).not(removedData).get();

            return data;
        }

        //PUBLIC FUNCTIONS

        // el.functionName = function() {
        // }

        init();

        return this;
    }

    var autoload = function() {
        $('select.eramba-auto-complete').erambaAutoComplete();
    }

    var removedItems = {};

    var memoryBehavior = function() {
        removedItems = {};
        $('.select2').on('select2-removing', function(e) {
            var name = $(e.target).prop('name');
            if (typeof removedItems[name] === 'undefined') {
                removedItems[name] = [];
            }
            removedItems[name].push(e.val);
        });
    }

    $(function() {
        setTimeout(function() {
            memoryBehavior();
        }, 500);
        $('#main-content').on('Eramba.Modal.loadHtml', function() {
            setTimeout(function() {
                autoload();
                memoryBehavior();
            }, 500);
        });
    });

})(jQuery);

