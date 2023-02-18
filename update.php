<?php
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください
session_start();
include('funcs.php');
LoginCheck();

$id       = $_POST["id"];
$station  = $_POST["station"];
$place    = $_POST["place"];
$detail   = $_POST["detail"];
$quantity = $_POST["quantity"];
$whose    = $_POST["whose"];
$action   = $_POST["action"];
$remarks  = $_POST["remarks"];
$fname    = $_FILES['fname']['name'];
// カンマ区切りの1行に変換する
$fname_str  =  implode(',', $fname);
$fnameShadow  = $_POST['fnameShadow'];
$countFiles = count($_FILES['fname']['name']);

if($countFiles == 0){
  $fname_str = $fnameShadow;
}else{
  $fname_str = $fname_str.','.$fnameShadow;
}
echo $fname_str;

// $upload = "./img/"; //画像アップロードフォルダへのパス→複数の場合はここをfor foreach文でまわす。
// for($i=0; $i<$countFiles; $i++){
//   if(move_uploaded_file($_FILES['fname']['tmp_name'][$i], $upload.$fname[$i])){
//   } else {
//     echo "Upload failed";
//     echo $_FILES['upfile']['error'][$i];
//   }
// }

$pdo = db_connect();
$sql = "UPDATE kadai SET station=:station,place=:place,detail=:detail,quantity=:quantity,whose=:whose,action=:action,remarks=:remarks ,fname=:fname WHERE id=:id;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':station',  $station,  PDO::PARAM_STR);
$stmt->bindValue(':place',    $place,    PDO::PARAM_STR);
$stmt->bindValue(':detail',   $detail,   PDO::PARAM_STR);
$stmt->bindValue(':quantity', $quantity, PDO::PARAM_STR);
$stmt->bindValue(':whose',    $whose,    PDO::PARAM_STR);
$stmt->bindValue(':action',   $action,   PDO::PARAM_STR);
$stmt->bindValue(':remarks',  $remarks,  PDO::PARAM_STR);
$stmt->bindValue(':id',       $id,       PDO::PARAM_INT);
$stmt->bindValue(':fname',    $fname_str,    PDO::PARAM_STR);
$status = $stmt->execute();

if($status==false){
    // SQLにエラーが実行されている場合
    $eror = $stmt->errorInfo();
    exit('ErrorQuery:'.$error[2]);
    }else{
        header('Location: select.php');
    }
?>