<!DOCTYPE html>
<head>
	<meta charset="utf-8"/>
	<title>Pet Listings</title>
	<style type="text/css">
		body{
			width: 1200px; /* how wide to make your web page */
			background-color: teal; /* what color to make the background */
			margin: 0 auto;
			padding: 0;
			font:12px/16px Verdana, sans-serif; /* default font */
		}
		div#main{
			background-color: #FFF;
			margin: 0;
			padding: 10px;
		}		
		h1{
			text-align: center;
		}
	</style>
</head>
<body><div id="main">
	<h1>Pet Listings</h1>
	<p>
		<a href="add-pet.html">Add a pet listing</a>
	</p>
	<?php 
	require 'database.php';

	$stmt = $mysqli->prepare("
		SELECT
		COUNT(id), species
		FROM pets
		GROUP BY species");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($number, $species); 
	echo "<h2>Pet Counts</h2><table>";
	while($stmt->fetch()){
		printf("<tr><td>%s</td> <td>%s</td></tr>\n",
			htmlspecialchars($species),
			htmlspecialchars($number)
			);
	}
	echo "</table>\n";
	$stmt->close();

	$stmt = $mysqli->prepare("SELECT species, name, weight, description, filename FROM pets");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($species, $name, $weight, $description, $picture); 
	while($stmt->fetch()){
		printf("<ul>\n\t<img src='pictures/%s'></br><li>Name: <strong>%s</strong></li><li>Species: %s</li><li>Weight: %s</li><li>Description: %s</li>\n</ul>\n",
			htmlspecialchars($picture),
			htmlspecialchars($name),
			htmlspecialchars($species),
			htmlspecialchars($weight),
			htmlspecialchars($description)
			);
	}
	$stmt->close();


	?> 
	<!-- CONTENT HERE -->


</div></body>
</html>