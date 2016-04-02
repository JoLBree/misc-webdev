<?php
require 'database.php';

// $filter = ['$month' => array(
// 	'date' => 2,
// 	)];


// "title" => "Title2"];

$filter = array();

$query = new MongoDB\Driver\Query($filter);

$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
$cursor = $manager->executeQuery("calendar.events", $query, $readPreference);



echo json_encode($cursor->toArray());
exit;

// I don't know the specific filtering syntax, etc

?>
