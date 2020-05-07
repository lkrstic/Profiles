<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

if(!isset($_SESSION['user'])) {
  die('You must be logged in to view this page.');
}

if(!isset($_GET['profile'])) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
}

//get profile based on query string and logged in user
$stmt=$pdo->prepare("SELECT * FROM profiles
                   WHERE profile_id = :profile AND user_id = :user");
$stmt->execute(array(':profile' => $_GET['profile'],
                     ':user' => $_SESSION['user']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  $_SESSION['error'] = "Profile not found.";
  header("Location: index.php");
  return;
}

//get positions associated with profile
$positions=loadPos($pdo, $_GET['profile']);


if(isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

//POST validation
if(isset($_POST['submit']) && isset($_POST['first_name']) &&
   isset($_POST['last_name']) && isset($_POST['email']) &&
   isset($_POST['headline']) && isset($_POST['summary'])) {

  $msg=validateProfile();
  if(is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile=".$_POST['profile_id']);
    return;
  }

  $msg=validatePosition();
  if(is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile=".$_POST['profile_id']);
    return;
  }

  //Update profile
  $sql="UPDATE profiles
        SET first_name = :fname, last_name = :lname,
            email = :em, headline = :hl, summary = :su
        WHERE profile_id = :pid";
  $stmt=$pdo->prepare($sql);
  $stmt->execute(
    array(
      ':fname' => $_POST['first_name'],
      ':lname' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':hl' => $_POST['headline'],
      ':su' => $_POST['summary'],
      ':pid' => $_POST['profile_id']
    )
  );

  //Update positions
  $stmt=$pdo->prepare("DELETE FROM positions WHERE profile_id=:pid");
  $stmt->execute(array(':pid' => $_POST['profile_id']));

  $rank=1;
  for($i=0;$i<=9;$i++) {
    if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) {
      continue;
    }

    $stmt=$pdo->prepare("INSERT INTO positions (profile_id, rank, year, description)
                         VALUES (:pid, :rank, :year, :desc)");
    $stmt->execute(array(
      ':pid' => $_POST['profile_id'],
      ':rank' => $rank,
      ':year' => $_POST['year'.$i],
      ':desc' => $_POST['desc'.$i]
    ));
    $rank++;
  }





  $_SESSION['success'] = "Record updated.";
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
  <h1>Edit Profile</h1>
  <?php
  //Display session messages
  displayMessages();
  ?>
  <form method="post">
    <p>First Name: <input type="text" name="first_name" size="60" value="<?=htmlentities($row['first_name'])?>"></p>
    <p>Last Name: <input type="text" name="last_name" size="60" value="<?=htmlentities($row['last_name'])?>"></p>
    <p>Email: <input type="text" name="email" size="30" value="<?=htmlentities($row['email'])?>"></p>
    <p>Headline:<br/>
      <input type="text" name="headline" size="80" value="<?=htmlentities($row['headline'])?>">
    </p>
    <p>Summary:<br/>
      <textarea name="summary" rows="8" cols="80"><?=htmlentities($row['summary'])?></textarea>
    </p>
    <p>Position:
      <input type="submit" value="+" id="addPos">
      <div id="position_fields">
        <?php
        //load associated positions
        for($i=0;$i<9;$i++){
          if (!isset($positions[$i])) {
            continue;
          }
          echo '<div id="position'.$positions[$i]['rank'].'">';
          echo '<p>Year: <input type="text" name="year'.$positions[$i]['rank'].'"
                   value="'.htmlentities($positions[$i]['year']).'">';
          echo '<input type="submit" value="-" class="removePos"></p>';
          echo '<textarea name="desc'.$positions[$i]['rank'].'"
                rows="8" cols="80">';
          echo htmlentities($positions[$i]['description']).'</textarea>';
          echo '</div>';
        }
        ?>
      </div>
    </p>
    <input type="hidden" name="profile_id" value="<?=htmlentities($row['profile_id'])?>">
    <p><input type="submit" name="submit" value="Save"> <input type="submit" name="cancel" value="Cancel"></p>
  </form>
</body>
<script>
//Add and remove position fields as needed
$(document).ready(function() {

  //find existing divs inside of position_fields and subtract count from max
  countPos=$("#position_fields div").length;

  console.log(countPos);
  window.console && console.log('Document ready called');

  $('#addPos').click(function(event){
    event.preventDefault();
    if (countPos >= 9) {
      alert("Maximum of 9 postion entries exceeded.");
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

  $('.removePos').click(function(event){
    event.preventDefault();
    $(this).closest('div').remove();
    return false;
  });
});
</script>
</html>
