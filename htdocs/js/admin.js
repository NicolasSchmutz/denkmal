var admin = {
	events: {
		init: function() {
			$('ul.events li .option.star').click(function() {
				admin.events.option($(this).parent('li'), 'star');
			});
			$('ul.events li .option.audio').click(function() {
				admin.events.option($(this).parent('li'), 'audio');
			});
			$('ul.events li .option.locked').click(function() {
				admin.events.option($(this).parent('li'), 'locked');
			});
			$('ul.events li .option.del').click(function() {
				if (confirm('Really remove this event?')) {
					admin.events.option($(this).parent('li'), 'del');
				}
			});
			$('ul.events li .option.enabled').click(function() {
				admin.events.option($(this).parent('li'), 'enabled');
			});
			$('ul.events li .option.blocked').click(function() {
				admin.events.option($(this).parent('li'), 'blocked');
			});
			

			$('ul.events li .description a').attr('href', '');
			$('ul.events li').each(function(i) {
				var eventId = $(this).attr('id').substring(5);
				var description = $(this).children('.description');
				description.editable('/admin/index/description/', {
						submitdata: {'id': eventId},
						cssclass: 'editable',
						height: '15px',
						data: function(value,settings) { return utils.stripHtml(value); },
						callback : function(value,settings) { $(this).parent('li').addClass('locked'); }
				});
			});
		},
		option: function(li, option) {
			var state = li.hasClass(option);
			var eventId = li.attr('id').substring(5);
			
			if (option == 'audio') {
				if (state == true) {
					li.children('.option.audio').attr('title', '');
				} else {
					this.selectAudio(eventId);
					return;
				}
			}
			
			$.getJSON('/admin/index/option/', {'id': eventId, 'option': option, 'state': !state}, function(data){
				li.toggleClass(option, data.state);
				if (data.reload) {
					document.location.reload();
				}
			});
		},
		selectAudio: function(eventId) {
			window.open('/admin/index/audio/?id='+eventId, 'audio', 'height=500,width=360,status=0,toolbar=0,location=0,menubar=0,directories=0,scrollbars=1')
		}
	},
	audios: {
		_eventId: null,
		init: function() {
			$('#audios_more').click(function() {
				$.getJSON('/admin/index/audioall/', function(data){
					$('#audios_more').parent('h3').after(data.html);
					$('#audios_more').replaceWith('Alle');
					admin.audios.activateLinks();
				});
			});
			this.activateLinks();
		},
		activateLinks: function() {
			$('ul.audios li a').unbind().click(function(){
				var file = $(this).text();
				$.getJSON('/admin/index/option/', {'id': admin.audios._eventId, 'option': 'audio', 'state': true, 'arg': file}, function(data){
					var li = window.opener.$('ul.events li#event'+admin.audios._eventId);
					li.addClass('audio');
					li.children('.option.audio').attr('title', file);
					window.close();
				});
			});
		}
	}
};


$(document).ready(function(){
	admin.events.init();
	admin.audios.init();
});