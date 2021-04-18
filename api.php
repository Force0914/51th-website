<?php
session_start();
$link = mysqli_connect("127.0.0.1","admin","1234","51");
switch ($_GET['do']){
    case "login":
        $account = $_POST['account'];
        $password = $_POST['password'];
        $result = query("SELECT * FROM user WHERE account = '$account' AND password = '$password'");
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
        $result = query("SELECT * FROM code WHERE code = '$invitecode'");
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
        query("INSERT INTO question(name,invitecodemod,pcpage) VALUES ('".$_POST['title']."','".$_POST['invitecodemod']."',".$_POST['pcpage'].")");
        $nameid = mysqli_insert_id($link);
        $i = 0;
            if(isset($_POST['questions'])){
                if ($_POST['invitecodemod'] == "1"){
                        query("INSERT INTO code(questionid, code, cishu) VALUES ($nameid,'".$_POST['invitecode']."',".$_POST['questionnum'].")");
                }else{
                    for ( $i=1 ; $i<=$_POST['questionnum'] ; $i++ ) {
                            $num = str_pad($i,4,"0",STR_PAD_LEFT);
                            query("INSERT INTO code(questionid, code, cishu) VALUES ($nameid,'".$_POST['invitecode'].$num."',1)");
                    }
                }
                foreach ($_POST['questions'] as $key => $question){
                    if (str_replace(" ","",$question['desc']) != null){
                        $i++;
                        query("INSERT INTO questions(questionid,description, mode, item, options, required) VALUES ('".$nameid."','".$question['desc']."','".$question['mode']."','".$key."','".json_encode($question['options'],JSON_UNESCAPED_UNICODE)."','".$question['required']."')");
                    }
                };
                if ($i > 0){
                    echo "新增成功";
                }else{
                    query("DELETE FROM question WHERE id = $nameid");
                    echo "發生了意料之外的錯誤";
                }
            }else{
                query("DELETE FROM question WHERE id = $nameid");
                echo "發生了意料之外的錯誤";
            }
        break;
    case "deletequestion":
        query("DELETE FROM question WHERE id = ".$_POST['questionid']);
        echo "刪除成功";
        break;
    case "editquestion":
        if (isset($_POST['questionid'])){
            if(isset($_POST['questions'])) {
                query("UPDATE question SET pcpage=".$_POST['pcpage'].",name='".$_POST['title']."' WHERE id = ".$_POST['questionid']);
                if (isset($_POST['delete'])){
                    foreach ($_POST['delete'] as $deleteid){
                        query("DELETE FROM questions WHERE id = $deleteid");
                    }
                }
                foreach ($_POST['questions'] as $key => $question) {
                    if ($question['mode'] != "0" && str_replace(" ","",$question['desc']) != null){
                        if ($question['id'] != null) {
                            query( "UPDATE questions SET description='" . $question['desc'] . "',mode='" . $question['mode'] . "',item='$key',options='" . json_encode($question['options'], JSON_UNESCAPED_UNICODE) . "',required='" . $question['required'] . "' WHERE id = " . $question['id']);
                        } else {
                            query( "INSERT INTO questions(questionid,description, mode, item, options, required) VALUES ('" . $question['questionid'] . "','" . $question['desc'] . "','" . $question['mode'] . "','" . $key . "','" . json_encode($question['options'], JSON_UNESCAPED_UNICODE) . "','" . $question['required'] . "')");
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
        query("INSERT INTO question SELECT null, CONCAT('複製_',name), 1, pcpage, locked FROM question WHERE id = $questionid");
        $copyquestionid = mysqli_insert_id($link);
        query("INSERT INTO code(questionid, code, cishu) VALUES ($copyquestionid,'".$copyquestionid.time()."',0)");
        $copycodeid = mysqli_insert_id($link);
        query("INSERT INTO questions SELECT null, $copyquestionid, description, mode, item, options, required FROM questions WHERE questionid = $questionid");
        if ($_POST['copyans'] == "true"){
            $result = query("SELECT * FROM result WHERE questionid = $questionid");
            while ($resultrow = mysqli_fetch_assoc($result)){
                $resultid = $resultrow['id'];
                $copyquestionsid = array();
                $copyallans = array("ans" => array(),"elseans" => array());
                query("INSERT INTO result(questionid, codeid) VALUES ($copyquestionid,".$resultid.time().")");
                $copyresultid = mysqli_insert_id($link);
                $copyquestions = query("SELECT * FROM questions WHERE questionid = $copyquestionid ORDER BY item");
                $copyans = query("SELECT answer.* FROM answer,questions WHERE answer.resultid = $resultid AND answer.questionsid = questions.id ORDER BY questions.item");
                while ($copyquestionsrow = mysqli_fetch_assoc($copyquestions)){
                    array_push($copyquestionsid,$copyquestionsrow['id']);
                }
                while ($copyansrow = mysqli_fetch_assoc($copyans)){
                    array_push($copyallans['ans'],$copyansrow['ans']);
                    array_push($copyallans['elseans'],$copyansrow['elseans']);
                }
                foreach ($copyquestionsid as $key => $value){
                    query("INSERT INTO answer(resultid, questionsid, ans, elseans) VALUES ($copyresultid,$value,'{$copyallans['ans'][$key]}','{$copyallans['elseans'][$key]}')");
                }
            }
        }
        break;
    case "lock":
        $questionid = $_POST['questionid'];
        if ($_POST['lock'] == "false"){
            query("UPDATE question SET locked = 'true' WHERE id = $questionid");
        }else{
            query("UPDATE question SET locked = 'false' WHERE id = $questionid");
        }
        break;
    case "write":
        $questionid = $_POST['questionid'];
        $invitecode = $_POST['invitecode'];
        $result = query("SELECT * FROM code WHERE code = '$invitecode'");
        $row = mysqli_fetch_assoc($result);
        $invitecodeid = $row['id'];
        if ($row ['cishu'] == 1){
            query("UPDATE code SET cishu = -1 WHERE  code = '$invitecode'");
        }elseif ($row ['cishu'] > 1){
            query("UPDATE code SET cishu = ".($row ['cishu'] - 1)." WHERE  code = '$invitecode'");
        }
        query("INSERT INTO result(questionid,codeid) VALUES ($questionid,$invitecodeid)");
        $resultid = mysqli_insert_id($link);
        foreach ($_POST['questions'] as $key => $question) {
            $questionsid = $question['id'];
            $ans = "[\"未填答\"]";
            if (isset($question['ans'])){
                $ans = json_encode($question['ans'],JSON_UNESCAPED_UNICODE);
            }
            query("INSERT INTO answer(resultid, questionsid, ans) VALUES ($resultid,$questionsid,'$ans')");
            $ansid = mysqli_insert_id($link);
            if (isset($question['ans'])){
                if ($question['mode'] == 2 || $question['mode'] == 3){
                    if ($question['ans'][0] == true || $question['ans'][0] == "true"){
                            $elseans = $question['else'];
                        query("UPDATE answer SET elseans = '$elseans' WHERE id = $ansid");
                    }
                }
            }
        }
        echo "作答完成";
        break;
    case "savesession":
        $_SESSION['title'] = $_POST['title'];
        $_SESSION['num'] = $_POST['num'];
        $_SESSION['invitecodemod'] = $_POST['invitecodemod'];
        $_SESSION['invitecode'] = $_POST['invitecode'];
        $_SESSION['questionnum'] = $_POST['questionnum'];
        break;
    case "checkinvitecode":
        if ($_POST['invitecodemod'] == "1"){
            $coderesult = query("SELECT * FROM code WHERE code = '".$_POST['invitecode']."'");
            if (mysqli_num_rows($coderesult) > 0){
                echo $_POST['invitecode'];
                die();
            }else{
                echo "wedidit";
                die();
            }
        }else{
            for ( $i=1 ; $i<=$_POST['questionnum'] ; $i++ ) {
                $num = str_pad($i,4,"0",STR_PAD_LEFT);
                $coderesult = query("SELECT * FROM code WHERE code = '".$_POST['invitecode'].$num."'");
                if (mysqli_num_rows($coderesult) > 0){
                    echo $_POST['invitecode'].$num;
                    die();
                }else{
                    echo "wedidit";
                    die();
                }
            }
        }
        break;
    case "searchinvitecode":
        $result = query("SELECT * FROM code WHERE code ='".$_POST['invitecode']."'");
        if (mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $result2 = query("SELECT * FROM result WHERE questionid = ".$row['questionid']);
            if (mysqli_num_rows($result2) > 0){
                echo "true";
            }else{
                echo "目前尚無任何回答";
            }
        }else{
            echo "查無此邀請碼資料";
        }
        break;
    case "outputquestion":
        $questionid = $_POST['questionid'];
        $mode = array("未設定","是非題","單選題","多選題","問答題");
        $questionresult = query("SELECT * FROM question WHERE id = $questionid");
        $questionrow = mysqli_fetch_assoc($questionresult);
        $questionsresult = query("SELECT * FROM questions WHERE questionid = $questionid ORDER BY item");
        $result = query("SELECT * FROM result WHERE questionid = $questionid");
        $resultresult = query("SELECT * FROM result,answer,questions WHERE result.id = answer.resultid AND answer.questionsid = questions.id AND result.questionid = $questionid");
        $filename = $_POST['name'];
        $fp = fopen("./files/$filename.csv", 'w');
            $questionmode = array();
            fwrite($fp,"[問卷]\n");
            fwrite($fp,$questionrow['name']."\n");
            while ($questionsrow = mysqli_fetch_assoc($questionsresult)){
                $options = json_decode($questionsrow['options']);
                array_shift($options);
                array_unshift($options,$mode[$questionsrow['mode']],$questionsrow['description']);
                if ($questionsrow['mode'] == 1){
                    $options = array($mode[$questionsrow['mode']],$questionsrow['description'],"是","否");
                }elseif ($questionsrow['mode'] == 4){
                    $options = array($mode[$questionsrow['mode']],$questionsrow['description']);
                }
                array_push($questionmode,$questionsrow['mode']);
                fwrite($fp,implode(",",$options)."\n");
            }
            fwrite($fp,"[問卷結束]\n");
            fwrite($fp,"[填答]\n");
            $allans = array();
            $i = 0;
            $resultallrow = mysqli_fetch_all($result);
            while ($resultrow = mysqli_fetch_assoc($resultresult)) {
                $ansdata = array();
                $ans = json_decode($resultrow['ans']);
                $j = 0;
                foreach ($ans as  $value){
                    if ($value == "true"){
                        $value = $resultrow['elseans'];
                    }
                    $j++;
                    array_push($ansdata,str_replace(" ","&nbsp;",$value));
                }
                array_push($allans,implode(" ",$ansdata));
            }
            if (isset($allans[0])) fwrite($fp,implode(",",$allans)."\n");
            fwrite($fp,"[填答結束]");
            fclose($fp);
            echo true;
        break;
    case "inputquestion":
        $filename = explode(".",$_FILES["file"]["name"]);
        if ($filename[1] != "csv"){
            echo "格式錯誤,請重新上傳";
        }else{
             $csv = array();
             $questiondone = false;
             $questionid = 0;
             $codeid = 0;
             $resultid = 0;
             $i = 0;
             $j = 0;
             $questionsrow = array();
             $modes=array("是非題"=>1,"單選題"=>2,"多選題"=>3,"問答題"=>4);
             $fp = fopen($_FILES["file"]["tmp_name"],"r");
             while(!feof($fp)){
                 $i++;
                 $value = str_replace("\n","",fgets($fp));
                 if ($value == "[問卷]"){
                     $questiondone = false;
                 }
                 if ($value == "[問卷結束]"){
                     $questiondone = true;
                 }
                 if (!$questiondone){
                    if($i == 2){
                        query("INSERT INTO question(name, invitecodemod, pcpage) VALUES ('".$value."',1,1)");
                        $questionid = mysqli_insert_id($link);
                        query("INSERT INTO code(questionid, code, cishu) VALUES ($questionid,'".$questionid.time()."',0)");
                        $codeid = mysqli_insert_id($link);
                    }elseif($i >2){
                        $questions = explode(",",$value);
                        $mode = $modes[$questions[0]];
                        $description = $questions[1];
                        array_shift($questions);
                        array_shift($questions);
                        if ($mode == 1){
                            $questions = array("false","","","","","","");
                        }
                        if ($mode == 2 || $mode == 3){
                            array_unshift($questions,"true");
                        }
                        $questions = array_pad($questions, 7, "false");
                        $options = json_encode($questions,JSON_UNESCAPED_UNICODE);
                        query("INSERT INTO questions(questionid, description, mode, item, options, required) VALUES ($questionid,'$description',$mode,$j,'$options','false')");
                        $questionsrow[$i-3] = mysqli_insert_id($link);
                        $j++;
                    }
                 }
                 else{
                     if ($value == "[填答]"){
                        query("INSERT INTO result(questionid, codeid) VALUES ($questionid,$codeid)");
                        $resultid = mysqli_insert_id($link);
                     }
                     if ($value != "[問卷結束]" && $value != "[填答]" && $value != "[填答結束]"){
                         $value = $value;
                         $answer = explode(",",$value);
                         foreach ($answer as $anskey => $ans){
                             $ans = str_replace("&nbsp;"," ",explode(" ",$ans));
                             $elseans = null;
                             if ($ans[0] != "false" && $ans[0] != "是" && $ans[0] != "否"){
                                 $elseans = $ans[0];
                                 $ans[0] = "true";
                             }
                             $ans = json_encode($ans,JSON_UNESCAPED_UNICODE);
                             $questionsid = $questionsrow[$anskey];
                             query("INSERT INTO answer(resultid, questionsid, ans, elseans) VALUES ($resultid,$questionsid,'$ans','$elseans')");
                         }
                     }
                 }
             }
             fclose($fp);
             echo "問卷輸入成功";
        }
        break;
}
function query($query){
    global $link;
    return mysqli_query($link,$query);
}