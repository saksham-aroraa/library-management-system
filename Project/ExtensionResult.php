<?php
include 'dbinfo.php'; 
?>  
<html>

<head>
	<title>Result</title>
	<link rel="stylesheet" href="library.css">
</head>

<body>
	<h1 id="title">Request Extension On a Book</h1>

	<div class="parent">
		<div class="child-centralise">
<?php

session_start(); 

$username = $_SESSION['username'];
if(isset($_POST['issueid'])) {
$issueid = $_POST['issueid'];
$_SESSION['issueid'] = $issueid;
$today = date("Y-m-d");
$today_copy = new DateTime($today);
$plus = strtotime("+14 day", time());
$estimate = date('Y-m-d', $plus);
$link = mysqli_connect($host,$user,$pass) or die( "Unable to connect");
mysqli_select_db($link, $database) or die( "Unable to select database");

$sql_query = "Select ExtenDate, IssueDate, ReturnDate, IsChecked, FuRequester, NumExten from issue AS I, book AS B, bookcopy AS BC
	Where I.IssueID = '$issueid' AND I.ISBN = B.ISBN AND I.ISBN = BC.ISBN AND I.CopyID = BC.CopyID";
	
    $result = mysqli_query ($link, $sql_query)  or die(mysqli_error($link));  
	if($result == false){
		echo 'The query by ISBN failed.';
		exit();
	}
$row = mysqli_fetch_array($result);
$ischecked = $row['IsChecked'];

$sql_query = "Select IsFaculty from student_faculty AS SF Where SF.Username = '$username'";

    $result1 = mysqli_query ($link, $sql_query)  or die(mysqli_error($link));  
	if($result1 == false)
	{
		echo 'The query by ISBN failed.';
		exit();
	}
$row1 = mysqli_fetch_array($result1);
$isfaculty = $row1['IsFaculty'];

$furequester = $row['FuRequester'];
$ori_extendate = $row['ExtenDate'];
$ori_issuedate = $row['IssueDate'];
$ori_issuedate_copy = new DateTime($ori_issuedate);
$ori_returndate = $row['ReturnDate'];
$numexten = $row['NumExten'];
if($isfaculty == 0) {
	date_modify($ori_issuedate_copy, '+28 day');
	$max_allowedreturndate = date_format($ori_issuedate_copy, 'Y-m-d');
	$new_extendate = null;
	$new_returndate = null;
	if($ischecked == 1) {
		if($furequester == null){
			if($numexten < 2) {
				$new_extendate = $today;
				date_modify($today_copy, '+14 day');
				$new_returndate = date_format($today_copy, 'Y-m-d');
				if($new_returndate > $max_allowedreturndate){
					$new_returndate = $max_allowedreturndate;
				}
				$numexten = $numexten + 1;
			} else {
				echo 'You are not allowed to extend because you are only allowed to extend for 2 times.';
			}	
		} else {
			echo 'You are not allowed to extend because someone has made a future hold request.';
		}
	} else {
		echo 'This book has not been checked out yet.';
	}
} else {
	date_modify($ori_issuedate_copy, '+70 day');
	$max_allowedreturndate = date_format($ori_issuedate_copy, 'Y-m-d');
	$new_extendate = null;
	$new_returndate = null;
	if($ischecked == 1) {
		if($furequester == null){
			if($numexten < 5) {
				$new_extendate = $today;
				date_modify($today_copy, '+14 day');
				$new_returndate = date_format($today_copy, 'Y-m-d');
				if($new_returndate > $max_allowedreturndate){
					$new_returndate = $max_allowedreturndate;
				}
				$numexten = $numexten + 1;
			} else {
				echo 'You are not allowed to extend because you are only allowed to extend for 5 times.';
			}	
		} else {
			echo 'You are not allowed to extend because someone make a future hold request.';
		}
	} else {
		echo 'This book has not been checked out yet.';
	}
}
$_SESSION['issuedate'] = $ori_issuedate;
$_SESSION['extendate'] = $new_extendate;
$_SESSION['returndate'] = $new_returndate;
$_SESSION['numexten'] = $numexten;
}
?> 
<br>
			<br>

			<table>
				<tr>
					<td>Original Checkout Date</td>
					<td><?php echo $ori_issuedate; ?></td>
				</tr>

				<tr>
					<td>Current Extension Date</td>
					<td><?php echo $ori_extendate; ?></td>
				</tr>

				<tr>
					<td>New Extension Date</td>
					<td><?php echo $new_extendate; ?></td>
				</tr>

				<tr>
					<td>Current Return Date</td>
					<td><?php echo $ori_returndate; ?></td>
				</tr>

				<tr>
					<td>New Estimated Return Date</td>
					<td><?php echo $new_returndate; ?></td>
				</tr>
			</table>
			<form action="ConfirmExten.php" method="post">
			<span class="justForNow"><input class="primary-button" type="submit" value="Submit" /></span>
			</form>
			<div class="justForNow">
			<form action="UserSummary.php" method="post">
				<input class="secondary-button" type="submit" value="Back" />
			</form>
		</div>
</body>

</html>