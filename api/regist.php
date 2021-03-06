<?php 
    require_once '../db.php';
    //post解析
    $Post = json_decode(file_get_contents('php://input'), true);
    $stmt = db_func::db_q("SELECT * FROM `user` WHERE `account`=?");
    $stmt->bindParam(1,$Post["account"]);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_CLASS);
    $flag = 0;//0帳號不存在，1存在
    foreach($result as $value){
        if($value->account!=""){
            $flag = 1;
        }
    }
    if($flag==1){
        //echo '已有登入帳號';
        echo json_encode(0);
    }
    else{
        $conn = ConnectDB::getConnection();
        $insertuser = $conn->prepare("INSERT INTO `user`(`account`, `password`, `email`) VALUES ('{$Post["account"]}','{$Post["password"]}','{$Post["email"]}')");
        $insertuser->execute();
        //每個使用者都要有一個outfolder
        $userid = $conn->lastInsertId();
        $insertfolder = db_func::db_q("INSERT INTO `folder`(`isOutFolder`,`createUserId`) VALUES (1,'{$userid}')");
        $insertfolder->execute();
        //echo '註冊成功';
        echo json_encode(1);
    }
?>