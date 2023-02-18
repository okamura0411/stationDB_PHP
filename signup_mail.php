<?php
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください
session_start();
//DB情報
include('funcs.php');
$pdo = db_connect();
$errors = array();

if(isset($_POST['submit'])){
    if(empty($_POST['mail'])){
        $errors['mail'] = 'メールアドレスが未入力です。';
    }else{
        $mail = $_POST['mail'];
        if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = 'メールアドレスの形式が正しくありません。';
       }
        //DB確認        
        $sql = 'SELECT id FROM kadai_user WHERE mail=:mail';
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':mail', $mail, PDO::PARAM_STR);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        //user テーブルに同じメールアドレスがある場合、エラー表示
        if(isset($result['id'])){
            $errors['user_check'] = 'このメールアドレスはすでに利用されております。';
        }
        //DB確認        
        $sql = 'SELECT id FROM kadai_user WHERE mail=:mail';
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':mail', $mail, PDO::PARAM_STR);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        //user テーブルに同じメールアドレスがある場合、エラー表示
        if(isset($result['id'])){
        $errors['user_check'] = 'このメールアドレスはすでに利用されております。';
        }
    }
    // エラーが0だったら
    if (count($errors) === 0){
        // トークン発行
        $urltoken = hash('sha256',uniqid(rand(),1));
        // urlにトークンをつけている
        $url = 'http://localhost/train/03_kadai01/signup.php?urltoken='.$urltoken;
        try{
            $sql = "INSERT INTO kadai_pre_user (urltoken, mail, date, flag) VALUES (:urltoken, :mail, now(), '0')";
            $stm = $pdo->prepare($sql);
            $stm->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
            $stm->bindValue(':mail', $mail, PDO::PARAM_STR);
            $stm->execute();
            $message = 'メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。';          
        }catch (PDOException $e){
            print('Error:'.$e->getMessage());
            exit();
        }
        $mailTo = $mail;
        $subject = '仮登録が完了しました';
        $body = <<< EOM
        この度はご登録いただきありがとうございます。
        24時間以内に下記のURLからご登録下さい。
        {$url}
EOM;
        mb_language('ja');
        mb_internal_encoding('UTF-8');
        //Fromヘッダーを作成
        $headers = 'From:StationDB@hoge.com';
        if(mb_send_mail($mailTo,$subject,$body,$headers)){
            if (isset($_COOKIE['PHPSESSID'])) {
                setcookie('PHPSESSID', '', time() - 1800, '/');
            }
            //セッションを破棄する
            session_destroy();
            $message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい{$url}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 余力あったらCSS入れる -->
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <script src="./js/jquery-2.1.3.min.js"></script>
    <title>Document</title>
</head>
<body>
    <h1>仮会員登録画面</h1>
    <?php if (isset($_POST['submit']) && count($errors) === 0): ?>
    <p><?=$message?></p>
    <?php else: ?>
        <?php if(count($errors) > 0): ?>
       <?php
       foreach($errors as $value){
           echo "<p class='error'>".$value."</p>";
       }
       ?>
    <?php endif; ?>
        <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
            <p>メールアドレス：<input type="text" name="mail" size="50"></p> 
            <input type="submit" name="submit" value="送信">
        </form>
    <?php endif; ?>
</body>
</html>