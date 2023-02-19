# stationDB_PHP

### 1．プロダクトについて
鉄道構造物の損傷状態を管理できるアプリ  

### 2.作成した理由  
前職で駅の点検を行っており、現地で紙に記入して事務所に戻りPC入力することが大変面倒だったため  
現地で写真と一緒にテキスト情報を保存できれば、業務効率化できると考え作成いたしました。  
これまでは、写真とテキスト情報が別ファイルに保存していたが、一元管理することで上司への報告や  
情報共有もより便利になると考えております。  
  
### 3．機能について  
・MySqlを使用したCRUD操作の実装  
・登録データ検索機能を実装  
・複数ファイルの登録・表示を実装  
・ログイン機能の実装およびパスワードのhash化  
   
### 4．工夫した点  
・検索機能はAjax通信による非同期通信を行なっているため、画面をリロードすることなく  
 DBからデータを取得しております。通常のGET通信よりも処理を早く表示することができています。  
・登録画像が複数ある場合、画像をクリックしていただくとJavaScriptのプラグイン(Slick)を使用して  
 スライダーで表示できるようにしております。  
・ログイン機能を実装しており、メール登録日時から24時間以内かつURLトークンが合致した場合にのみ  
 登録できるようしております。  
