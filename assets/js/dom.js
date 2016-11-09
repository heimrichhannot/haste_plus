(function($){

	$.extend(HASTE_PLUS, {
		getTextWithoutChildren: function(element, notrim)
		{
			var result = element.clone().children().remove().end().text();

			if (typeof notrim !== 'undefined' && notrim == true)
				return result;
			else
				return result.trim();
		},
		scrollTo: function($anchor, offsetSize, delay, force) {
			setTimeout(function () {
				if (!HASTE_PLUS.elementInViewport($anchor) || typeof force !== 'undefined')
				{
					$('html, body').animate({scrollTop: ($anchor.offset().top - offsetSize)}, 'slow');
				}
			}, delay);
		},
		elementInViewport: function(el) {
			el = el.get(0);
			var top = el.offsetTop;
			var left = el.offsetLeft;
			var width = el.offsetWidth;
			var height = el.offsetHeight;

			while(el.offsetParent) {
				el = el.offsetParent;
				top += el.offsetTop;
				left += el.offsetLeft;
			}

			return (
				top < (window.pageYOffset + window.innerHeight) &&
				left < (window.pageXOffset + window.innerWidth) &&
				(top + height) > window.pageYOffset &&
				(left + width) > window.pageXOffset
			);
		}
	});

}(jQuery));
