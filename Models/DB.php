<?php

function Connect(){

  $servername = "localhost";
  $username = "root";
  $password ="";
  $dbname ="librarydb";

  $conn = mysqli_connect(
    $servername,
    $username,
    $password,
    $dbname
  );

  if(!$conn){
    die("Connection Failed: ".mysqli_connect_error());
  }

  return $conn;

}

function Close($conn){
  mysqli_close($conn);
}
