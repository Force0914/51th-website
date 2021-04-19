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
    <h1>手機問卷管理系統</h1>
    <a class="btn" style="position: absolute;top: 15px;right: 20px;" @click="logout()">登出</a>
    <div class="btn-group">
        <input type="button" class="btn" value="新增問卷" @click="showModal('modal')">
        <input type="button" class="btn" value="問卷統計" @click="statistics()">
    </div>
    <br><br>
    <table class="table">
        <thead>
        <tr>
            <th>編號</th>
            <th>問卷名稱</th>
            <th>功能</th>
            <th>鎖定</th>
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
                            if ($row['locked'] == "true"){
                                $row['locked'] = true;
                            }else{
                                $row['locked'] = false;
                            }
                            $lock[$id] = $row['locked'];
                            echo "<tr>
                                    <td>$i</td>
                                    <td>$name</td>
                                    <td>
                                        <a class='btn btn-primary' :disabled='this.lock[$id]' @click='editquestion($id)'>修改</a>
                                    </td>
                                    <td>
                                        <input type='checkbox' class='ios' id='checkbox-1' v-model='lock[$id]' @click='lockquestion($id)'>
                                    </td>
                                    </tr>";
                        }
                    ?>
                    </tbody>
        <?php
                }
            ?>
    </table>
    <div id="modal" class="modal hide fade">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3 id="myModalLabel">新增問卷</h3>
        </div>
        <form action="question.php" method="POST" @submit.prevent="submit()">
            <div class="modal-body">
                <label>問卷名稱：<input type="text" name="title" v-model="title" required></label>
                <label>　題數：<input type="number" name="num" min="1" v-model="num" required></label><br><br>
                <label v-if="invitecodemod == 1">問卷所需數量：<input type="number" name="questionnum" min="0" v-model.number="questionnum" required>　( 數字填 0 即為無限 )</label>
                <label v-if="invitecodemod == 2">問卷所需數量：<input type="number" name="questionnum" min="1" v-model.number="questionnum" required>　( 最小值為 1 )</label><br><br>
                <p>邀請碼：　</p>
                <label><input type="radio" value="1" name="invitecodemod" v-model="invitecodemod">共用邀請碼</label>
                <label><input type="radio" value="2" name="invitecodemod" v-model="invitecodemod">獨立邀請碼</label><br><br>
                <div>
                    <label><input v-model="invitecode" name="invitecode[]" type="text" required></label>
                    <p v-if="invitecodemod == 2 &&　invitecode != null">　　{{ invitecode }}0001 ~ {{ invitecode }}{{ numnum(questionnum) }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn" data-dismiss="modal" value="取消">
                <input type="submit" class="btn btn-primary" value="確定">
            </div>
        </form>
    </div>
</div>
<script>
    new Vue({
        el:'#app',
        data(){
            return{
                title: null,
                num: null,
                invitecode: null,
                invitecodemod: "1",
                lock: <?=json_encode($lock);?>,
                questionnum: 1
            }
        },
        methods:{
            logout(){
                alert("登出成功");
                location.href = "index.php";
            },
            statistics(){
                location.href = "statisticslist.php";
            },
            submit(){
                const _this = this
                $.post(`api.php?do=checkinvitecode`,{
                    questionnum: this.questionnum,
                    invitecodemod: this.invitecodemod,
                    invitecode: this.invitecode
                },function (a){
                    if(a == "wedidit"){
                        $.post(`api.php?do=savesession`,_this.$data,function (b){
                            location.href = "question.php"
                        })
                    }else{
                        alert(`此邀請碼已存在，請更換邀請碼 => ${a}`)
                    }
                })
            },
            lockquestion(questionid){
                $.post(`api.php?do=lock`,{
                    questionid:questionid,
                    lock: this.lock[questionid]
                },function (a){})
            },
            editquestion(questionid){
                if (!this.lock[questionid])
                    location.href = `editquestion.php?id=${questionid}`;
            },
            showModal(modalid){
                $(`#${modalid}`).modal('show')
            },
            create(){
                if (!this.title) {
                    alert("問卷名稱不得為空")
                    return
                }
                if (!this.num) {
                    alert("題數不得為空")
                    return
                }
            },
            numnum(num){
                if(num < 10){
                    num = `000${num}`
                }else if(num >=10 && num < 100){
                    num = `00${num}`
                }else if(num >=100 && num < 1000){
                    num = `0${num}`
                }
                return num
            }
        }
    })
</script>
</body>
</html>