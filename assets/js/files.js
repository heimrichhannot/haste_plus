(function($){

	$.extend(HASTE_PLUS, {
		sanitizeFileName: function (strFileName)
		{
			strFileName = strFileName.toLowerCase();
			strFileName = strFileName.replace(/[^a-z0-9_-]/g, '_');
			strFileName = strFileName.replace(/_+/g, '_');
			return strFileName;
		}
	});

}(jQuery));
