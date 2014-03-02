<script type="text/javascript">
	d3.json("widgets/json.php", function (data) {
		var svg = dimple.newSvg("#div-daily-stats", $("#div-daily-stats").width(), $("#div-daily-stats").width() * 0.6);
		var chart = new dimple.chart(svg, data);
		chart.defaultColors = [
			new dimple.color("#ff4444"),
			new dimple.color("#33b5e5"),
			new dimple.color("#99cc00"),
			new dimple.color("#aa66cc"),
			new dimple.color("#ffbb33")
		]; 
		chart.addTimeAxis("x", "DATE", "%Y-%m-%d", "%m/%d");
		chart.addMeasureAxis("y", "COUNT");
		chart.addSeries("METRIC", dimple.plot.line);
		chart.addSeries("METRIC", dimple.plot.bubble);
		chart.addLegend("60%", "10%", "30%", "30%", "right");
		chart.assignColor("Characters Created","#33b5e5","#33b5e5",1);
		chart.assignColor("Characters Killed","#ff4444","#ff4444",1);
		chart.assignColor("Structures Built","#aa66cc","#aa66cc",1);
		chart.assignColor("Vehicles Spawned","#ffbb33","#ffbb33",1);
		chart.assignColor("New Players","#99cc00","#99cc00",1);
		chart.assignColor("Inactive Players","#cc0000","#cc0000",1);
		chart.draw();
	});
</script>