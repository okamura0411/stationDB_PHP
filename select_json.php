<?php
session_start();
include("funcs.php");
LoginCheck();
$pdo = db_connect();

//２．データ登録SQL作成
$sql = "SELECT * FROM kadai WHERE  station  LIKE :value 
                                OR place    LIKE :value 
                                OR quantity LIKE :value 
                                OR detail   LIKE :value 
                                OR whose    LIKE :value 
                                OR action   LIKE :value 
                                OR remarks  LIKE :value";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":value", '%'.$_POST["s"].'%' ,PDO::PARAM_STR);

$status = $stmt->execute();

//３．データ表示
$values = "";
if($status==false) {
//   sql_error($stmt);
}else{
  //全データ取得
  $values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
  echo $json = json_encode($values,JSON_UNESCAPED_UNICODE);  //JSに渡したいとき
}