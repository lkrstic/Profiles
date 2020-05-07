<?php
session_start();
require_once "pdo.php";

if(!isset($_GET['profile'])) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
}

$sql="SELECT * FROM profiles WHERE profile_id = :pid AND user_id = :uid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':pid' => $_GET['profile'], ':uid' => $_SESSION['user']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
} else {
  $toDelete = htmlentities($row['first_name'])." ".htmlentities($row['last_name']);
}

if(isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

if(isset($_POST['profile']) && isset($_POST['delete'])) {
  $sql="DELETE FROM profiles WHERE profile_id = :pid";
  $stmt=$pdo->prepare($sql);
  $stmt->execute(array(':pid' => $_POST['profile']));
  $_SESSION['success'] = "Profile deleted.";
  header("Location: index.php");
  return;
}
?>
<html>
<head>
  <title>Profiles</title>
</head>
<body>
  <p>Are you sure you want to delete <?=$toDelete?>?</p>
  <form method="post">
    <input type="hidden" name="profile" value="<?=$row['profile_id']?>">
    <p><input type="submit" name="delete" value="Delete">
      <input type="submit" name="cancel" value="Cancel">
    </p>
  </form>
</body>
</html>
