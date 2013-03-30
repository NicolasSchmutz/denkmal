Denkmal.widget = {
	_filesData: {},
	_baseUrl: null,
	_activateIcons: function() {
		jQuery('#denkmal_widget .eventicon:not(.star,.audio)').css('background-image', 'url(' +this._baseUrl+ '/pics/dot.png)');
		jQuery('#denkmal_widget .eventicon.star:not(.audio)').css('background-image', 'url(' +this._baseUrl+ '/pics/dot_star.png)');
		jQuery('#denkmal_widget .eventicon.audio:not(.star)').css('background-image', 'url(' +this._baseUrl+ '/pics/dot_audio.png)');
		jQuery('#denkmal_widget .eventicon.star.audio').css('background-image', 'url(' +this._baseUrl+ '/pics/dot_star_audio.png)');
		jQuery('#denkmal_widget .eventicon.audio').unbind();
		jQuery('#denkmal_widget .eventicon.audio').css('cursor', 'pointer');
		
		jQuery.each(this._filesData, function(hash, file) {
			var audiotitle = file.substring(0, file.length-4);
			jQuery('#denkmal_widget .eventicon.audio'+hash).click(function() {
				jQuery(this).blur();
			});
			jQuery('#denkmal_widget .eventicon.audio'+hash+':not(.star.notooltip)').tooltip({title: audiotitle, classes: 'tooltip_audio', x:10, y:15, track:true, container:'#denkmal_widget'});
			jQuery('#denkmal_widget .eventicon.star.audio'+hash+':not(.notooltip)').tooltip({title: audiotitle, classes: 'tooltip_audio tooltip_star', x:10, y:15, track:true, container:'#denkmal_widget'});
		});
	},
	_initHeight: function() {
		var heightActual = jQuery('#denkmal_widget ul.events').height();
		var heightMax = jQuery('#denkmal_widget #denkmal_events').height();
		if (heightActual <= heightMax) {
			jQuery('#denkmal_widget #denkmal_events').height(heightActual);
		} else {
			var more = jQuery('#denkmal_widget #denkmal_events #denkmal_events_more');
			more.find('> a').click(function(){ Denkmal.widget.expand(); });
			more.show();
		}
		
	},
	expand: function() {
		var heightActual = jQuery('#denkmal_widget ul.events').height();
		jQuery('#denkmal_widget #denkmal_events').animate({height: heightActual+'px'}, 200);
		jQuery('#denkmal_widget #denkmal_events #denkmal_events_more').fadeOut(200);
	},
	addFilesData: function(files) {
		jQuery.each(files, function(hash, file) {
			Denkmal.widget._filesData[hash]=file;
		});
	},
	setHtml: function(html) {
		jQuery('#denkmal_widget').html(html);
	},
	setBaseUrl: function(baseUrl) {
		this._baseUrl = baseUrl;
	},
	init: function() {
		this._activateIcons();
		this._initHeight();
	}
};
