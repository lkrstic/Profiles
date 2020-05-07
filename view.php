<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

if(!isset($_GET['profile'])) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
}

$sql="SELECT * FROM profiles WHERE profile_id = :pid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':pid' => $_GET['profile']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
}

$positions=loadPos($pdo, $_GET['profile']);
?>
<html>
<head>
  <title>Profiles</title>
</head>
<body>
  <h1>Profile Details</h1>
  <p>First Name: <?=htmlentities($row['first_name'])?></p>
  <p>Last Name: <?=htmlentities($row['last_name'])?></p>
  <p>Email: <?=htmlentities($row['email'])?></p>
  <p>Headline:<br/> <?=htmlentities($row['headline'])?></p>
  <p>Summary:<br/> <?=htmlentities($row['summary'])?></p>
  <p>Positions:<br/>
    <?php
    echo '<ul>';
    foreach($positions as $position) {
      echo '<li>'.htmlentities($position['year']).': '.htmlentities($position['description']).'</li>';
    }
    echo '</ul>';
    ?>
  </p>
  <p><a href="index.php">Done</a></p>
</body>
</html>
