<?php
session_start();
include('funcs.php');
LoginCheck();
$pdo = db_connect();
$id = $_GET['id'];

  $sql  = "DELETE FROM kadai WHERE id=:id";
  $stmt = $pdo->prepare($sql);
  $stmt -> bindValue(':id',$id,PDO::PARAM_INT);
  $status = $stmt->execute(); 

  if($status==false){
    $error = $stmt->errorInfo();
    exit('QueryError:'.$error[2]);
  }else{
    header('Location: select.php');
  }
?>