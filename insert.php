<?php
session_start();
include('funcs.php');
LoginCheck();
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください

$station  = $_POST["station"];
$place    = $_POST["place"];
$quantity = $_POST["quantity"];
$detail   = $_POST["detail"];
$whose    = $_POST["whose"];
$action   = $_POST["action"];
$remarks  = $_POST["remarks"];

// 配列で送信されている。それを一つ一つ処理していく。
$fname    = $_FILES['fname']['name'];

// 配列をカンマ区切りの1行にかえる。
$fname_str  =  implode(',', $fname);
$countFiles = count($_FILES['fname']['name']);

$upload = "./img/"; //画像アップロードフォルダへのパス
for($i=0; $i<$countFiles; $i++){
  if(move_uploaded_file($_FILES['fname']['tmp_name'][$i], $upload.$fname[$i])){
  } else {
    echo "Upload failed";
    echo $_FILES['upfile']['error'][$i];
  }
}

$pdo = db_connect();
$sql = "INSERT INTO kadai(station, place, detail, quantity, whose, action, remarks, time, fname)
                   VALUES(:station, :place, :detail, :quantity, :whose, :action, :remarks, now(), :fname);";
$stmt = $pdo->prepare($sql);//各項目の準備完了
$stmt->bindValue(':station',  $station,    PDO::PARAM_STR);
$stmt->bindValue(':place',    $place,      PDO::PARAM_STR);
$stmt->bindValue(':detail',   $detail,     PDO::PARAM_STR);
$stmt->bindValue(':quantity', $quantity,   PDO::PARAM_STR);
$stmt->bindValue(':whose',    $whose,      PDO::PARAM_STR);
$stmt->bindValue(':action',   $action,     PDO::PARAM_STR);
$stmt->bindValue(':remarks',  $remarks,    PDO::PARAM_STR);
$stmt->bindValue(':fname',    $fname_str,  PDO::PARAM_STR);

$status = $stmt->execute(); //idと時間は自動で登録してくれている。

//データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQLError:".$error[2]);
}else{
  //５．index.phpへ戻る
  header("Location: index.php");
  exit();
}
?>
