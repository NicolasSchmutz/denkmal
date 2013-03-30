var events = {
	day: '',
	_timer: null,
	_requestDay: null,
	
	load: function(day) {
		this._requestDay = day;
		clearTimeout(events._timer);
		events._timer = setTimeout(function() {
				$('#events').html('<div class="notice"><img src="/pics/wait_ffffff.gif" /> Lade...</div>');
			}, 500);
		$.getJSON('/'+day, {'audioMode': 'link'}, function(data){
			if (data.weekday != events._requestDay) {
				return;
			}
			clearTimeout(events._timer);
			$('#days li.active').removeClass('active');
			$('#days li.w'+data.weekday).addClass('active');
			$('#events').html(data.eventsHtml);
			if (typeof(_gaq) != 'undefined') {
				_gaq.push(['_trackPageview', '/'+data.weekday]);
			}
		});
	}
};


$.address.change(function(event) {
	if (events.day != event.pathNames) {
		events.load(event.pathNames);
		events.day = event.pathNames;
	}
});