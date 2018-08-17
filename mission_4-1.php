<?php 
$edit_val = "";
$name_val ="";
$comment_val ="";
$pass_val ="";

$date = date(Y."-".m."-".d."-".H."-".i."-".s);

//$name = $_POST["name"];
//$comment = $_POST["comment"];

//DB接続
try {
    $dsn = 'mysql:host=localhost;dbname=データベース名';
    $user = 'ユーザー名';
    $pass = 'パスワード';
    $pdo = new PDO(
        $dsn,$user,$pass,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//エラー投げる
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );
    
    //テーブル削除
    //$sql = "DROP TABLE IF EXISTS table4";
    //$pdo -> exec($sql);
    
    //テーブル作成
    $DB_table_name = "table4";
    $create_query = '
    CREATE TABLE IF NOT EXISTS '.$DB_table_name.'(
    id INT NOT NULL AUTO_INCREMENT primary key, 
    name CHAR(32) NOT NULL, 
    comment TEXT NOT NULL,
    date DATETIME NOT NULL,
    password TEXT NOT NULL
    )'; 
    $create_table = $pdo -> prepare($create_query);
    $create_table -> execute();
    
    //削除パス判定
    if(!empty($_POST["delete_pass"]) && !empty($_POST["delete_num"]) && ctype_digit($_POST["delete_num"])){
        //削除行の情報取得
        $delete_pass = $_POST["delete_pass"];
        $delete_num = $_POST["delete_num"];
        $select_sql ="SELECT * FROM table4 where id=$delete_num";
        $select_result = $pdo->query($select_sql);
        $sel_result = $select_result->fetch(PDO::FETCH_NUM);
        
        //パスが一致してたら削除実行
        if($sel_result[4] == $delete_pass){
            $delete_sql = "delete from table4 where id=$delete_num";
            $delete_result = $pdo->query($delete_sql);
        }elseif($sel_result[4] != $delete_pass){
            echo "削除のパスワードが違います";
        }
    }
    
    //編集パス判定
    if(!empty($_POST["edit_pass"]) && !empty($_POST["edit_num"]) && ctype_digit($_POST["edit_num"])){
        $edit_pass = $_POST["edit_pass"];
        $edit_num = $_POST["edit_num"];
        unset($select_sql);
        unset($select_result);
        unset($sel_result);
        $select_sql ="SELECT * FROM table4 where id=$edit_num";
        $select_result = $pdo->query($select_sql);
        $sel_result = $select_result->fetch(PDO::FETCH_NUM);
        
        //パスが一致していたら編集内容をフォームに表示
        if($sel_result[4] == $edit_pass){
            $edit_val = $sel_result[0];
            $name_val = $sel_result[1];
            $comment_val = $sel_result[2];
            $pass_val = $sel_result[4];
        }elseif($sel_result[4] != $edit_pass){
            echo "編集のパスワードが違います";
        }
    }
    
    //編集機能
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $password = $_POST["passward"];
    $edit = $_POST["edit"];
    if(!empty($_POST["edit"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["passward"])){
        
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $password = $_POST["passward"];
        $edit = $_POST["edit"];
        
        $update_sql = "update table4 set name='$name' , comment='$comment' , date='$date' , password='$password' where id=$edit";
        $update_result = $pdo->query($update_sql);
    }elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["passward"])){
        //投稿機能
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $password = $_POST["passward"];
        
        //DBに追加
        $add_sql = $pdo->prepare("INSERT INTO table4(name,comment,date,password)VALUES(:name,:comment,:date,:password)");
        $add_sql->bindParam(':name',$name,PDO::PARAM_STR);
        $add_sql->bindParam(':comment',$comment,PDO::PARAM_STR);
        $add_sql->bindParam(':date',$date,PDO::PARAM_STR);
        $add_sql->bindParam(':password',$password,PDO::PARAM_STR);
        $add_sql->execute();
    }

    //DB内容取得
        unset($select_sql);
        unset($select_result);
        unset($sel_result);
        $select_sql ='SELECT * FROM table4 ORDER BY id';
        $select_result = $pdo->query($select_sql);
        $sel_result = $select_result->fetchAll(PDO::FETCH_NUM);

} catch (PDOException $e) {
    echo $e->getMessage()." - ".$e->getLine().PHP_EOL;

}
?>

<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>4-1</title>
    </head>
    <body>
        <form action="mission_4-1.php" method="post">
            <input type="text" name="edit" value="<?=$edit_val;?>" placeholder="後で隠す数字">
            <input type="text" name="name" value="<?=$name_val;?>" placeholder="名前"><br>
            <input type="text" name="comment" value="<?=$comment_val;?>" placeholder="コメント"><br>
            <input type="text" name="passward" value="<?=$pass_val;?>" placeholder="パスワード"><br>
            <input type="submit" value="送信！">
        </form>
        <form action="mission_4-1.php" method="post">
            <input type="text" name="delete_num" placeholder="削除番号"><br>
            <input type="text" name="delete_pass" placeholder="削除パス"><br>
            <input type="submit" value="削除！">
        </form>
        <form action="mission_4-1.php" method="post">
            <input type="text" name="edit_num" placeholder="編集番号"><br>
            <input type="text" name="edit_pass" placeholder="編集パス"><br>
            <input type="submit" value="編集！">
        </form>
        <?php 
        //ブラウザ表示用
        foreach((array)$sel_result as $key1 => $val1){
            foreach( $val1 as $key2 => $val2 ){
                echo $val2." ";
            }
            echo "<br>";
        }
        ?>
    </body>
</html>
    