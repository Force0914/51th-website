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
<!--        <label class="btn" style="float: right"><form style="display: none" action="api.php?do=inputquestion" method="POST"><input type="file" accept=".csv"></form>問卷輸入</label>-->
        <label class="btn" style="float: right"><input id="files" type="file" accept=".csv" style="display: none" @change="inputquestion($event)">問卷輸入</label>
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
        $crow = array();
        $countarray = array("");
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
                array_push($countarray,intval($count));
                echo "<tr>
                                    <td>$i</td>
                                    <td>$name</td>
                                    <td>$count 份</td>
                                    <td>
                                        <div class='btn-group'>
                                            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>動作 <span class='caret'></span></a>
                                            <ul class='dropdown-menu'>
                                                <li><a @click='statisticsquestion($i,$id)'>統計結果</a></li>
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
                invitecode: null,
                countarray: <?=json_encode($countarray)?>,
            }
        },
        methods:{
            statisticsquestion(i,questionid){
                console.log(i)
                if (this.countarray[i] > 0){
                    location.href = `statistics.php?id=${questionid}`
                }else{
                    alert("目前尚無任何回答")
                }
            },
            outputquestion(name,questionid){
                $.post(`api.php?do=outputquestion`,{name,questionid},function (a){
                    if (a){
                        location.href = `./files/${name}.csv`
                    }
                })
            },
            inputquestion(event){
                const formdata = new FormData()
                formdata.append('file', event.target.files[0])
                $.ajax({
                    url: 'api.php?do=inputquestion',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: formdata
                }).then(a => {
                    alert(a)
                    history.go(0)
                })
            },
            search(invitecode){
                $.post(`api.php?do=searchinvitecode`,{invitecode},function (a){
                    if (a == "true"){
                        location.href = `search.php?code=${invitecode}`;
                    }else{
                        alert(a);
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
                let copyans = confirm("是否要連答案一起複製")
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