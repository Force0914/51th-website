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
    <input type="button" class="btn" value="新增問卷" @click="showModal('modal')"><br><br>
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
                            echo "<tr><td>$i</td><td>$name</td><td><div class='btn-group'><a class='btn btn-primary' @click='editquestion($id)'>編輯</a><a class='btn btn-danger' @click='deletequestion($id)'>刪除</a></div></td><td><input type='checkbox' class='ios' id='checkbox-1' v-model='lock[$id]' @click='lockquestion($id)'></td></tr>";
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
        <form action="question.php" method="POST">
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
                        <label class="invitecode" style="margin-bottom: 5px;" v-for="index in questionnum">{{ index }}. <input style="margin: 0" name="invitecode[]" v-model="invitecode[index]" type="text" required></label>
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
                $.post(`api.php?do=deletequestion`,{questionid:questionid},function (a){
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