<?php
	// DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザ名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, 
	        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //データベースにテーブルの作成
    //もし、mission5というテーブルが存在しなかったら作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
	    . "comment TEXT,"
	    . "date char(32),"
	    . "password TEXT"
	    .");";
	$stmt = $pdo->query($sql);
	
    $date = date("Y/m/d H:i:s");

    $submit_btn = $_POST["submit_btn"]; //送信ボタン
    $delete_btn = $_POST["delete_btn"]; //削除ボタン
    $edit_btn = $_POST["edit_btn"];     //編集ボタン
    $name = $_POST["name"];             //名前入力
    $comment = $_POST["comment"];       //コメント入力
    $delete_num = $_POST["delete_num"]; //削除番号
    $edit_post = $_POST["edit_post"];   //編集番号(プログラムが入力)
    $edit_num = $_POST["edit_num"];     //編集番号(自分で入力)
    $submit_password = $_POST["submit_password"];   //送信ボタンでのパスワード
    $delete_password = $_POST["delete_password"];   //削除ボタンでのパスワード
    $edit_password = $_POST["edit_password"];       //編集ボタンでのパスワード
	
	if ($submit_btn != ""){
	    //編集モード
	    if ($edit_post != ""){  //編集番号(プログラムが入力)されたものが入っているとき
            if ($name != "" && $comment != ""){
        	    $id = $edit_post; //変更する投稿番号
            	$sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,
            	            password=:password WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
            	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
            	$stmt->bindParam(':password', $submit_password, PDO::PARAM_STR);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	$stmt->execute();
            	$edit_post = ""; //編集番号を削除する
            }
        }
        //新規入力モード
        else{
            if ($name != "" && $comment != ""){
                //データの入力
                $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, password) 
                                                VALUES (:name, :comment, :date, :password)");
    	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    	        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    	        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    	        $sql -> bindParam(':password', $submit_password, PDO::PARAM_STR);
    	        $sql -> execute();
            }
        }
    }
    
    //削除
    if ($delete_btn != ""){     //削除ボタンに何か入っているとき
        if ($delete_num != "" && $delete_password != ""){
            $id = $delete_num;
            //一部のみ表示
            $sql = 'SELECT * FROM mission5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();                             
            $results = $stmt->fetchAll(); 
            foreach ($results as $row){
                //passwordが等しければ削除
            	if($row['password'] == $delete_password){
                	$sql = 'delete from mission5 where id=:id';
                	$stmt = $pdo->prepare($sql);
                	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
                	$stmt->execute();
            	}
            }
        }
    }
    
    //編集
    if ($edit_btn != ""){       //編集ボタンに何か入っているとき
        if ($edit_num != "" && $edit_password != ""){
            $id = $edit_num;
            $sql = 'SELECT * FROM mission5';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	            if($row['id'] == $id && $row['password'] == $edit_password){
                    $edit_post = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
	            }
	        }
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
    
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>

<style>
        body{
            margin: 50px 100px;
            padding:20px
        }
        h1{
            text-align:center;
        }
        h3{
            text-align:center;
        }
        p{
            text-align:left;
            display: inline-block;
        }
        div{
            text-align:center;
        }
        div.div{
            text-align:left;
            display: inline-block;
        }
        tr.button{
            text-align:center;
        }
</style>

<body>
    <h1>好きな食べ物(DB仕様版)</h1>
    <!--tableを使って、縦並びに配列-->
    <form action="" method="post">
        <h3>【新規・編集フォーム】</h3>
        <table align="center">
            <tr>
                <input type="hidden" name="edit_post"
                    value="<?php echo $edit_post; ?>">
            </tr>    
            <tr>
                <td>名前：<input type="str" name="name" placeholder="名前" 
                    value="<?php echo $edit_name; ?>"></td>
            </tr>
            <tr>
                <td>好きな食べ物：<input type="str" name="comment" placeholder="好きな食べ物" 
                        value="<?php echo $edit_comment; ?>"></td>
            </tr>
            <tr>
                <td>パスワード：
                        <input type="str" name="submit_password" placeholder="パスワード"></td>
            </tr>
            <tr class="button">
                <td><input type="submit" name="submit_btn"></td>
            </tr>
        </table>
        <div>
            <p>
                ・名前と好きな食べ物を登録しないと反映されません。</br>
                ・パスワードを登録しないと、削除・編集ができません。
            </p>
        </div>
        
        <br>
        
        <h3>【削除】</h3>
        <table align="center">
            <tr>
                <td>削除したい番号：
                        <input type="number" name="delete_num" min="1" placeholder="削除番号"></td>
            </tr>
            <tr>        
                <td>登録したパスワード：
                        <input type="str" name="delete_password" placeholder="パスワード"></td>
            </tr>
            <tr class="button">        
                <td><input type="submit" name="delete_btn" value="削除"></td>
        </table>
        
        <br>
        
        <h3>【編集】</h3>
        <table align="center">
            <tr>
                <td>編集したい番号：
                        <input type="number" name="edit_num" min="1" placeholder="編集番号"></td>
            </tr>
            <tr>
                <td>登録したパスワード：
                        <input type="str" name="edit_password" placeholder="パスワード"></td>
            </tr>
            <tr class="button">
                <td><input type="submit" name="edit_btn" value="編集"></td>
            </tr>
        </table>
        <div>
            <p>編集ボタンを押した後<br>
            ・新規・編集フォームから編集して送信してください。<br>
            ・パスワードは新しく入力してください。</p>
        </div>
    </form>
    
    <h3>【投稿された文章】</h3>
    <div>
        <div class="div">
            <?php
                $sql = 'SELECT * FROM mission5';
        	    $stmt = $pdo->query($sql);
        	    $results = $stmt->fetchAll();
        	    foreach ($results as $row){
        		    //$rowの中にはテーブルのカラム名が入る
        		    echo $row['id'].',';
        		    echo $row['name'].',';
        		    echo $row['comment'].',';
        		    echo $row['date'].'<br>';
        	    echo "<hr>";
        	    }
            ?>
        </div>
    </div>
</body>
</html>