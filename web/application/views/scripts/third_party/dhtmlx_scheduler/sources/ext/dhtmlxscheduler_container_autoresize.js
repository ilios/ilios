/*
This software is allowed to use under GPL or you need to obtain Commercial or Enterise License
to use it in non-GPL project. Please contact sales@dhtmlx.com for details
*/
(function() {

	scheduler.config.container_autoresize = true;
	scheduler.config.month_day_min_height = 90;

	var old_pre_render_event = scheduler._pre_render_events;

	scheduler._pre_render_events = function(evs, hold) {
		if (!scheduler.config.container_autoresize) {
			return old_pre_render_event.apply(this, arguments);
		}

		var hb = this.xy.bar_height;
		var h_old = this._colsS.heights;
		var h = this._colsS.heights = [0, 0, 0, 0, 0, 0, 0];
		var data = this._els["dhx_cal_data"][0];

		if (!this._table_view)
			evs = this._pre_render_events_line(evs, hold); //ignore long events for now
		else
			evs = this._pre_render_events_table(evs, hold);

		if (this._table_view) {
			if (hold)
				this._colsS.heights = h_old; else {
				var evl = data.firstChild;
				if (evl.rows) {
					for (var i = 0; i < evl.rows.length; i++) {
						h[i]++;
						if ((h[i]) * hb > this._colsS.height - 22) { // 22 - height of cell's header
							//we have overflow, update heights
							var cells = evl.rows[i].cells;
							for (var j = 0; j < cells.length; j++) {
								cells[j].childNodes[1].style.height = h[i] * hb + "px";
							}
							h[i] = (h[i - 1] || 0) + cells[0].offsetHeight;
						}
						h[i] = (h[i - 1] || 0) + evl.rows[i].cells[0].offsetHeight;
					}
					h.unshift(0);
					if (evl.parentNode.offsetHeight < evl.parentNode.scrollHeight && !evl._h_fix) {
						//we have v-scroll, decrease last day cell

						// NO CHECK SHOULD BE MADE ON VERTICAL SCROLL
					}
				} else {
					if (!evs.length && this._els["dhx_multi_day"][0].style.visibility == "visible")
						h[0] = -1;
					if (evs.length || h[0] == -1) {
						//shift days to have space for multiday events
						var childs = evl.parentNode.childNodes;
						var dh = ((h[0] + 1) * hb + 1) + "px"; // +1 so multiday events would have 2px from top and 2px from bottom by default
						data.style.top = (this._els["dhx_cal_navline"][0].offsetHeight + this._els["dhx_cal_header"][0].offsetHeight + parseInt(dh, 10)) + 'px';
						data.style.height = (this._obj.offsetHeight - parseInt(data.style.top, 10) - (this.xy.margin_top || 0)) + 'px';
						var last = this._els["dhx_multi_day"][0];
						last.style.height = dh;
						last.style.visibility = (h[0] == -1 ? "hidden" : "visible");
						last = this._els["dhx_multi_day"][1];
						last.style.height = dh;
						last.style.visibility = (h[0] == -1 ? "hidden" : "visible");
						last.className = h[0] ? "dhx_multi_day_icon" : "dhx_multi_day_icon_small";
						this._dy_shift = (h[0] + 1) * hb;
						h[0] = 0;
					}
				}
			}
		}

		return evs;
	};

	var checked_divs = ["dhx_cal_navline", "dhx_cal_header", "dhx_multi_day", "dhx_cal_data"];
	var updateContainterHeight = function(is_repaint) {
		var total_height = 0;
		for (var i = 0; i < checked_divs.length; i++) {

			var className = checked_divs[i];
			var checked_div = (scheduler._els[className]) ? scheduler._els[className][0] : null;
			var height = 0;
			switch (className) {
				case "dhx_cal_navline":
				case "dhx_cal_header":
					height = parseInt(checked_div.style.height, 10);
					break;
				case "dhx_multi_day":
					height = (checked_div) ? checked_div.offsetHeight : 0;
					if (height == 1)
						height = 0;
					break;
				case "dhx_cal_data":
					height = Math.max(checked_div.offsetHeight - 1, checked_div.scrollHeight);
					var mode = scheduler.getState().mode;
					if (mode == "month") {
						if (scheduler.config.month_day_min_height && !is_repaint) {
							var rows_length = checked_div.getElementsByTagName("tr").length;
							height = rows_length * scheduler.config.month_day_min_height;
						}
						if (is_repaint) {
							checked_div.style.height = height + "px";
						}
					}
					if (scheduler.matrix && scheduler.matrix[mode]) {
						if (is_repaint) {
							height += 2;
							checked_div.style.height = height + "px";
						} else {
							height = (scheduler.matrix[mode].y_unit.length * scheduler.matrix[mode].dy)+2;
						}
					}
					if (mode == "day" || mode == "week") {
						height += 2;
					}
					break;
			}
			total_height += height;
		}
		scheduler._obj.style.height = (total_height) + "px";

		if (!is_repaint)
			scheduler.updateView();
	};

	var conditionalUpdateContainerHeight = function() {
		var mode = scheduler.getState().mode;

		updateContainterHeight();
		if ( (scheduler.matrix && scheduler.matrix[mode]) || mode == "month" ) {
			window.setTimeout(function() {
				updateContainterHeight(true);
			}, 1);
		}
	};

	scheduler.attachEvent("onViewChange", conditionalUpdateContainerHeight);
	scheduler.attachEvent("onXLE", conditionalUpdateContainerHeight);
	scheduler.attachEvent("onEventChanged", conditionalUpdateContainerHeight);
	scheduler.attachEvent("onAfterSchedulerResize", conditionalUpdateContainerHeight);

})();