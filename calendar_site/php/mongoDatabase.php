<?php
header("Content-Type: application/json");
require '/etc/php5/cli/vendor/autoload.php';

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");


// please keep these comments here till final submission



// echo extension_loaded("mongodb") ? "loaded\n" : "not loaded\n";
// require __DIR__ . '/vendor/autoload.php';

// print_r(get_loaded_extensions());
//  phpinfo();
// var_dump($manager);

// $filter = array();
// $options = array(
// 	"users" => array("username" => 1),
//     // /* Only return the following fields in the matching documents */
//     // "projection" => array(
//     //     "title" => 1,
//     //     "article" => 1,
//     // ),
//     // "sort" => array(
//     //     "views" => -1,
//     // ),
//     // "modifiers" => array(
//     //     '$comment'   => "This is a query comment",
//     //     '$maxTimeMS' => 100,
// );

// $query = new MongoDB\Driver\Query($filter);

// $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
// $cursor = $manager->executeQuery("calendar.events", $query, $readPreference);

// echo json_encode(iterator_to_array($cursor));

// foreach($cursor as $document) {
// 	echo "hi";
//     var_dump($document);
// }



// ****************insertion***************



// $bulk = new MongoDB\Driver\BulkWrite;

// $document1 = ['username' => 'phpdude', 'pw_hash' => '$1$SEyXWUbC$AJKaXmNCmmh1549aZs1qN/', 'token_hash' => null];

// $_id1 = $bulk->insert($document1);

// var_dump($_id1);

// $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
// $result = $manager->executeBulkWrite('calendar.users', $bulk, $writeConcern);





// $collection = $client->calendar->users;
// $result = $collection->find( [ 'username' => 'person' ] );

// foreach ($result as $entry) {
//     echo $entry;
// }

// // $client = new MongoDB\Client("mongodb://localhost:27017");
// $collection = $client->demo->beers;

// $result = $collection->insertOne( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );

// echo "Inserted with Object ID '{$result->getInsertedId()}'";

?>