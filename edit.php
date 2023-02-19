<?php
session_start();
include('funcs.php');
ini_set('display_errors', 'On'); // エラーを表示させるようにしてください
error_reporting(E_ALL); // 全てのレベルのエラーを表示してください
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
        $fname_arr = explode(',',$row['fname']);
        $json = json_encode($fname_arr);
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
<form id="postForm" method="post" action="update.php" enctype="multipart/form-data">
<fieldset>
    <legend style="background-color: #FAC300;">修正画面</legend>
     <label>路線名<br>
        <select name="line" id='line'>
              <!-- <option value="" selected disabled>選択してください</option> -->
              <option value="YM">山手線</option>
              <option value="TY">東横線</option><br>
            </select></label><br>
      <label>駅名<br>
        <select name="station" id='station'>
              <!-- <option value="" selected disabled>選択してください</option> -->
              <option value='<?=$row['station']?>' selected><?=$row['station']?></option>
                <optgroup label="山手線" id="YM">
                  <option value="東京">東京</option>
                  <option value="有楽町">有楽町</option>
                  <option value="新橋">新橋</option>			
                  <option value="浜松町">浜松町</option>
                  <option value="田町">田町</option>			
                  <option value="高輪ゲートウェイ">高輪ゲートウェイ</option>
                  <option value="品川">品川</option>			
                  <option value="大崎">大崎</option>
                  <option value="五反田">五反田</option>			
                  <option value="目黒">目黒</option>
                  <option value="恵比寿">恵比寿</option>
                  <option value="渋谷">渋谷</option>
                  <option value="原宿">原宿</option>			
                  <option value="代々木">代々木</option>
                  <option value="新宿">新宿</option>			
                  <option value="新大久保">新大久保</option>
                  <option value="高田馬場">高田馬場</option>			
                  <option value="目白">目白</option>
                  <option value="池袋">池袋</option>			
                  <option value="大塚">大塚</option>
                  <option value="巣鴨">巣鴨</option>
                  <option value="駒込">駒込</option>
                  <option value="田端">田端</option>			
                  <option value="西日暮里">西日暮里</option>
                  <option value="日暮里">日暮里</option>			
                  <option value="鶯谷">鶯谷</option>
                  <option value="上野">上野</option>			
                  <option value="御徒町">御徒町</option>
                  <option value="秋葉原">秋葉原</option>			
                  <option value="神田">神田</option>
                </optgroup>
                <optgroup label="東横線" id="TY">
                  <option value="渋谷">渋谷</option>
                  <option value="代官山">代官山</option>
                  <option value="中目黒">中目黒</option>			
                  <option value="祐天寺">祐天寺</option>
                  <option value="学芸大学">学芸大学</option>			
                  <option value="都立大学">都立大学</option>
                  <option value="自由が丘">自由が丘</option>			
                </optgroup>
            </select></label><br>
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
        <input type="submit" value="更新" class='touroku'>
    </fieldset>
    <div id="imgField">
      <div id="imgTitle">
      <button type="button" id="fileTitle"><img src="./images/albam.png" alt="albam"> アルバム</button>
        <button type="button" id="cameraTitle" class="bgc_select"><img src="./images/camera.png" alt="camera"> カメラ撮影</button>
      </div>
      <!-- カメラ撮影 -->
        <div id="cameraField" class="hide">
          <video id="camera" width="300" height="200"></video>
          <canvas id="picture" width="150" height="100" style="border: 1px red solid;"></canvas>
          <form method="POST">
            <button type="button" id="shutter"><img src="./images/camera.png" alt="camera"></button>
          </form>
          <audio id="se" preload="auto">
            <source src="" type="audio/mp3">
          </audio>
        </div>
        <div id="fileField">
        <label class="labelPhoto">
              <div class="upPhoto">
                画像データを選択
              </div>
            <input type="file" name="fname[]" class="cms-item" accept="image/*" multiple="multiple" onchange="loadImage(this);"></label>
            <input type="hiddin" name="fnameShadow" class="cms-item" accept="image/*" value="<?=$row['fname']?>">
          <p style="margin-bottom: 5px;">または、ここに画像をドロップしてください。</p>
        </div>
        <p id="preview"></p>
    </div>
</form>
<script>
   // アップロードするファイルを選択
   let tmp_files = [];
$('input[type=file]').change(function() {
  //選択したファイルを取得し、file変数に格納
  let file = $(this).prop('files')[0];
  
  // 画像以外は処理を停止
  if(!file.type.match('image.*')) {
    // クリア
    $(this).val(''); //選択されてるファイルを空にする
    $('.sample > img').html(''); //画像表示箇所を空にする
    return;
  }
  let reader = new FileReader(); 
  reader.onload = function() {   
    $('.sample > img').attr('src', reader.result);
  }
  reader.readAsDataURL(file);    
  });
  // 路線名が選択された時の駅名表示の変更
  $('#line').change(function () {
    const selectedStation = $('#line').val();
    $('#station').children().removeClass('hide');
    if($('#line').val()==selectedStation){
      $('#'+selectedStation).siblings().addClass('hide');
    }
    });
    $('#cameraTitle').on('click',function () {
      $('#cameraTitle').removeClass('bgc_select');
      $('#fileTitle').addClass('bgc_select');
      $('#fileField').addClass('hide');
      $('#cameraField').removeClass('hide');
      })
      $('#fileTitle').on('click',function () {
        $('#cameraTitle').addClass('bgc_select');
        $('#fileTitle').removeClass('bgc_select');
        $('#cameraField').addClass('hide');
        $('#fileField').removeClass('hide');
      });

      window.onload = () => {
      const video  = document.querySelector("#camera");
      const canvas = document.querySelector("#picture");
      const se     = document.querySelector('#se');

  /** カメラ設定 */
  const constraints = {
    audio: false,
    video: {
      width: 600,
      height: 400,
      facingMode: "user"
    }
  };

  // カメラの処理
  navigator.mediaDevices.getUserMedia(constraints)
  .then( (stream) => {
    video.srcObject = stream;
    video.onloadedmetadata = (e) => {
      video.play();
    };
  })
  .catch( (err) => {
    console.log(err.name + ": " + err.message);
  });
  // 空配列作成
   document.querySelector("#shutter").addEventListener("click", () => {
    const ctx = canvas.getContext("2d");

    // 演出的な目的で一度映像を止めてSEを再生する
    video.pause();  // 映像を停止
    se.play();      // シャッター音
    setTimeout( () => {
      video.play();    // 0.2秒後にカメラ再開
    }, 200);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    let image_url = canvas.toDataURL("image/png");
    let base64 = image_url.substr(image_url.indexOf(',') + 1);
    tmp_files.push(base64);
    console.log(tmp_files);
  });
};

// IMGの生成（プレビュー）
const js_arr = JSON.parse('<?= $json ?>');
console.log(js_arr);
let html="";
for(i=0; i<js_arr.length; i++){
      html += `
      <img class="preview_img" draggable="true" src="./img/${js_arr[i]}">
      `;
    }
    $("#preview").append(html);
function loadImage(obj){
  console.log(obj.files);
	for (i = 0; i < obj.files.length; i++) {
		let fileReader = new FileReader();
		fileReader.onload = (function (e) {
      // ここに枠を入れてあげる。
			document.getElementById('preview').innerHTML += '<img class="preview_img" draggable="true" src="' + e.target.result + '">';
		});
		fileReader.readAsDataURL(obj.files[i]);
	}
}

</script>
</body>
</html>
