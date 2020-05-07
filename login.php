<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

//POST validation
if(isset($_POST['email']) && isset($_POST['pass'])) {
  if(strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
    $_SESSION['error'] = "Both fields required.";
    header("Location: login.php");
    return;
  }
  if(strpos($_POST['email'], '@') === false) {
    $_SESSION['error'] = "Invalid email.";
    header("Location: login.php");
    return;
  }

  //Stored password is hashed. Hash input and check for user in DB.
  $check=hash('md5', $_POST['pass']);
  $sql="SELECT user_id FROM users WHERE email = :email AND pass = :pass";
  $stmt=$pdo->prepare($sql);
  $stmt->execute(
    array(
      ':email' => $_POST['email'],
      ':pass' => $check
    )
  );
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false) {
    $_SESSION['error'] = "Email or password is incorrect.";
    header("Location: login.php");
    return;
  } else {
    $_SESSION['user'] = $row['user_id'];
    $_SESSION['success'] = "Login success";
    header("Location: index.php");
    return;
  }
}
?>
<html>
<head>
  <title>Profiles</title>
</head>
<body>
  <h1>Please log in</h1>
  <?php
  //Display session messages.
  displayMessages();
  ?>
  <form method="post">
    <p>Email: <input type="text" name="email" id="email"></p>
    <p>Password: <input type="password" name="pass" id="pass"></p>
    <p><input type="submit" value="Log in" onClick="return doValidate();">
      <a href="index.php">Cancel</a></p>
  </form>
</body>
<script type="text/javascript">
//Front End data validation
function doValidate() {
  console.log('Validating...');
  try {
    let email = document.getElementById('email').value;
    let pass = document.getElementById('pass').value;
    console.log('Validating email='+email+' pass='+pass);
    if(email == null || email == "" || pass == null || pass == "") {
      alert('Both fields required.');
      return false;
    }
    if(email.indexOf('@') == -1) {
      alert('Invalid email');
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
  return false;
}
</script>
</html>
