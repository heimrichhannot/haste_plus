(function($)
{

    $.extend(HASTE_PLUS, {
        isTruthy: function(value)
        {
            return typeof value !== 'undefined' && value !== null;
        },
        call: function(func) {
            if (typeof func === 'function')
            {
                func.apply(this, Array.prototype.slice.call(arguments, 1));
            }
        }
    });

}(jQuery));
