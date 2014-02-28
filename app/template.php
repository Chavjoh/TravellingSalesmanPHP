<?php
/**
 * Template to show result
 *
 * @package TravellingSalesmanPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license CC BY-SA 3.0 Unported
 */

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	
	<title>Travelling Salesman PHP</title>
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Main design -->
	<link href="style/jumbotron-narrow.css" rel="stylesheet">
	
	<!-- Bootstrap -->
	<link href="plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<!-- PaperJS -->
	<script type="text/javascript" src="plugin/paperjs/paper.js"></script>
	<script type="text/paperscript" canvas="canvas1">
		function clear()
		{
			var l = project.activeLayer.children.length;
			
			for (var i=0; i < l; i++) {
				project.activeLayer.children[0].remove();
			}
		}
		
		var cityList = { 
			<?php
			foreach (CityManager::getInstance()->getCityList() as $city)
			{
				echo '"'.$city->getName().'": new Point('.$city->getX().', '.$city->getY().'),';
			}
			?>
		};
		
		var solutionList = [
			<?php
			foreach ($bestSolutionList as $solution)
			{
				echo '[';
				foreach ($solution->getCityList() AS $city)
				{
					echo '"'.$city->getName().'",';
				}
				echo '],';
			}
			?>
		];
		
		var interval = setInterval(function(){next()}, 1000);
		
		var solutionIndex = 0;
		
		function next()
		{
			clear();
			
			var text = new PointText(new Point(10, 20));
			text.fillColor = 'black';
			text.fontSize = '1.3em';
			if (solutionIndex == solutionList.length - 1) {
				text.content = 'Best solution';
			}
			else {
				text.content = 'Solution of generation ' + (solutionIndex + 1);
			}
			
			
			for (cityName in cityList) {
				var path = new Path.Circle(cityList[cityName], 2);
				path.fillColor = 'black';
			}
			
			currentSolution = solutionList[solutionIndex];
			lastCityName = currentSolution[currentSolution.length - 1];
			
			for (indexCity in currentSolution) {
				drawConnexion(lastCityName, currentSolution[indexCity]);
				lastCityName = currentSolution[indexCity];
			}
			
			solutionIndex = (solutionIndex + 1) % solutionList.length;
			
			paper.view.draw();
		}
		
		function drawConnexion(from, to)
		{
			var from = cityList[from];
			var to = cityList[to];
			var path = new Path.Line(from, to);
			path.strokeColor = 'blue';
		}
	</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<h3 class="text-muted">Travelling Salesman PHP</h3>
		</div>

		<div class="jumbotron">
			<h1>Genetic Algorithm</h1>
			<p class="lead">
				Genetic algorithm is a search heuristic that mimics the process of natural selection.
				In the travelling salesman problem, we use GA to find the shortest possible route that visits each city exactly once and returns to the origin city.
			</p>
		</div>

		<div class="row marketing">
			<div class="col-lg-6">
				<h4>Population</h4>
				<p>The currently base population is set to <?=(GA_POPULATION )?>.</p>

				<h4>Selection</h4>
				<p>The rate of selection is currently set to <?=(GA_SELECTION * 100)?>%.</p>
			</div>
			
			<div class="col-lg-6">
				<h4>Mutation</h4>
				<p>The rate of change is currently set to <?=(GA_MUTATION * 100)?>%.</p>

				<h4>Crossover</h4>
				<p>The rate of crossover is currently set to <?=(GA_CROSSOVER * 100)?>%.</p>
			</div>
		</div>
		
		<div class="row text-center">
			<canvas id="canvas1" width="500" height="500"></canvas>
		</div>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Generation</th>
					<th>Path</th>
					<th>Distance</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($bestSolutionList AS $generation => $solution)
				{
				?>	<tr>
						<td class="text-center"><?=$generation?> <br /> (<?=$executionTime[$generation]?> sec)</td>
						<td>
							<?php
							foreach ($solution->getCityList() AS $city)
							{
								echo ' <span class="glyphicon glyphicon-arrow-right arrow-grey"></span> '.$city->getName();
							}
							?>
						</td>
						<td class="text-center"><?=$solution->getDistance()?></td>
					
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>

		<div class="footer">
			<p>By <a href="http://www.chavjoh.ch">Johan Chavaillaz</a>, under CC BY-SA 3.0 Unported license.</p>
		</div>

	</div>
</body>
</html>