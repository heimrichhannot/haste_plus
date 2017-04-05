(function($)
{
    $.extend(HASTE_PLUS, {
        // only callable on HTTPS
        getCurrentLocation: function(success, error)
        {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function() {
                    HASTE_PLUS.call(success, position.coords.latitude, position.coords.longitude);
                });
            }
            else
            {
                HASTE_PLUS.call(error);
            }
        }
    });
}(jQuery));
