<?php
require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

if (isset($_POST['year']) && isset($_POST['month']) && isset($_POST['date'])){

$date = $_POST['year']."-".$_POST['month']."-".$_POST['date']." 00:00:00";

$stmt = $mysqli->prepare("SELECT event_id, title, body, tag, datetime 
	from events 
	where user_id = ? and (datetime BETWEEN ? AND (DATE_ADD(?, INTERVAL 42 DAY)))
	ORDER BY datetime ASC;");
$userid = 2;
$stmt->bind_param('iss', $userid, $date, $date);
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
 
$stmt->execute();
 
$result = $stmt->get_result();


while($row = $result->fetch_assoc()) {
    $rows[] = $row;
    // echo $row;
}
// echo ($rows);
echo json_encode($rows);

// echo "<ul>\n";
// while($row = $result->fetch_assoc()){
// 	printf("\t<li>%s %s</li>\n",
// 		htmlspecialchars( $row["first_name"] ),
// 		htmlspecialchars( $row["last_name"] )
// 	);
// }
// echo "</ul>\n";
 
$stmt->close();
}
exit;


?>
