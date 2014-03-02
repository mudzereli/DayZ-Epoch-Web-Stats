function daily_stats(selector,days,legend,duration) {
	d3.json("widgets/daily_stats_json.php?days=" + days, function (data) {
		var svg = dimple.newSvg(selector, $(selector).width(), $(selector).width() * 0.6);
		var chart = new dimple.chart(svg, data);
		chart.defaultColors = [
			new dimple.color("#ff4444"),
			new dimple.color("#33b5e5"),
			new dimple.color("#99cc00"),
			new dimple.color("#aa66cc"),
			new dimple.color("#ffbb33")
		];
		if (days <= 7) {
			chart.addTimeAxis("x", "DATE", "%Y-%m-%d", "%d");
		} else if (days <= 30) {
			chart.addTimeAxis("x", "DATE", "%Y-%m-%d", "%m/%d");
		} else {
			chart.addTimeAxis("x", "DATE", "%Y-%m-%d", "%m/%d/%Y");
		}
		chart.addMeasureAxis("y", "VALUE");
		chart.addSeries("METRIC", dimple.plot.line);
		chart.addSeries("METRIC", dimple.plot.bubble);
		if (legend) { chart.addLegend("60%", "10%", "30%", "30%", "right"); };
		chart.assignColor("Characters Created","#33b5e5","#33b5e5",1);
		chart.assignColor("Characters Killed","#ff4444","#ff4444",1);
		chart.assignColor("Structures Built","#aa66cc","#aa66cc",1);
		chart.assignColor("Vehicles Spawned","#ffbb33","#ffbb33",1);
		chart.assignColor("New Players","#99cc00","#99cc00",1);
		chart.assignColor("Inactive Players","#cc0000","#cc0000",1);
		chart.draw(duration);
	});
}