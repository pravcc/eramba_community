$(function() {
    var savedSelects = [];

    function saveSelectOptions(btnElem) {
        var wrap = btnElem.closest('.form-group-quick-create');
        var select = wrap.find('select');

        savedSelects[select.attr('name')] = getSelectKeys(select);
    }

    function getSelectKeys(select) {
        var optionValues = [];

        select.find('option').each(function() {
            var val = $(this).val();
            optionValues[val] = val;
        });

        return optionValues;
    }

    function setActiveNewSelectItems() {
        $('.form-group-quick-create select').each(function() {
            var name = $(this).attr('name');
            if (typeof savedSelects[name] !== 'undefined' && savedSelects[name] !== null) {
                diff = diffArrays(getSelectKeys($(this)), savedSelects[name]);
                activeSelectItem($(this), diff);
                savedSelects[name] = null;
            }
        });
    }

    function activeSelectItem(select, key) {
        if (typeof key[0] === 'undefined') {
            return;
        }

        if (typeof select.attr('multiple') !== 'undefined') {
            var selected = select.val() || [];
            selected.push(key[0]);
            select.val(selected);
        }
        else {
            select.val(key[0]);
        }

        select.trigger('change');

    }

    function diffArrays(a, b) {
        diff = [];

        a.forEach(function(item) {
            if (b.indexOf(item) == -1) {
                diff.push(item);
            }
        });

        return diff;
    }

    $(document).on('click', '.form-group-quick-create a[data-ajax-action="quick-create"]', function() {
        saveSelectOptions($(this));
    });

    $('#main-content').on('Eramba.Modal.loadHtml', function() {
        setActiveNewSelectItems();
    });
});