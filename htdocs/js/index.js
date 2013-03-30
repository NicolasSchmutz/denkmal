var map = {
	locations: {},
	events: {},
	showall: false,
	
	init: function() {
		$.each(this.locations, function(id, location) {
			var pin;
			if (location.url) {
				pin = $('<a class="pin" id="pin'+id+'" href="'+location.url+'" target="_blank"><img /></a>');
			} else {
				pin = $('<div class="pin" id="pin'+id+'"><img /></div>');
			}
			pin.css({'left':(location.x-4),'top':(location.y-4)});
			if (location.show) { pin.addClass('show'); }
			$('#map').append(pin);
			
			pin.tooltip({title: location.name, container: '#map', classes: 'tooltip_location'});
			pin.hover(function() {
				$.each(events.getEventsByLocation(id), function(id,event) {
					$('.events li#event'+id+'.hasMap').addClass('active');
				});
			}, function() {
				$.each(events.getEventsByLocation(id), function(id,event) {
					$('.events li#event'+id+'.hasMap').removeClass('active');
				});
			});
		});
		$('#showall').click(function() {
			$(this).blur();
			map.showall = !map.showall;
			if (map.showall) {
				$(this).text('Weniger');
			} else {
				$(this).text('Mehr');
			}
			map.render();
		});
	},
	render: function() {
		if (this.showall) {
			$('#map .pin img').attr('src', map.getPinImg(false, true));
			$('#map .pin').show();
		} else {
			$('#map .pin:not(.show)').hide();
			$('#map .pin.show img').attr('src', map.getPinImg(false, true));
		}
		$('#map .pin:visible').css('z-index','2');
		$.each(map.events, function(id, event) {
			var zindex=3; if (event.star) { zindex=4; }
			$('#map #pin'+event.locationId).show().css('z-index',zindex).find('img').attr('src', map.getPinImg(event.star));
		});
	},
	getPinImg: function(star, off) {
		if (star) { return '/pics/dot_star_pin.png'; }
		if (off) { return '/pics/dot_off_pin.png'; }
		return '/pics/dot_pin.png';
	}
};

var events = {
	day: '',
	_timer: null,
	_requestDay: null,
	
	load: function(day) {
		this._requestDay = day;
		ui.search.end();
		clearTimeout(events._timer);
		events._timer = setTimeout(function() {
				$('#events').html('<div class="notice"><img src="/pics/wait_ffffff.gif" /> Lade...</div>');
				map.events = {};
				map.render();
			}, 500);
		$.getJSON('/'+day, function(data){
			if (data.weekday != events._requestDay) {
				return;
			}
			clearTimeout(events._timer);
			map.events = data.events;
			$('#days li.active').removeClass('active');
			$('#days li.w'+data.weekday).addClass('active');
			$('#date_str').text(data.datestr);
			$('#events').html(data.eventsHtml);
			$('#map').tooltipRemoveGroup('location');
			events.activate($('#events ul.events'));
			audioplayer.addFilesData(data.audios);
			audioplayer.activateIcons();
			map.render();
			if (typeof(_gaq) != 'undefined') {
				_gaq.push(['_trackPageview', '/'+data.weekday]);
			}
		});
	},
	activate: function(eventslist) {
		eventslist.find('li').hover(function() {
			$(this).addClass('active');
			if ($(this).hasClass('hasMap')) {
				locationId = map.events[$(this).attr('id').substring(5)].locationId;
				var location = map.locations[locationId];
				if (location) {
					$('#map #pin'+locationId).tooltipAdd({title: location.name, fade: 0, container: '#map', classes: 'tooltip_location', group: 'location'});
				}
			}
		},function() {
			$(this).removeClass('active');
			if ($(this).hasClass('hasMap')) {
				locationId = map.events[$(this).attr('id').substring(5)].locationId;
				$('#map #pin'+locationId).tooltipRemove();
			}
		});
	},
	getEventsByLocation: function(locationId) {
		var events={};
		$.each(map.events, function(id, event) {
			if (event.locationId == locationId) { events[id] = event; }
		});
		return events;
	}
};

var audioplayer = {
	files: {},
	ready: false,
	playing: false,
	currFile: null, currHash: null,
	ad: true,
	autoplayHash: null,
	
	init: function() {
		var flashvars = {
			'onLoad': 'audioplayer._onLoad()'
		};
		var params = {
			'allowscriptaccess': 'true',
			'wmode': 'transparent'
		};
		var attributes = {
			'id': 'player',
			'name': 'player'
		};
		swfobject.embedSWF('/swf/player.swf', 'player', '154', '33', '9.0.0', false, flashvars, params, attributes);
	},
	registerEvents: function() {
		if (niftyplayer('player')) {
			niftyplayer('player').registerEvent('onBufferingStarted', 'audioplayer._onPlay()');
			niftyplayer('player').registerEvent('onPlay', 'audioplayer._onPlay()');
			niftyplayer('player').registerEvent('onPause', 'audioplayer._onStop()');
			niftyplayer('player').registerEvent('onStop', 'audioplayer._onStop()');
			niftyplayer('player').registerEvent('onSongOver', 'audioplayer._onStop()');
			niftyplayer('player').registerEvent('onError', 'audioplayer._onStop()');
			this.ready = true;
		}
	},
	addFilesData: function(files) {
		$.each(files, function(hash, file) {
			audioplayer.files[hash]=file;
		});
	},
	activateIcons: function() {
		$('.eventicon:not(.star,.audio)').css('background-image', 'url(/pics/dot.png)');
		$('.eventicon.star:not(.audio)').css('background-image', 'url(/pics/dot_star.png)');
		$('.eventicon.audio:not(.star)').css('background-image', 'url(/pics/dot_audio.png)');
		$('.eventicon.star.audio').css('background-image', 'url(/pics/dot_star_audio.png)');
		$('.eventicon.audio').unbind();
		$('.eventicon.audio').css('cursor', 'pointer');
		
		$.each(this.files, function(hash, file) {
			var audiotitle = file.substring(0, file.length-4);
			$('.eventicon.audio'+hash).click(function() {
				audioplayer.toggle(hash, file);
				$(this).blur();
			});
			$('.eventicon.audio'+hash+':not(.star.notooltip)').tooltip({title: audiotitle, classes: 'tooltip_audio', x:10, y:15, track:true});
			$('.eventicon.star.audio'+hash+':not(.notooltip)').tooltip({title: audiotitle, classes: 'tooltip_audio tooltip_star', x:10, y:15, track:true});
		});
		if (this.playing) {
			this._onPlay();
		}
	},
	_resetIcons: function(excludePlaying) {
		if (excludePlaying) {
			$('.eventicon.audio:not(.audio'+this.currHash+')').css('background-position', '0 0');
		} else {
			$('.eventicon.audio').css('background-position', '0 0');
		}
	},
	toggle: function(hash, file) {
		if (!file) {
			if (this.files[hash]) {
				file = this.files[hash];
			} else {
				return;
			}
		}
		ui.showPlayer();
		if (!this.ready) {
			return;
		}
		if (file == this.currFile) {
			niftyplayer('player').playToggle();
		} else {
			this._load(file);
			this.currFile = file; this.currHash = hash;
			this._onPlay();
		} 
	},
	_load: function(file) {
		niftyplayer('player').loadAndPlay('/audio/'+file);
	},
	_onLoad: function() {
		this.registerEvents();
		if (this.autoplayHash) {
			this.toggle(this.autoplayHash);
		}
	},
	_onPlay: function() {
		this._resetIcons(true);
		$('.eventicon.audio'+this.currHash).css('background-position', '14px 0');
		this.playing = true;
	},
	_onStop: function() {
		this._resetIcons();
		this.playing = false;
	}
};

var ui = {
	_playerHidden: true,
	
	showPlayer: function() {
		if (this._playerHidden) {
			$('#box3_ad').fadeOut(700);
			this._playerHidden = false;
		}
	},
	init: function() {
		$('#navbar #box1 #add').hover(function() {
			$(this).css({'background-position': '34px 0', height: 41});
		}, function() {
			$(this).css({'background-position': '0 0', height: 33});
		});
		this.promotion.init();
		this.weblinks.init();
		this.search.init();
	},
	sendemail: function(name) {
		location.href='mailto:' + name + '@' + 'denkmal.org';
	},
	legal: function() {
		alert('Die angebotenen Songs sind Hörproben.\nVon kaufbarer Musik werden nur Previews angeboten.\nSollten Sie mit der Veröffentlichung eines Songs nicht einverstanden sein, so melden Sie das bitte an: kontakt'+'@'+'denkmal.org.\n\nAchtung: Es besteht keine Garantie, dass an einem Event die hier angebotene Musik läuft. ;)');
	},
	
	weblinks: {
		_hover: false,
		init: function() {
			$('#box4, #weblinks').hover(function() {
				ui.weblinks._hover = true;
				ui.search.end();
				ui.promotion.close();
				$('#weblinks_cont').show();
			}, function() {
				ui.weblinks._hover = false;
				setTimeout(function(){
						if (!ui.weblinks._hover) {
							$('#weblinks_cont').hide();
						}
					}, 200);
			});
		}
	},
	
	promotion: {
		_open: false,
		init: function() {
			$('#navbar #promo_entry').hover(function() {
				$(this).css({'background-position': '34px 0', height: 41});
			}, function() {
				$(this).css({'background-position': '0 0', height: 33});
			});
			$('#navbar #promo_entry').click(function() {
				$('#promotion').toggle();
				if ($('#promotion:visible')) {
					$('#promotion input:eq(0)').focus();
				}
			});
			$('#promotion form').submit(function() {
				ui.promotion.submit($(this).find('input[name="name"]').val(), $(this).find('input[name="email"]').val());
				return false;
			});
		},
		close: function() {
			$('#promotion').hide();
		},
		submit: function(name, email) {
			$('#promotion .error').hide();
			$.getJSON('/index/promotion/', { name: name, email: email }, function(data){
				if (data.success) {
					$('#promotion').html(data.msg);
					setTimeout(function() { $('#promotion').fadeOut(); }, 4000);
				} else {
					$('#promotion .error').show().html(data.msg);
				}
			});
		}
	},
	
	search: {
		_hover: false,
		_focus: false,
		_q: '',
		_timer: null,
		
		init: function() {
			$('#search').hover(function() { ui.search._boxUpdate(true, null); }, function() { ui.search._boxUpdate(false, null);	});
			$('#search input')
				.focus(function() { ui.search._boxUpdate(null, true); })
				.blur(function() { ui.search._boxUpdate(null, false); })
				.bind('keypress keydown keyup change', function() {
					setTimeout(function() { ui.search.boxChange(); }, 0);
				});
			$('#search #clear').click(function() {
				ui.search.boxChange('');
			});
		},
		_boxUpdate: function(hover, focus) {
			if (hover != null) { ui.search._hover=hover; }
			if (focus != null) { ui.search._focus=focus; }
			if (ui.search._focus || ui.search._hover || $('#search input').val() != '') {
				$('#search').css('background-position', '0 21px');
			} else {
				$('#search').css('background-position', '0 0');
			}
		},
		boxChange: function(setVal) {
			var immediateSearch = false;
			if (setVal != null) {
				$('#search input').val(setVal).focus();
				immediateSearch = true;
			}
			var val = $('#search input').val();
			
			if (val != '') { $('#search #clear').show(); }
			else { $('#search #clear').hide(); }
			
			if (val != ui.search._q) {
				ui.search._q = val;
				clearTimeout(ui.search._timer);
				if (val == '') {
					ui.search.hide();
				} else if (immediateSearch) {
 					search.search(val);
				} else {
					ui.search._timer = setTimeout(function() { search.search($('#search input').val()); }, 200);
				}
			}
		},
		showWait: function() {
			ui.search.show('<div class="notice"><img src="/pics/wait_96dd6b.gif" />Suche läuft...</div>');
		},
		show: function(html) {
			$('#search_results').show().html(html);
		},
		hide: function() {
			$('#search_results').hide();
		},
		end: function() {
			ui.search.boxChange('');
			$('#search input').blur();
		}
	}
};

var search = {
	search: function(q) {
		ui.search.showWait();
		// Use get() here instead of getJSON(), because getJSON would interpret the query "q=?" as a JSONP-request
		$.get('/index/search/', { q: q }, function(data){
			if ($('#search input').val() == '') return;
			data = eval('('+data+')');
			if ($('#search input').val() != data.q) return;
			ui.search.show(data.html);
			audioplayer.addFilesData(data.audios);
			audioplayer.activateIcons();
		});
		if (typeof(pageTracker) != 'undefined') {
			pageTracker._trackPageview('/search?q='+q);
		}
	}
};

var utils = {
	getDomain: function(url) {
		var domains = url.match(/^https?:\/\/(www\.)?([\w\._-]+)/);
		if (domains.length >= 3) {
			return domains[2];
		}
		return url;
	},
	stripHtml: function(html) {
		return $('<div>'+html+'</div>').text();
	}
}


$(document).ready(function(){
	events.activate($('#events ul.events'));
	map.init();
	map.render();
	audioplayer.init();
	audioplayer.activateIcons();
	ui.init();
});

$.address.change(function(event) {
	if (events.day != event.pathNames) {
		events.load(event.pathNames);
		events.day = event.pathNames;
	}
});