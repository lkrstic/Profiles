<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

//Can only be visited by logged in user.
if(!isset($_SESSION['user'])) {
  die('You must be logged in to view this page.');
}

//POST validation
if(isset($_POST['first_name']) && isset($_POST['last_name']) &&
   isset($_POST['email']) && isset($_POST['headline']) &&
   isset($_POST['summary'])) {

  $msg = validateProfile();
  if(is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }

  $msg = validatePosition();
  if(is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }

  //Add the new entry after data validation.
  $sql="INSERT INTO profiles (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fname, :lname, :em, :hl, :su)";
  $stmt=$pdo->prepare($sql);
  $stmt->execute(
    array(
      ':uid' => $_SESSION['user'],
      ':fname' => $_POST['first_name'],
      ':lname' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':hl' => $_POST['headline'],
      ':su' => $_POST['summary']
    )
  );
  $profile_id = $pdo->lastInsertId();

  $rank=1;
  for($i=1;$i<=9;$i++) {
    if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) {
      continue;
    }

    $sql="INSERT INTO positions (profile_id, rank, year, description)
          VALUES (:pid, :rank, :year, :desc)";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(
      array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $_POST['year'.$i],
        ':desc' => $_POST['desc'.$i]
      )
    );
    $rank++;
  }

  $_SESSION['success'] = "Record added.";
  header("Location: index.php");
  return;
}
?>
<html>
<head>
  <title>Profiles</title>
  <script src="https://code.jquery.com/jquery-3.2.1.js"
   integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
   crossorigin="anonymous">
  </script>
</head>
<body>
  <h1>Add a New Profile</h1>
  <?php
  //Display session messages.
  displayMessages();
  ?>
  <form method="post">
    <p>First Name: <input type="text" name="first_name" size="60"></p>
    <p>Last Name: <input type="text" name="last_name" size="60"></p>
    <p>Email: <input type="text" name="email" size="30"></p>
    <p>Headline:<br/>
      <input type="textarea" name="headline" size="80">
    </p>
    <p>Summary:<br/>
      <textarea name="summary" rows="8" cols="80"></textarea>
    </p>
    <p>Position:
      <input type="submit" value="+" id="addPos">
      <div id="position_fields"></div>
    </p>
    <p><input type="submit" value="Add"> <a href="index.php">Cancel</a></p>
  </form>
</body>
<script>
countPos=0;
$(document).ready(function() {
  window.console && console.log('Document ready called');
  $('#addPos').click(function(event){
    event.preventDefault();
    if (countPos >= 9) {
      alert("Maximum of 9 position entries exceeded.");
      return;
    }
    countPos++;
    window.console && console.log('Adding position: '+countPos);

    $('#position_fields').append('<div id="position'+countPos+'"> \
    <p>Year: <input type="text" name="year'+countPos+'"> \
    <input type="button" value="-" \
      onclick="$(\'#position'+countPos+'\').remove(); return false;"></p> \
    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
    </div>');
  });
});
</script>
</html>
