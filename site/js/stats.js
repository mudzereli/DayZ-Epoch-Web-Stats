function daily_stats(selector,days,legend,duration) {
	d3.json("widgets/daily_stats_json.php?days=" + days, function (data) {
		var svg = dimple.newSvg(selector, $(selector).width(), $(selector).width() * 0.6);
		var chart = new dimple.chart(svg, data);
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
		if (legend) { chart.addLegend("80%",0, "30%", "30%", "left"); };
		chart.assignColor("Characters Created","#33b5e5","#33b5e5",1);
		chart.assignColor("Characters Killed","#ff4444","#ff4444",1);
		chart.assignColor("Structures Built","#aa66cc","#aa66cc",1);
		chart.assignColor("Vehicles Spawned","#ffbb33","#ffbb33",1);
		chart.assignColor("New Players","#99cc00","#99cc00",1);
		chart.assignColor("Inactive Players","#cc0000","#cc0000",1);
		chart.draw(duration);
	});
}

function object_stats(selector,cat,percent,legend,duration) {
	d3.json("widgets/object_stats_json.php?cat=" + cat, function (data) {
		var svg = dimple.newSvg(selector, $(selector).width(), $(selector).width() * 0.6);
		var chart = new dimple.chart(svg, data);
		chart.addCategoryAxis("x", "CATEGORY", "%Y-%m-%d", "%d");
		if (percent) {
			chart.addPctAxis("y", "COUNT");
		} else {
			chart.addMeasureAxis("y", "COUNT");
		}
		chart.addSeries("CLASSNAME", dimple.plot.bar);
		if (legend) { chart.addLegend("85%","80%", "30%", "30%", "left"); };
		chart.draw(duration);
	});
}

function survivor_stats(selector,percent,legend,duration) {
	d3.json("widgets/survivor_stats.php", function (data) {
		var svg = dimple.newSvg(selector, $(selector).width(), $(selector).width() * 0.3);
		var chart = new dimple.chart(svg, data);
		if (percent) {
			chart.addPctAxis("x", "COUNT");
			chart.addCategoryAxis("y", "Life Style");
		} else {
			chart.addMeasureAxis("x", "COUNT");
			chart.addCategoryAxis("y", "LIFESTYLE");
		}
		chart.addSeries("LIFESTYLE", dimple.plot.bar);
		if (legend) { chart.addLegend("85%","80%", "30%", "30%", "left"); };
		chart.assignColor("Survivor","#edffb8","#669900",1);
		chart.assignColor("Hero","#d0eef9","#0099cc",1);
		chart.assignColor("Bandit","#ffcdcd","#cc0000",1);
		chart.draw(duration);
	});
}

function death_stats(selector,percent,legend,duration) {
	d3.json("widgets/death_stats.php", function (data) {
		var svg = dimple.newSvg(selector, $(selector).width(), $(selector).width() * 0.3);
		var chart = new dimple.chart(svg, data);
		if (percent) {
			chart.addPctAxis("x", "COUNT");
			chart.addCategoryAxis("y", "Death Type");
		} else {
			chart.addMeasureAxis("x", "COUNT");
			chart.addCategoryAxis("y", "DEATH_TYPE");
		}
		chart.addSeries("DEATH_TYPE", dimple.plot.bar);
		if (legend) { chart.addLegend("85%","80%", "30%", "30%", "left"); };
		chart.assignColor("Zombie","#ffefd0","#ff8800",1);
		chart.assignColor("Survivor","#edffb8","#669900",1);
		chart.assignColor("Hero","#d0eef9","#0099cc",1);
		chart.assignColor("Bandit","#ffcdcd","#cc0000",1);
		chart.draw(duration);
	});
}