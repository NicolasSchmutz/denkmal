var add = {
	_previewLast: null,
	_previewTimer: null,
	_locationIdLast: null,
	
	formChange: function() {
		if ($('form#add #location').val() == '-') {
			$('form#add #location_new').slideDown(200);
		} else {
			$('form#add #location_new').slideUp(200);
		}
		
		var url;
		if (url = $('form#add #location :selected').attr('class')) {
			$('form#add #location_website_info').html('<a href="'+url+'" target="_blank">'+utils.getDomain(url)+'</a>');
		} else {
			$('form#add #location_website_info').html('');
		}
		
		var previewNext = $('form#add .forPreview').serialize()
		if (this._previewLast != (this._previewLast = previewNext)) {
			clearTimeout(this._previewTimer);
			this._previewTimer = setTimeout(function() { add.preview(); }, 300);
		}
		
		var locationIdNext = $('form#add #location').val();
		if (this._locationIdLast != (this._locationIdLast = locationIdNext)) {
			if (locationIdNext == '-') {
				$('#events').hide();
			} else {
				$('#events_cont').html('');
				$.getJSON("/add/events", {location_id:locationIdNext}, function(data){
					$('#events').show();
					$('#events_cont').html(data.eventsHtml);
					audioplayer.activateIcons();
				});
			}
		}
		
	},
	preview: function() {
		$.getJSON("/add/preview", $('form#add .forPreview').serializeArray(), function(data){
			$('form#add #preview ul.events li').html(data.html);
			if (data.notice) {
				$('form#add #preview .notice').html(data.notice);
			} else {
				$('form#add #preview .notice').html('');
			}
		});
	}
};



$(document).ready(function(){
	$('form#add input, form#add select, form#add textarea')
		.bind('keypress keydown keyup change', function(){ add.formChange(); });
	if ($('form#add').length > 0) {
		add.formChange();
	}
});