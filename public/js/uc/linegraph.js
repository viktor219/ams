var doughnutData = [
	{
		value: 30,
		color: "#455C73"
	},
	{
		value: 30,
		color: "#9B59B6"
	},
	{
		value: 60,
		color: "#BDC3C7"
	},
	{
		value: 100,
		color: "#26B99A"
	},
	{
		value: 120,
		color: "#3498DB"
	}
];
var myDoughnut = new Chart(document.getElementById("canvas1").getContext("2d")).Doughnut(doughnutData);