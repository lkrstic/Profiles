<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

$stmt=$pdo->query("SELECT * FROM profiles");
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
  <title>Profiles</title>
</head>
<body>
  <h1>Profiles</h1>
  <?php
  //Display session messages.
  displayMessages();

  //Check if user is logged in.
  if(!isset($_SESSION['user'])) {
    echo '<p>Please <a href="login.php">login</a></p>';
  } else {
    echo '<p><a href="logout.php">Logout</a></p>';
  }

  //Display table if there are entries in the DB.
  //There is more info in the table if the user is logged in.
  if($rows != false) {
    echo '<table border="1">';
      echo '<tr>';
        echo '<td>Name</td>';
        echo '<td>Headline</td>';
        if(isset($_SESSION['user'])) {
          echo '<td>Action</td>';
        }
      echo '</tr>';
      foreach ($rows as $row) {
        $name=htmlentities($row['first_name']).' '.htmlentities($row['last_name']);
        echo '<td><a href="view.php?profile='.$row['profile_id'].'">'.$name.'</a></td>';
        echo '<td>'.htmlentities($row['headline']).'</td>';
        if(isset($_SESSION['user'])) {
          if($row['user_id'] == $_SESSION['user']){
            echo '<td><a href="edit.php?profile='.$row['profile_id'].'">Edit</a>
                    / <a href="delete.php?profile='.$row['profile_id'].'">Delete</a></td>';
          } else {
            echo '<td></td>';
          }
        }
        echo '</tr>';
      }
    echo '</table>';
  }

  if(isset($_SESSION['user'])) {
    echo '<p><a href="add.php">Add New Entry</a></p>';
  }
  ?>
</body>
</html>
