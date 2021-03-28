<?php
session_start();
$link = mysqli_connect("127.0.0.1","admin","1234","51");
switch ($_GET['do']){
    case "login":
        $account = $_POST['account'];
        $password = $_POST['password'];
        $result = mysqli_query($link,"SELECT * FROM user WHERE account = '$account' AND password = '$password'");
        if (mysqli_num_rows($result) >= 1){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['account'] = $account;
            $_SESSION['password'] = $password;
            $_SESSION['userid'] = $row['id'];
            echo "admin";
        }else{
            echo "帳號密碼錯誤";
        }
        break;
    case "run":
        $invitecode = $_POST['invitecode'];
        $result = mysqli_query($link,"SELECT * FROM code WHERE code = '$invitecode'");
        $row = mysqli_fetch_assoc($result);
        if(mysqli_num_rows($result) >= 1){
            if ($row['cishu'] >= 0){
                echo "run";
            }else{
                echo "您好！本問卷已達所需之參考數量，感謝您的支持";
            }
        }else{
            echo "查無此邀請碼 或 此邀請碼已失效";
        }
        break;
    case "creatquestion":
        mysqli_query($link,"INSERT INTO question(name,invitecodemod,pcpage) VALUES ('".$_POST['title']."','".$_POST['invitecodemod']."',".$_POST['pcpage'].")");
        $nameid = mysqli_insert_id($link);
        $i = 0;
            if(isset($_POST['questions'])){
                if ($_POST['invitecodemod'] == "1"){
                        mysqli_query($link,"INSERT INTO code(questionid, code, cishu) VALUES ($nameid,'".$_POST['invitecode'][0]."',".$_POST['questionnum'].")");
                }else{
                    for ( $i=0 ; $i<$_POST['questionnum'] ; $i++ ) {
                            mysqli_query($link,"INSERT INTO code(questionid, code, cishu) VALUES ($nameid,'".$_POST['invitecode'][$i]."',1)");
                    }
                }
                foreach ($_POST['questions'] as $key => $question){
                    if ($question['mode'] != "0" && str_replace(" ","",$question['desc']) != null){
                        $i++;
                        mysqli_query($link,"INSERT INTO questions(questionid,description, mode, item, options, required) VALUES ('".$nameid."','".$question['desc']."','".$question['mode']."','".$key."','".json_encode($question['options'],JSON_UNESCAPED_UNICODE)."','".$question['required']."')");
                    }
                };
                if ($i > 0){
                    echo "新增成功";
                }else{
                    mysqli_query($link,"DELETE FROM question WHERE id = $nameid");
                    echo "發生了意料之外的錯誤";
                }
            }else{
                mysqli_query($link,"DELETE FROM question WHERE id = $nameid");
                echo "發生了意料之外的錯誤";
            }
        break;
    case "deletequestion":
        mysqli_query($link,"DELETE FROM question WHERE id = ".$_POST['questionid']);
        echo "刪除成功";
        break;
    case "editquestion":
        if (isset($_POST['questionid'])){
            if(isset($_POST['questions'])) {
                mysqli_query($link,"UPDATE question SET pcpage=".$_POST['pcpage'].",name='".$_POST['title']."' WHERE id = ".$_POST['questionid']);
                if (isset($_POST['delete'])){
                    foreach ($_POST['delete'] as $deleteid){
                        mysqli_query($link,"DELETE FROM questions WHERE id = $deleteid");
                    }
                }
                foreach ($_POST['questions'] as $key => $question) {
                    if ($question['mode'] != "0" && str_replace(" ","",$question['desc']) != null){
                        if ($question['id'] != null) {
                            mysqli_query($link, "UPDATE questions SET description='" . $question['desc'] . "',mode='" . $question['mode'] . "',item='$key',options='" . json_encode($question['options'], JSON_UNESCAPED_UNICODE) . "',required='" . $question['required'] . "' WHERE id = " . $question['id']);
                        } else {
                            mysqli_query($link, "INSERT INTO questions(questionid,description, mode, item, options, required) VALUES ('" . $question['questionid'] . "','" . $question['desc'] . "','" . $question['mode'] . "','" . $key . "','" . json_encode($question['options'], JSON_UNESCAPED_UNICODE) . "','" . $question['required'] . "')");
                        }
                    }
                }
                echo "編輯成功";
            }else{
                echo "發生了意料之外的錯誤";
            }
        }else{
            echo "發生了意料之外的錯誤";
        }
        break;
    case "copyquestion":
        $questionid = $_POST['questionid'];
        mysqli_query($link,"INSERT INTO question SELECT null, CONCAT('複製_',name), invitecodemod, pcpage, locked FROM question WHERE id = $questionid");
        $copyquestionid = mysqli_insert_id($link);
        mysqli_query($link,"INSERT INTO questions SELECT null, $copyquestionid, description, mode, item, options, required FROM questions WHERE questionid = $questionid");
        if ($_POST['copyans']){
            mysqli_query($link,"INSERT INTO result SELECT null, $copyquestionid FROM result WHERE questionid = $questionid");
            $copyresultid = mysqli_insert_id($link);
            mysqli_query($link,"INSERT INTO answer SELECT null, answer.resultid, $copyresultid, answer.ans, answer.elseans FROM result,answer WHERE answer.resultid = result.id AND result.questionid = $questionid");
        }
        break;
    case "lock":
        $questionid = $_POST['questionid'];
        if ($_POST['lock'] == "false"){
            mysqli_query($link,"UPDATE question SET locked = 'true' WHERE id = $questionid");
        }else{
            mysqli_query($link,"UPDATE question SET locked = 'false' WHERE id = $questionid");
        }
        break;
    case "write":
        $questionid = $_POST['questionid'];
        $invitecode = $_POST['invitecode'];
        $result = mysqli_query($link,"SELECT * FROM code WHERE code = '$invitecode'");
        $row = mysqli_fetch_assoc($result);
        if ($row ['cishu'] == 1){
            mysqli_query($link,"UPDATE code SET cishu = -1 WHERE  code = '$invitecode'");
        }elseif ($row ['cishu'] > 1){
            mysqli_query($link,"UPDATE code SET cishu = ".($row ['cishu'] - 1)." WHERE  code = '$invitecode'");
        }
        mysqli_query($link,"INSERT INTO result(questionid) VALUES ($questionid)");
        $resultid = mysqli_insert_id($link);
        foreach ($_POST['questions'] as $key => $question) {
            $questionsid = $question['id'];
            $ans = "未填答";
            if (isset($question['ans'])){
                $ans = json_encode($question['ans'],JSON_UNESCAPED_UNICODE);
            }
            mysqli_query($link,"INSERT INTO answer(resultid, questionsid, ans) VALUES ($resultid,$questionsid,'$ans')");
            $ansid = mysqli_insert_id($link);
            if (isset($question['ans'])){
                if ($question['mode'] == 2 || $question['mode'] == 3){
                    if ($question['ans'][0] == true || $question['ans'][0] == "true"){
                            $elseans = $question['else'];
                        mysqli_query($link,"UPDATE answer SET elseans = '$elseans' WHERE id = $ansid");
                    }
                }
            }
        }
        echo "作答完成";
        break;
    case "checkinvitecode":
        if ($_POST['invitecodemod'] == "1"){
            $coderesult = mysqli_query($link,"SELECT * FROM code WHERE code = '".$_POST['invitecode'][0]."'");
            if (mysqli_num_rows($coderesult) > 0){
                echo $_POST['invitecode'][0];
                die();
            }else{
                echo "wedidit";
                die();
            }
        }else{
            for ( $i=0 ; $i<$_POST['questionnum'] ; $i++ ) {
                $coderesult = mysqli_query($link,"SELECT * FROM code WHERE code = '".$_POST['invitecode'][$i]."'");
                if (mysqli_num_rows($coderesult) > 0){
                    echo $_POST['invitecode'][$i];
                    die();
                }else{
                    echo "wedidit";
                    die();
                }
            }
        }
        break;
    case "savesession":
        $_SESSION['title'] = $_POST['title'];
        $_SESSION['num'] = $_POST['num'];
        $_SESSION['invitecodemod'] = $_POST['invitecodemod'];
        $_SESSION['invitecode'] = $_POST['invitecode'];
        $_SESSION['questionnum'] = $_POST['questionnum'];
        break;
}