<?php
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください
session_start();
// DB接続
include('funcs.php');
$pdo  = db_connect();
$mail = $_POST['mail'];
    $sql  = 'SELECT * FROM kadai_user WHERE mail=:mail AND status=1';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':mail',$mail);
    $res  = $stmt->execute();
    //抽出データを取得
    $val = $stmt->fetch();

    if(password_verify($_POST['pw'],$val['pw'])){
        $_SESSION['chk_ssid'] = session_id();
        $_SESSION['manage'] = $val['manage'];
        $_SESSION['name'] = $val['name'];
        header('Location: index.php');
    }else{
        header('Location: login.php');
    }
    exit();
?>