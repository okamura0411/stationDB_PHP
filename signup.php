<?php
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください
session_start();
//DB情報
include('funcs.php');
$pdo = db_connect();
$errors = array();

if(empty($_GET)) {
	header("Location: registration_mail");
	exit();
}else{
	//エラーがなければelse以降を処理する
	$urltoken = isset($_GET["urltoken"]) ? $_GET["urltoken"] : NULL;
	if ($urltoken == ''){
		$errors['urltoken'] = "トークンがありません。";
	}else{
		try{
			// DB接続	
			//flagが0の未登録者 or 登録データから24時間を引いて確認。
			$sql = 'SELECT * FROM kadai_pre_user WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour';
            $stm = $pdo->prepare($sql);
			$stm->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$stm->execute();
			
			//レコード件数取得
			$row_count = $stm->rowCount();
			
			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count ==1){
				$mail_array = $stm->fetch();
				$mail = $mail_array["mail"];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおして下さい。";
			}
			//データベース接続切断
			$stm = null;
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			exit();
		}
	}
}
/**
* 確認する(btn_confirm)押した後の処理
*/
if(isset($_POST['btn_confirm'])){
	if(empty($_POST)) {
		header("Location: login.php");
		exit();
	}else{
		//POSTされたデータを各変数に入れる
		$name = $_POST['name'];
		$pw = $_POST['pw'];
		//セッションに登録
		$_SESSION['name'] = $name;
		$_SESSION['pw'] = $pw;

		//アカウント入力判定
		//パスワード入力判定
		if ($pw == ''):
			$errors['pw'] = "パスワードが入力されていません。";
		else:
			$password_hide = str_repeat('*', strlen($pw));
		endif;
		if ($name == ''):
			$errors['name'] = "氏名が入力されていません。";
		endif;
	}
}
/**
* page_3
* 登録(btn_submit)押した後の処理
*/
if(isset($_POST['btn_submit'])){
	$pw_hash =  password_hash($_SESSION['pw'], PASSWORD_DEFAULT);
	//ここでデータベースに登録する
	try{
		$sql = "INSERT INTO kadai_user (name,pw,mail,status,created_at,updated_at) VALUES (:name,:pw_hash,:mail,1,now(),now())";
        $stm = $pdo->prepare($sql);
		$stm->bindValue(':name', $_SESSION['name'], PDO::PARAM_STR);
		$stm->bindValue(':mail', $_SESSION['mail'], PDO::PARAM_STR);
		$stm->bindValue(':pw_hash', $pw_hash, PDO::PARAM_STR);
		$stm->execute();

		//pre_userのflagを1にする(トークンの無効化)
		$sql = "UPDATE kadai_pre_user SET flag=1 WHERE mail=:mail";
		$stm = $pdo->prepare($sql);
		//プレースホルダへ実際の値を設定する
		$stm->bindValue(':mail', $mail, PDO::PARAM_STR);
		$stm->execute();
		/*
		* 登録ユーザへ本登録メール送信
       */
		$to = $mail;
		$subject =  '本登録が完了しました。';
		$body = <<< EOM
		この度はご登録いただきありがとうございます。
		本登録致しました。
EOM;
       mb_language('ja');
       mb_internal_encoding('UTF-8');
       //Fromヘッダーを作成
       $headers = 'From: stationDB@test.com';
       if(mb_send_mail($to, $subject, $body, $headers)){          
           $message['success'] = "会員登録しました";
       }else{
           $errors['mail_error'] = "メールの送信に失敗しました。";
		}	

		//データベース接続切断
		$stm = null;
		//セッション変数を全て解除
		$_SESSION = array();
		//セッションクッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
		}
		//セッションを破棄する
		session_destroy();
	}catch (PDOException $e){
		//トランザクション取り消し（ロールバック）
		$pdo->rollBack();
		$errors['error'] = "もう一度やりなおして下さい。";
		print('Error:'.$e->getMessage());
	}
}
?>

<h1>会員登録画面</h1>
<!-- page_3 完了画面-->
<?php if(isset($_POST['btn_submit']) && count($errors) === 0):?>
本登録されました。
<a href="./login.php">ログイン画面に戻る</a>

<!-- page_2 確認画面-->
<?php elseif (isset($_POST['btn_confirm']) && count($errors) === 0): ?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
		<p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
		<p>パスワード：<?=$pw?></p>
		<p>氏名：<?=htmlspecialchars($name, ENT_QUOTES)?></p>
		
		<input type="submit" name="btn_back" value="戻る">
		<input type="submit" name="btn_submit" value="登録する">
	</form>
<?php else: ?>
	<!-- page_1 登録画面 -->
	<?php if(count($errors) > 0): ?>
       <?php
       foreach($errors as $value){
           echo "<p class='error'>".$value."</p>";
       }
       ?>
		<?php endif; ?>
		<?php if(!isset($errors['urltoken_timeover'])): ?>
			<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
				<p>メールアドレス：<?=$mail?></p>
				<p>パスワード：<input type="password" name="pw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">(0~1, a~z, A~Z各1文字以上を含む8文字以上)</p>
				<p>氏名：<input type="text" name="name"></p>
				<input type="submit" name="btn_confirm" value="確認する">
			</form>
		<?php endif ?>
	<?php endif; ?>
