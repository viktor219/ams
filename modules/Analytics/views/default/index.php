<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Customer;
use app\models\Location;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Analytics';
$this->params['breadcrumbs'][] = $this->title;
?>
    <script>
        var randomScalingFactor = function () {
            return Math.round(Math.random() * 100)
        };

        var sharePiePolorDoughnutData = [
            {
                value: 120,
                color: "#455C73",
                highlight: "#34495E",
                label: "Dark Grey"
        },
            {
                value: 50,
                color: "#9B59B6",
                highlight: "#B370CF",
                label: "Purple Color"
        },
            {
                value: 150,
                color: "#BDC3C7",
                highlight: "#CFD4D8",
                label: "Gray Color"
        },
            {
                value: 180,
                color: "#26B99A",
                highlight: "#36CAAB",
                label: "Green Color"
        },
            {
                value: 100,
                color: "#3498DB",
                highlight: "#49A9EA",
                label: "Blue Color"
        }

    ];

        $(document).ready(function () {
            window.myDoughnut = new Chart(document.getElementById("canvas_doughnut").getContext("2d")).Doughnut(sharePiePolorDoughnutData, {
                responsive: true,
                tooltipFillColor: "rgba(51, 51, 51, 0.55)"
            });
        });
        var lineChartData = {
                labels: ["week 1", "week 2", "week 3", "week 4"],
                datasets: [
                    {
                        label: "My First dataset",
                        fillColor: "rgba(38, 185, 154, 0.21)", //rgba(220,220,220,0.2)
                        strokeColor: "rgba(38, 185, 154, 0.7)", //rgba(220,220,220,1)
                        pointColor: "rgba(38, 185, 154, 0.7)", //rgba(220,220,220,1)
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: [31, 74, 6, 39]
                },
                    {
                        label: "My Second dataset",
                        fillColor: "rgba(3, 88, 106, 0.2)", //rgba(151,187,205,0.2)
                        strokeColor: "rgba(3, 88, 106, 0.70)", //rgba(151,187,205,1)
                        pointColor: "rgba(3, 88, 106, 0.70)", //rgba(151,187,205,1)
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(151,187,205,1)",
                        data: [82, 23, 66, 9]
                }
            ]

            }

            $(document).ready(function () {
                new Chart(document.getElementById("canvas000").getContext("2d")).Line(lineChartData, {
                    responsive: true,
                    tooltipFillColor: "rgba(51, 51, 51, 0.55)"
                });
                //
                new Chart(document.getElementById("canvas001").getContext("2d")).Line(lineChartData, {
                    responsive: true,
                    tooltipFillColor: "rgba(51, 51, 51, 0.55)"
                });          
            });
            $(document).ready(function () {
                window.myPolarArea = new Chart(document.getElementById("canvas_area").getContext("2d")).PolarArea(sharePiePolorDoughnutData, {
                    responsive: true,
                    tooltipFillColor: "rgba(51, 51, 51, 0.55)"
                });
            }); 
            //
            $(document).ready(function () {
                window.myPolarArea = new Chart(document.getElementById("canvas_area1").getContext("2d")).PolarArea(sharePiePolorDoughnutData, {
                    responsive: true,
                    tooltipFillColor: "rgba(51, 51, 51, 0.55)"
                });
            }); 
            var barChartData = {
                    labels: ["week 1", "week 2", "week 3", "week 4"],
                    datasets: [
                        {
                            fillColor: "#26B99A", //rgba(220,220,220,0.5)
                            strokeColor: "#26B99A", //rgba(220,220,220,0.8)
                            highlightFill: "#36CAAB", //rgba(220,220,220,0.75)
                            highlightStroke: "#36CAAB", //rgba(220,220,220,1)
                            data: [51, 30, 40, 28]
                    },
                        {
                            fillColor: "#03586A", //rgba(151,187,205,0.5)
                            strokeColor: "#03586A", //rgba(151,187,205,0.8)
                            highlightFill: "#066477", //rgba(151,187,205,0.75)
                            highlightStroke: "#066477", //rgba(151,187,205,1)
                            data: [41, 56, 25, 48]
                    }
                ],
                }

                $(document).ready(function () {
                    new Chart($("#canvas_bar").get(0).getContext("2d")).Bar(barChartData, {
                        tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                        responsive: true,
                        barDatasetSpacing: 6,
                        barValueSpacing: 5
                    });
                });
            
            var barChart = {
                    labels: ["IBM 4610-2CR", "Epson U220",  "Toshiba 4900-783", "Fujitsu Cash Drawer", "IBM 4820-5LG"],
                    datasets: [
                        {
                            fillColor: "#26B99A", //rgba(220,220,220,0.5)
                            strokeColor: "#26B99A", //rgba(220,220,220,0.8)
                            highlightFill: "#36CAAB", //rgba(220,220,220,0.75)
                            highlightStroke: "#36CAAB", //rgba(220,220,220,1)
                            data: [51, 30, 40, 28, 92]
                    },
                        {
                            fillColor: "#03586A", //rgba(151,187,205,0.5)
                            strokeColor: "#03586A", //rgba(151,187,205,0.8)
                            highlightFill: "#066477", //rgba(151,187,205,0.75)
                            highlightStroke: "#066477", //rgba(151,187,205,1)
                            data: [41, 56, 25, 48, 72]
                    }
                ],
                }            
            $(document).ready(function () {
                new Chart($("#canvas_bar1").get(0).getContext("2d")).Bar(barChart, {
                    tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                    responsive: true,
                    barDatasetSpacing: 6,
                    barValueSpacing: 5
                });
            });                   
    </script>
<div class="Analytics-default-index">
	<div class="row row-margin">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row vertical-align">
					<div class="col-md-9 vcenter">
						<h4>
							<span class="glyphicon glyphicon-equalizer"></span>
							Pricing Reports
						</h4>
					</div>
					<div class="col-md-3 vcenter text-right">
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-export"></span>Export', '#', ['class' => 'btn btn-success']) ?>
						</div>
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-stats"></span> Reports', '#', ['class' => 'btn btn-success']) ?>
						</div>
					</div>
				</div>		
			</div>
			<div class="panel-body">
	                        <div class="col-md-4 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Cost Of Goods</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas_doughnut"></canvas>
	                                </div>
	                            </div>
	                        </div>	
	                        <div class="col-md-4 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Monthly Purchases</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas_bar"></canvas>
	                                </div>
	                            </div>
	                        </div>		
	                        <div class="col-md-4 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Top Items</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas_bar1"></canvas>
	                                </div>
	                            </div>
	                        </div>		                                       
			</div>
	    </div>
    </div>
	<div class="row row-margin">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row vertical-align">
					<div class="col-md-9 vcenter">
						<h4>
							<span class="glyphicon glyphicon-equalizer"></span>
							Inventory Reports
						</h4>
					</div>
					<div class="col-md-3 vcenter text-right">
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-export"></span>Export', '#', ['class' => 'btn btn-success']) ?>
						</div>
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-stats"></span> Reports', '#', ['class' => 'btn btn-success']) ?>
						</div>
					</div>
				</div>		
			</div>
			<div class="panel-body">
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Monthly Inventory</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas000"></canvas>
	                                </div>
	                            </div>
	                        </div>	
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Monthly Shipping</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas001"></canvas>
	                                </div>
	                            </div>
	                        </div>		                
			</div>
	    </div>
    </div>
	<div class="row row-margin">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row vertical-align">
					<div class="col-md-9 vcenter">
						<h4>
							<span class="glyphicon glyphicon-equalizer"></span>
							Gross Profit Reports
						</h4>
					</div>
					<div class="col-md-3 vcenter text-right">
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-export"></span>Export', '#', ['class' => 'btn btn-success']) ?>
						</div>
						<div class="col-md-6">
							<?= Html::a('<span class="glyphicon glyphicon-stats"></span> Reports', '#', ['class' => 'btn btn-success']) ?>
						</div>
					</div>
				</div>		
			</div>
			<div class="panel-body">
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Job Profitability</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas_area"></canvas>
	                                </div>
	                            </div>
	                        </div>	
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                            <div class="x_panel">
	                                <div class="x_title">
	                                    <h2>Customer Profitability</h2>
	                                    <ul class="nav navbar-right panel_toolbox">
	                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                                        </li>
	                                        <li class="dropdown">
	                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
	                                            <ul class="dropdown-menu" role="menu">
	                                                <li><a href="#">Settings 1</a>
	                                                </li>
	                                                <li><a href="#">Settings 2</a>
	                                                </li>
	                                            </ul>
	                                        </li>
	                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
	                                        </li>
	                                    </ul>
	                                    <div class="clearfix"></div>
	                                </div>
	                                <div class="x_content">
	                                    <canvas id="canvas_area1"></canvas>
	                                </div>
	                            </div>
	                        </div>		                
			</div>
	    </div>
    </div>
</div>