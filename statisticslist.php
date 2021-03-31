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
    <div class="btn-group">
        <input type="button" class="btn" value="新增問卷" @click="showModal('modal')">
        <input type="button" class="btn" value="問卷統計">
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>編號</th>
            <th>問卷名稱</th>
            <th>填寫數量</th>
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
                $aresult = mysqli_query($link,"SELECT COUNT(*) FROM result WHERE questionid = $id");
                $count = mysqli_fetch_assoc($aresult);
                $count = $count['COUNT(*)'];
                if ($row['locked'] == "true"){
                    $row['locked'] = true;
                }else{
                    $row['locked'] = false;
                }
                $lock[$id] = $row['locked'];
                echo "<tr>
                                    <td>$i</td>
                                    <td>$name</td>
                                    <td>$count 份</td>
                                    <td>
                                        <div class='btn-group'>
                                            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>動作 <span class='caret'></span></a>
                                            <ul class='dropdown-menu'>
                                                <li><a @click='editquestion($id)'>修改</a></li>
                                                <li class='divider'></li>
                                                <li><a @click='copyquestion($id)'>複製</a></li>
                                                <li class='divider'></li>
                                                <li><a @click='deletequestion($id)'>刪除</a></li>
                                            </ul>
                                        </div>
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
    <div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">新增問卷</h3>
        </div>
        <form action="question.php" method="POST" @submit.prevent="submit()">
            <div class="modal-body">
                <label>問卷名稱：<input type="text" name="title" v-model="title" required></label>
                <label>　題數：<input type="number" name="num" min="1" v-model="num" required></label><br><br>
                <label v-if="invitecodemod == 1">問卷所需數量：<input type="number" name="questionnum" min="0" v-model.number="questionnum" required>　( 數字填 0 即為無限 )</label>
                <label v-if="invitecodemod == 2">問卷所需數量：<input type="number" name="questionnum" min="1" v-model.number="questionnum" required>　( 最小值為 1 )</label><br><br>
                <label>邀請碼：　</label>
                <label><input type="radio" value="1" name="invitecodemod" v-model="invitecodemod">共用邀請碼</label>
                <label><input type="radio" value="2" name="invitecodemod" v-model="invitecodemod">獨立邀請碼</label><br><br>
                <div>
                    <label v-if="invitecodemod == '1'"><input v-model="invitecode[0]" name="invitecode[]" type="text" required></label>
                    <div v-if="invitecodemod == '2'">
                        <label class="invitecode" style="margin-bottom: 5px;" v-for="index in questionnum">{{ index }}. <input style="margin: 0" name="invitecode[]" v-model="invitecode[index]" type="text" @change="happy(index)" required></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn" data-dismiss="modal" aria-hidden="true" value="取消">
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
                invitecode: [null],
                invitecodemod: "1",
                lock: <?=json_encode($lock);?>,
                questionnum: null
            }
        },
        methods:{
            statistics(){
                location.href = "statisticslist.php";
            },
            happy(i){
                let haha = this.invitecode.filter(fuck => fuck == this.invitecode[i])
                if (haha.length > 1){
                    this.invitecode[i] = null
                    this.$forceUpdate()
                    alert("偵測到重覆邀請碼")
                }
            },
            submit(){
                const _this = this
                $.post(`api.php?do=checkinvitecode`,{
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
                if (this.lock[questionid]){
                    alert("此問卷已被鎖定!");
                }else{
                    location.href = `editquestion.php?id=${questionid}`;
                }
            },
            deletequestion(questionid){
                $.post(`api.php?do=deletequestion`,{questionid},function (a){
                    alert(a);
                    history.go(0)
                })
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
        },
        watch: {
            invitecode: function() {
                if (this.invitecode[this.invitecode.length - 1] != null)
                    this.invitecode.push(null);
            }
        }
    })
</script>
</body>
</html>