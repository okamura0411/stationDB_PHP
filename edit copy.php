<?php
session_start();
include('funcs.php');
LoginCheck();
$pdo = db_connect();
$id=$_GET['id'];

  $sql  = "SELECT * FROM kadai WHERE id=:id";
  $stmt = $pdo->prepare($sql);
  $stmt -> bindValue(':id',$id,PDO::PARAM_INT);
//executeで実行。stmtを実行した結果がstatusに入ってくる。
  $status = $stmt->execute();
    $view='';
    if($status==false){
        // SQLにエラーが実行されている場合
        $eror = $stmt->errorInfo();
        exit('ErrorQuery:'.$error[2]);
        }else{
            $row = $stmt->fetch();
        }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>データ登録</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/jquery-2.1.3.min.js"></script>
</head>
<body>
<header>
  <nav>
    <a href="select.php">データ一覧</a>
    <a href="logout.php">ログアウト</a>
  </nav>
</header>
<form method="post" action="update.php" enctype="multipart/form-data">
<fieldset>
    <legend>点検項目入力</legend>
     <label>路線名<br>
     <label>駅名<br><input type="text" name="station" placeholder='例）三軒茶屋' value='<?=$row['station']?>'>駅</label><br>
     <label>詳細な場所<br><input type="text" name="place"  placeholder='例）上りホーム4号車1番ドア前天井' value='<?=$row['place']?>' style="width:300px;"></label><br>
     <label>損傷内容<br><input type="text" name="detail" placeholder='例）漏水' value='<?=$row['detail']?>' ></label><br>
     <label>数量<br><input type="text" name="quantity" placeholder='例）1・複数' value='<?=$row['quantity']?>'>箇所</label><br>
     <label>管理者<br>
     <select name="whose">
					<option value='<?=$row['whose']?>' selected><?=$row['whose']?></option>
					<option value="土木">土木</option>			
					<option value="建築">建築</option>
					<option value="その他">その他</option>
        </select></label><br>
     <label>今後の対応<br>
        <select name="action">
          <option value='<?=$row['action']?>' selected><?=$row['action']?></option>
					<option value="経過観察">経過観察</option>			
					<option value="通常補修">通常補修</option>
					<option value="緊急対応">緊急対応</option>
        </select></label><br>
        <input type="hidden" name='id' value='<?=$row['id']?>'>
     <label>備考欄<br><textArea name="remarks" rows="3" cols="40" placeholder='天井から2秒毎滴下・お客様への影響がないため通常対応とする。'><?=$row['remarks']?></textArea></label><br>
     <p class="sample"><img src="./img/<?=$row['fname']?>" class="reupFiles" width="200"></p>
            <label class="labelPhoto">
              <div class="upPhoto">
                画像を変更
              </div>
              <input type="file" name="fname" class="cms-item" accept="image/*" value="<?=$row['fname']?>">
              <input type="hiddin" name="fnameShadow" class="cms-item" accept="image/*" value="<?=$row['fname']?>">
            </label><br>
            <input type="submit" value="更新" class='touroku'>
    </fieldset>
</form>
<script>
  // アップロードするファイルを選択
$('input[type=file]').change(function() {
  //選択したファイルを取得し、file変数に格納
  let file = $(this).prop('files')[0];
  if (!file.type.match('image.*')) {
    $(this).val(''); //選択されてるファイルを空にする
    $('.sample > img').html(''); //画像表示箇所を空にする
    return;
  }
  // 画像表示
  let reader = new FileReader(); 
  reader.onload = function() {   
    $('.sample > img').attr('src', reader.result);
  }
  reader.readAsDataURL(file);    
  });

</script>
</body>
</html>
