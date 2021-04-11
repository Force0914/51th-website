<!doctype html>
<html lang="zh_tw">
<head>
    <?php include("head.php");?>
    <script src="js/jquery.min.js" charset="utf-8"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/vue.js"></script>
    <title>網路民意調查系統</title>
</head>
<body class="txt-center">
<div id="app" class="container">
    <h1>手機問卷管理系統 - 問卷統計</h1>
    <div class="btn-group">
        <input type="button" class="btn" value="返回" @click="admin()">
        <input type="button" class="btn" value="問卷輸入" @click="inputquestion()">
    </div><br><br>
    <div class="btn-group">
        <input list="list" @keypress.enter="search(invitecode)" v-model="invitecode" placeholder="以邀請碼查詢填答內容">
        <datalist id="list">
            <option v-for="(value,index) in invitecoderow" :value="value[2]">
        </datalist>
        <input type="button" class="btn" value="查詢" @click="search(invitecode)">
    </div><br><br>
    <table class="table">
        <thead>
        <tr>
            <th>編號</th>
            <th>問卷名稱</th>
            <th>已填寫數量</th>
            <th>功能</th>
        </tr>
        </thead>
        <?php
        $link = mysqli_connect("127.0.0.1","admin","1234","51");
        $result = mysqli_query($link,"SELECT * FROM question");
        $lock = array();
        if (mysqli_num_rows($result) >=1){
            ?>
            <tbody>
            <?php
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)){
                $i++;
                $id = $row['id'];
                $name = $row['name'];
                $cresult = mysqli_query($link,"SELECT * FROM code");
                $crow = mysqli_fetch_all($cresult);
                $aresult = mysqli_query($link,"SELECT COUNT(*) FROM result WHERE questionid = $id");
                $count = mysqli_fetch_assoc($aresult);
                $count = $count['COUNT(*)'];
                echo "<tr>
                                    <td>$i</td>
                                    <td>$name</td>
                                    <td>$count 份</td>
                                    <td>
                                        <div class='btn-group'>
                                            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>動作 <span class='caret'></span></a>
                                            <ul class='dropdown-menu'>
                                                <li><a @click='statisticsquestion($id)'>統計結果</a></li>
                                                <li class='divider'></li>
                                                <li><a @click='copyquestion($id)'>複製</a></li>
                                                <li class='divider'></li>
                                                <li><a @click='deletequestion($id)'>刪除</a></li>
                                                <li class='divider'></li>
                                                <li><a @click='outputquestion(`$name`,$id)'>問卷輸出</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    </tr>";
            }
            ?>
            </tbody>
            <?php
        }
        ?>
    </table>
</div>
<script>
    new Vue({
        el:'#app',
        data(){
            return{
                title: null,
                num: null,
                invitecoderow: <?=json_encode($crow)?>,
                invitecode: null
            }
        },
        methods:{
            statisticsquestion(questionid){
                location.href = `statistics.php?id=${questionid}`
            },
            outputquestion(name,questionid){
                $.post(`api.php?do=outputquestion`,{name,questionid},function (a){
                    if (a){
                        location.href = `./files/${name}.csv`
                    }
                })
            },
            inputquestion(questionid){
                $.post(`api.php?do=inputquestion`,{questionid},function (a){})
            },
            search(invitecode){
                $.post(`api.php?do=searchinvitecode`,{invitecode},function (a){
                    if (a == "查無此邀請碼資料"){
                        alert(a)
                    }else{
                        a.splice(",")
                        location.href = ""
                    }
                })
            },
            deletequestion(questionid){
                $.post(`api.php?do=deletequestion`,{questionid},function (a){
                    alert(a);
                    history.go(0)
                })
            },
            admin(){
                location.href = "admin.php";
            },
            copyquestion(questionid){
                let copyans = false
                if (confirm("是否要連答案一起複製")){
                    copyans = true
                }
                $.post(`api.php?do=copyquestion`,{questionid,copyans},function (a){
                    alert("複製成功")
                    history.go(0)
                })
            }
        }
    })
</script>
</body>
</html>