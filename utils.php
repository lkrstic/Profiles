<?php

function displayMessages() {
  if(isset($_SESSION['success'])) {
    echo '<p style="color:green;">'.$_SESSION['success'].'</p>';
    unset($_SESSION['success']);
  }
  if(isset($_SESSION['error'])) {
    echo '<p style="color:red;">'.$_SESSION['error'].'</p>';
    unset($_SESSION['error']);
  }
}


function validateProfile() {
  if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
     strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
    return "All fields are required.";
  }
  if(strpos($_POST['email'], '@') === false) {
    return "Invalid email.";
  }

  return true;
}


function validatePosition() {
  for($i<=1;$i<=9;$i++) {
    if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) {
      continue;
    }

    if(strlen($_POST['year'.$i]) == 0 || strlen($_POST['desc'.$i]) == 0) {
      return "All fields required.";
    }

    if(! is_numeric($_POST['year'.$i])) {
      return "Position year must be numeric.";
    }
  }
  return true;
}


function loadPos($pdo, $profile_id) {
  $stmt=$pdo->prepare("SELECT * FROM positions
                      WHERE profile_id = :profile");
  $stmt->execute(array(':profile' => $profile_id));
  $positions=array();
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $positions[]=$row;
  }
  return $positions;
}
