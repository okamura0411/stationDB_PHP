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
    if($status==false){
        // SQLにエラーが実行されている場合
        $eror = $stmt->errorInfo();
        exit('ErrorQuery:'.$error[2]);
        }else{
            $row = $stmt->fetch();
        }
        $fname_arr = explode(",", $row['fname']);
        $json = json_encode($fname_arr);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ブックマークリスト</title>
<link rel="stylesheet" href="./css/reset.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="./css/style.css">
<script src="./js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
</head>
<body id="main">
<header>
  <nav>
    <a href="index.php">入力画面</a>
    <a href="select.php">データ一覧</a>
    <a href="logout.php">ログアウト</a>
  </nav>
</header>
    <ul class="slider-2" id="js-slider-2">
    </ul>
    <div class="dots-2"></div>
</div>


<script>
const js_arr = JSON.parse('<?= $json ?>');
console.log(js_arr);
let html="";
for(i=0; i<js_arr.length; i++){
      html += `
      <li><img class="slick-img" src="./img/${js_arr[i]}" alt=""></li>
      `;
    }
    $(".slider-2").append(html);

$(function () {
  $('#js-slider-2').slick({
    arrows: true, // 前・次のボタンを表示する
    dots: true, // ドットナビゲーションを表示する
    appendDots: $('.dots-2'), // ドットナビゲーションの生成位置を変更
    speed: 1000, // スライドさせるスピード（ミリ秒）
    slidesToShow: 1, // 表示させるスライド数
    centerMode: true, // slidesToShowが奇数のとき、現在のスライドを中央に表示する
    variableWidth: true, // スライド幅の自動計算を無効化
  });
});

</script>
</body>
</html>