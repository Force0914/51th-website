<?php
$invitecode = $_GET['code'];
$link = mysqli_connect("127.0.0.1","admin","1234","51");
$result = mysqli_query($link,"SELECT * FROM code WHERE code = '$invitecode'");
$row = mysqli_fetch_assoc($result);
$questionid = $row['questionid'];
$result2 = mysqli_query($link,"SELECT * FROM question WHERE id = $questionid");
$row2 = mysqli_fetch_assoc($result2);
$title = $row2['name'];
$questiondata = mysqli_query($link,"SELECT * FROM question WHERE id = $questionid");
$questiondatarow = mysqli_fetch_assoc($questiondata);
$questionsdata = mysqli_query($link,"SELECT * FROM questions WHERE questionid = $questionid ORDER BY item");
$title = $questiondatarow['name'];
$num = mysqli_num_rows($questionsdata);
$data = array();
while ($questionsdatarow = mysqli_fetch_assoc($questionsdata)){
    $options = json_decode($questionsdatarow['options']);
    if ($options[0] == "true"){
        $options[0] = true;
    }else{
        $options[0] = false;
    }
    if ($questionsdatarow['required'] == "true"){
        $questionsdatarow['required'] = true;
    }else{
        $questionsdatarow['required'] = false;
    }
    $adata = array("id" => $questionsdatarow['id'],"desc" => $questionsdatarow['description'],"item" => $questionsdatarow['item'],"mode" => $questionsdatarow['mode'],"required" => $questionsdatarow['required'],"options" => $options,"ans" => array(),"else" => "");
    array_push($data,$adata);
}
?>
<!doctype html>
<html lang="zh_tw">
<head>
    <?php include("head.php");?>
    <script src="js/jquery.min.js" charset="utf-8"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/vue.js"></script>
    <script src="js/mixin.js"></script>
    <title>網路民意調查系統</title>
</head>
<body>
<div class="container" id='app'>
    <div class="Top-well">
        <div class="well-color-top2 progress progress-info progress-striped active">
            <div class="bar pcnone" :style="{width: ((mpage+1)/questions.length)*100 + '%'}"></div>
        </div>
        <h1><?=$title?></h1>
        <input type="hidden" name="title" value="<?=$title?>">
        <div style="float: right" class="btn-group">
            <input type="button" class="btn" @click="cancel()" value="取消">
            <input type="button" class="btn" @click="check()" value="送出">
        </div>
    </div>
    <div>
        <div :class="{well:true,active:index == mpage}" v-for="(question, index) in questions" :key="question.item">
            <div class="well-color-top3" style="margin: 0">{{index + 1}}<p style="float: right;font-size: 15px;margin-right: 10px;color: green;">{{ types[question.mode] }}</p></div>
            <div style="margin-top: 30px">
                <p>題目說明：{{ question.desc }}</p>
                <p style="color: red;position: absolute;left: -20px;top: 10px;width: 10px" v-if="question.required">＊必填</p><br><br>
                <p>題目選項：</p><br>
                <div class="chose">
                    <label v-if="question.mode == 1"><input type="radio" value="是" v-model="question.ans[0]">是</label>
                    <label v-if="question.mode == 1"><input type="radio" value="否" v-model="question.ans[0]">否</label>
                    <div class="row" v-if="question.mode == 2">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0"><input type="radio" :value="n" v-model="question.ans[0]">{{ question.options[n] }}</label>
                    </div>
                    <div class="row" v-if="question.mode == 3">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0"><input type="checkbox" value="n" v-model="question.ans[n]">{{ question.options[n] }}</label>
                    </div>
                    <div class="row">
                        <label class="span5" v-if="question.options[0]"><input v-model="question.ans[0]" value="true" :type="question.mode == 2 ? 'radio' : 'checkbox'">其他：<input style="margin: 0" type="text" v-model="question.else" :disabled="question.ans[0] != true && question.ans[0] !='true'"></label>
                    </div>
                    <label v-if="question.mode == 4"><input style="margin-left: 16px;" type="text" placeholder="自行輸入" v-model="question.ans[0]"></label>
                </div>
                <br>
                <div style="width: 322px;" class="row pcnone next">
                    <table style="width: 100%;table-layout: fixed;">
                        <td style="text-align: center;"><input type="button" class="btn" value="上一題" @click="mpage--" :disabled="index == 0"></td>
                        <td style="text-align: center;"><p style="font-size: 13px;width: 100%;" class="pcnone">已完成{{((mpage+1)/questions.length)*100 + '%'}}題目</p></td>
                        <td style="text-align: center;"><input type="button" class="btn" value="下一題" @click="mpage++" :disabled="index == questions.length - 1"></td>
                    </table>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    new Vue({
        el: "#app",
        data(){
            return {
                questionid: <?=$questionid?>,
                invitecode: "<?=$_GET['code']?>",
                title: "<?=$title?>",
                questions: <?=json_encode($data)?>,
                mpage: 0
            }
        },
        methods:{
            cancel(){
                location.href = "index.php";
            },
            check(){
                let done = true;
                this.questions.forEach(question => {
                    let ansfilter = question.ans.filter(e=>e);
                    if (question.required){
                        if (question.ans.length <= 0 || ansfilter.length <= 0){
                            done = false;
                        }
                        if(question.ans[0] == true || question.ans[0] == "true"){
                            if(question.else.length <= 0){
                                done = false;
                            }
                        }
                    }
                })
                if (done){
                    this.sand();
                }else{
                    done = true;
                    alert("尚有必填問題未回答");
                }
            },
            sand(){
                $.post(`api.php?do=write`, this.$data, function (a) {
                    alert(a);
                    if (a == "作答完成") {
                        location.href = "index.php";
                    } else {
                        history.go(0);
                    }
                })
            }
        }
    })
</script>
</body>
</html>