$(function () {
	$('#world-map-gdp').vectorMap({
		map: 'world_mill_en',
		backgroundColor: 'transparent',
		zoomOnScroll: false,
		series: {
			regions: [{
				values: gdpData,
				scale: ['#E6F2F0', '#149B7E'],
				normalizeFunction: 'polynomial'
			}]
		},
		onRegionTipShow: function (e, el, code) {
			el.html(el.html() + ' (GDP - ' + gdpData[code] + ')');
		}
	});
});