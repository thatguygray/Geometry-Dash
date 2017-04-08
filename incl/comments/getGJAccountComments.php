<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$userstring = "";
$songsstring  = "";
$type = $ep->remove($_POST["type"]);
$accountid = $ep->remove($_POST["accountID"]);
$page = $ep->remove($_POST["page"]);
$commentpage = $page*10;
$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :accountid");
$query2->execute([':accountid' => $accountid]);
if ($query2->rowCount() > 0) {
	$userID = $query2->fetchAll()[0]["userID"];
} else {
	$query = $db->prepare("INSERT INTO users (isRegistered, extID)
	VALUES (:register,:id)");

	$query->execute([':register' => $register, ':id' => $id]);
	$userID = $db->lastInsertId();
}
$query = "SELECT * FROM acccomments WHERE userID = :userID ORDER BY timeStamp DESC LIMIT 10 OFFSET $commentpage";
$query = $db->prepare($query);
$query->execute([':userID' => $userID]);
$result = $query->fetchAll();
$countquery = $db->prepare("SELECT count(*) FROM acccomments WHERE userID = :userID");
$countquery->execute([':userID' => $userID]);
$commentcount = $countquery->fetchAll()[0][0];
foreach($result as &$comment1) {
	if($comment1["commentID"]!=""){
		$uploadDate = date("d/m/Y G:i", $comment1["timestamp"]);
		$commentstring .= "2~".$comment1["comment"]."~3~".$comment1["userID"]."~4~".$comment1["likes"]."~5~0~7~".$comment1["isSpam"]."~9~".$uploadDate."~6~".$comment1["commentID"]."|";
	}
}
$commentstring = substr($commentstring, 0, -1);
echo $commentstring;
echo "#".$commentcount.":".$commentpage.":10";
?>