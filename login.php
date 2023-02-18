<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <script src="./js/jquery-2.1.3.min.js"></script>
    <title>Document</title>
</head>
<body>
    <form method="post" action="login_act.php" class="form">
       <h2>駅点検DB</h2>
         <fieldset>
           <label><p style="width:100px; font-size:20px; margin-bottom:3px; margin-left:5px;">Mail</p>
           <input type="mail" name="mail" placeholder='Mail' id="textmail" style="width:400px;"></label>
           <label><p style="width:100px; font-size:20px; margin-bottom:3px; margin-left:5px;">PassWord</p>
           <input type="password" name="pw" id="textPassword" placeholder='PassWord'  style="width:400px;">
           <span id="buttonEye" class="fa fa-eye" style="margin-right:5px;" onclick="pushHideButton()"></span>
           </label><br>
           <input type="submit" value="ログイン" class="login"><br>
           <a href=""><button type="button" class="Browse">閲覧専用</button></a>
           <p style="text-align:center; font-size:20px; width:420px;"><a href="./signup_mail.php">初めての方はこちらから</a></p>
          </fieldset>
      </form>    
      <script>
      function pushHideButton() {
        var txtPass = document.getElementById("textPassword");
        var btnEye = document.getElementById("buttonEye");
        if (txtPass.type === "text") {
          txtPass.type = "password";
          btnEye.className = "fa fa-eye";
        } else {
          txtPass.type = "text";
          btnEye.className = "fa fa-eye-slash";
        }
      }
      </script>
</body>
</html>