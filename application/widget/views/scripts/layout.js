var Denkmal = {
	html: '<?php echo $this->html ?>',

	loadJs: function(file, callback) {
		callback = callback || function() {
		};
		var script = document.createElement('script');
		script.type = 'text/javascript';

		if (script.readyState) {
			script.onreadystatechange = function() {
				if (script.readyState == 'loaded' || script.readyState == 'complete') {
					script.onreadystatechange = null;
					callback();
				}
			};
		} else {
			script.onload = callback;
		}

		script.src = file;
		document.getElementsByTagName('head')[0].appendChild(script);
	},
	loadCss: function(file, browserContains) {
		if (browserContains && navigator.appVersion.indexOf(browserContains) <= 0) {
			return;
		}
		var link = document.createElement('link');
		link.rel = 'stylesheet';
		link.type = 'text/css';
		link.href = file;
		document.getElementsByTagName('head')[0].appendChild(link);
	},
	init: function() {
		Denkmal.widget.setBaseUrl('http://<?php echo $this->domain; ?>');
		Denkmal.widget.addFilesData(<?php echo $this->jsonData($this->audiosData); ?>);
		Denkmal.widget.setHtml(<?php echo json_encode($this->layout()->content) ?>);
		Denkmal.widget.init();
		}
		};

		Denkmal.loadCss('http://<?php echo $this->domain ?>/css/widget.css');

		Denkmal.loadJs('http://<?php echo $this->domain ?>/js/jquery.js', function() {
			Denkmal.loadJs('http://<?php echo $this->domain ?>/js/jquery.tooltip.js', function() {
				Denkmal.loadJs('http://<?php echo $this->domain ?>/js/widget.js', function() {
					jQuery.noConflict();
					Denkmal.init();
				});
		});
		});

		document.write('
			<div id="denkmal_widget"></div>
		');
