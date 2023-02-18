<?php
session_start();
include('funcs.php');
LoginCheck();
$pdo = db_connect();

// stmtにデータが全て入っている。正直ここで何してるかわからん。
$stmt = $pdo->prepare("SELECT * FROM kadai");
$status = $stmt->execute();
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("sqlError:".$error[2]);
}
//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); 
//PDO::FETCH_ASSOC[カラム名のみで取得できるモード]$stmtのデータを1行ずつ$valuesに入れている。
$json = json_encode($values);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ブックマークリスト</title>
<link rel="stylesheet" href="./css/reset.css">
<link rel="stylesheet" href="./css/style.css">
<script src="./js/jquery-2.1.3.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body id="main">
<header>
  <nav>
    <a href="index.php">入力画面</a>
    <a href="logout.php">ログアウト</a>
  </nav>
  <div class="search">
    <input type="text" id="s">
    <button id="btn" type="button">
      <img src="./images/search.png" alt="search" style="width:20px; border-radius:50px;">
    </button>
  </div>
</header>
<div>
    <table id='list'>
        <tr id="kind_item">
          <th>No.</th>
          <th>駅名</th>
          <th>場所</th>
          <th>詳細</th>
          <th>数量</th>
          <th>管理者</th>
          <th>今後の対応</th>
          <th>備考</th>
          <th>画像</th>
        </tr>
      <?php
      $num = 0;
      foreach($values as $v){
        $num++;
        $fname_arr = explode(',',$v["fname"]);
      ?>
        <tr>
          <td><?=$num?></td>
          <td><?=$v["station"]?></td>
          <td><?=$v["place"]?></td>
          <td><?=$v["detail"]?></td>
          <td><?=$v["quantity"]?></td>
          <td><?=$v["whose"]?></td>
          <td><?=$v["action"]?></td>
          <td style='text-align:left;'><?=$v["remarks"]?></td>
          <td style="height: 100px;">
            <a href="./slick.php?id=<?=$v["id"]?>">
            <img src="./img/<?=$fname_arr[0]?>" alt="" class="s_photo s_big" style="height: 80px;"><span style="margin-left:10px;"><?=count($fname_arr);?>枚</span></a>
          </td>
          <td class='edit'>
            <a href="./edit.php?id=<?=$v["id"]?>">
              <img src="./images/edit.png" class="editIcon" alt=""></a>
            <a href="./delete.php?id=<?=$v["id"]?>">
              <img src="./images/delete.png"class="deleteIcon" alt=""></a>
          </td>  
        </tr>
      <?php
    }
    ?>
    </table>
</div>
<!-- Main[End] -->
<script>
  const js_arr = JSON.parse('<?= $json ?>');
  console.log(js_arr);//foreach文の中の$valueと同じ役割
  for(i=0; i<js_arr.length; i++){
    js_fnames = js_arr[i].fname.split(',');//ここでカンマ区切りの文字列を配列に戻す。
  };
  console.log(js_fnames);
  $('#btn').on('click',function(){
  const params = new URLSearchParams();
  params.append('s',$('#s').val());
  let html="";
  let kind_item =
  `
  <tr id="kind_item">
    <th>No.</th>
    <th>駅名</th>
    <th>場所</th>
    <th>詳細</th>
    <th>数量</th>
    <th>管理者</th>
    <th>今後の対応</th>
    <th>備考</th>
    <th>画像</th>
  </tr>
  `
  axios.post('select_json.php',params).then(function (response) {
    console.log(typeof response.data); //通信OK配列の形で保存されている。
    for(let i=0;i<response.data.length;i++){
      let str    = response.data[i].fname;
      // 複数ファイルの先頭を抽出
      if (str.indexOf(',')) {
        let strCut = str.substr(0, str.indexOf(','));
        str = strCut;
      }
      let strCut = str.substr(0, str.indexOf(','));
      // カンマの数を数える
      // let kanmaCount = str.match(/,/g);
      let kanmaCount = response.data[i].fname.split(',').length;
      console.log(response.data[i].fname.split(','));
      console.log(response.data[i].fname.split(',').length);
      console.log(kanmaCount);
      html += `
      <tr>
        <td>${i+1}</td>
        <td>${response.data[i].station}</td>
        <td>${response.data[i].place}</td>
        <td>${response.data[i].detail}</td>
        <td>${response.data[i].quantity}</td>
        <td>${response.data[i].whose}</td>
        <td>${response.data[i].action}</td>
        <td style='text-align:left;'>${response.data[i].remarks}</td>
          <td style="height: 100px;">
          <a href="./slick.php?id=<?=$v["id"]?>">
          <img src="./img/${str}" alt="" class="s_photo s_big" style="height: 80px;"><span style="margin-left:10px;">${kanmaCount}枚</span></td></a>
          <td class='edit'>
          <a href="./edit.php?id=${response.data[i].id}">
          <img src="./images/edit.png" class="editIcon" alt=""></a>
          <a href="./delete.php?id=${response.data[i].id}">
          <img src="./images/delete.png"class="deleteIcon" alt=""></a>
        </td>  
    </tr>
      `;
    }
    $("#list").html(kind_item);
    $("#list").append(html);
    // この下にclass処理を追加する。
    $("td:contains('緊急対応')").addClass('red');
    }).catch(function (error) {
        console.log(error);  //通信Error
    }).then(function () {
        console.log("Last"); //通信OK/Error後に処理を必ずさせたい場合
    });
});

// hover,clickの処理

$(document).ready(function(){
  $("td:contains('緊急対応')").addClass('red');
  });
  
  $(document).on('click','.s_photo',function(){
    if($(this).hasClass('big')){
      $(this).removeClass('big');
      $(this).addClass('s_big');
      }else{
        $('.s_photo').removeClass('big');
        $(this).addClass('big');
        $(this).removeClass('s_big');
      }
    });
</script>
</body>
</html>