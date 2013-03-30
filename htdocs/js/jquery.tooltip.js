/*
Made by web ninja Jonas Arnklint. Use it, modify it and...
An MIT license is distributed with this plugins original source.
Originally extracted from the easy to use CMS Venio at http://venio.se.
Modified for http://www.denkmal.org.
*/
(function($) {
  tooltipCounter = 1,
  
  $.fn.tooltip = function(options){
	  var opts = $.extend({}, $.fn.tooltip.defaults, options);
	  
	  return this.each(function(){
		  var me = $(this);
		  me.hover(function(e){
			me.addClass('tooltip-above');
	  	  	$(opts.container).append("<p id='tooltip' class='tooltip "+opts.classes+"'>"+opts.title+"</p>");
	  	  	var position = me.offset();
	  	  	if (opts.container != 'body' && me.css('position') == 'absolute') {
	  	  		position = me.position();
	  	  	}
	  	  	if (opts.track) {
	  	  		position.left=e.pageX; position.top=e.pageY;
	  	  	}
	  	  	$("#tooltip")
	  	  		.css("top",(position.top - opts.y) + "px")
	  	  		.css("left",(position.left + opts.x) + "px")
	  	  		.fadeIn(opts.fade);
	      },function(){
	    	  $("#tooltip").remove();
	    	  me.removeClass('tooltip-above');
	      });	   
	  	  me.mousemove(function(e){
	  		if (opts.track) {
	  			$("#tooltip")
	  				.css("top",(e.pageY - opts.y) + "px")
	  	  			.css("left",(e.pageX + opts.x) + "px");
	  		}
	  	  });
	  });
  }

  $.fn.tooltipAdd = function(options){
	  var me = $(this);
	  var opts = $.extend({}, $.fn.tooltip.defaults, options);
	  if (opts.group) {
		  $('.tooltip_group_'+opts.group).remove();
		  opts.classes += ' tooltip_group_'+opts.group;
	  }
	  me.addClass('tooltip-above');
	  var tooltip = $("<p class='tooltip "+opts.classes+"' id='tooltip"+tooltipCounter+"'>"+opts.title+"</p>");
	  me.data('tooltipNum', tooltipCounter++);
	  $(opts.container).append(tooltip);
	  var position = me.offset();
	  if (opts.container != 'body' && me.css('position') == 'absolute') {
		  position = me.position();
	  }
	  tooltip
	  	.css("top",position.top-opts.y + "px")
	  	.css("left",position.left+opts.x + "px")
	  	.fadeIn(opts.fade);
  }
  
  $.fn.tooltipRemove = function(){
	  var me = $(this);
	  me.removeClass('tooltip-above');
	  var tooltipNum = me.data('tooltipNum');
	  if (tooltipNum) {
		  $("#tooltip"+tooltipNum).remove();
	  }
  }
  
  $.fn.tooltipRemoveGroup = function(group){
	  $(this).find('.tooltip_group_'+group).remove();
  }
  
  $.fn.tooltip.defaults = {
    title: null,
    container: 'body',
    x: -6,
    y: 5,
    fade: 100,
    track: false,
    classes: null,
    group: null
  }
})(jQuery);
