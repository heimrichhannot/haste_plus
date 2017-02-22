(function($)
{

    $.extend(HASTE_PLUS, {
        isTruthy: function(value)
        {
            return typeof value !== 'undefined' && value !== null;
        }
    });

}(jQuery));
