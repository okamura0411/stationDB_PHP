<?php
function LoginCheck(){
    if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid']!=session_id()){
        echo 'LOGIN Error!';
        exit();
      }else{
        session_regenerate_id(true);
        $_SESSION['chk_ssid'] = session_id();
      }      
}

function h($str){
  return htmlspecialchars($str, ENT_QUOTES);
}

//DBConnection
function db_connect(){
  try {
      $pdo = new PDO('mysql:dbname=gs_db;charset=utf8;host=localhost','root','');
 
    } catch (PDOException $e) {
      exit('DBConnectError:'.$e->getMessage());
    }
    return $pdo;
  }
?>
