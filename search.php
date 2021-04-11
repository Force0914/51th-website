<?php
$invitecode = $_GET['code'];
$link = mysqli_connect("127.0.0.1","admin","1234","51");
$result = mysqli_query($link,"SELECT * FROM code WHERE code = '$invitecode'");
$row = mysqli_fetch_assoc($result);
$questionid = $row['questionid'];
$questiondata = mysqli_query($link,"SELECT * FROM question WHERE id = $questionid");
$questiondatarow = mysqli_fetch_assoc($questiondata);
$questionsdata = mysqli_query($link,"SELECT * FROM questions WHERE questionid = $questionid ORDER BY item");
$resultdata = mysqli_query($link,"SELECT * FROM result WHERE questionid = $questionid");
$title = $questiondatarow['name'];
$page = mysqli_num_rows($resultdata);
$questions = array();
$questionsdatarowarray = mysqli_fetch_all($questionsdata);
while ($resultdatarow = mysqli_fetch_assoc($resultdata)){
    $data = array();
    foreach ($questionsdatarowarray as $questionsdatarow){
        $ansdata = mysqli_query($link,"SELECT * FROM answer WHERE resultid = ".$resultdatarow['id']." AND questionsid = ".$questionsdatarow['0']);
        $ansdatarow = mysqli_fetch_assoc($ansdata);
        $options = json_decode($questionsdatarow['5']);
        if ($options[0] == "true"){
            $options[0] = true;
        }else{
            $options[0] = false;
        }
        if ($questionsdatarow['6'] == "true"){
            $questionsdatarow['6'] = true;
        }else{
            $questionsdatarow['6'] = false;
        }
        $adata = array("id" => $questionsdatarow['0'],"desc" => $questionsdatarow['2'],"item" => $questionsdatarow['4'],"mode" => $questionsdatarow['3'],"required" => $questionsdatarow['6'],"options" => $options,"ans" => json_decode($ansdatarow['ans']),"else" => $ansdatarow['elseans']);
        array_push($data,$adata);
    }
    array_push($questions,$data);
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
        <div class="well-color-top"></div>
        <h1><?=$title?></h1>
        <input type="hidden" name="title" value="<?=$title?>">
        <input style="float: right" type="button" class="btn" @click="cancel()" value="返回">
        <div style="position: absolute;top: 40px;right: 25px;">
            <p>第 <input style="width: 50px;margin: 0" type="number" min="1" :max="allquestions.length" id="page" v-model="page" @input="checknum"> 項 / 共 {{ allquestions.length }} 項</p>
        </div>
    </div>
    <div>
        <div class="well" v-for="(question, index) in allquestions[page-1]">
            <div class="well-color-top3" style="margin: 0"><span style="color: red;">{{question.required ?　'*' : '&nbsp;'}}</span>{{index + 1}}<p style="float: right;font-size: 15px;margin-right: 10px;color: green;">{{ types[question.mode] }}</p></div>
            <div style="margin-top: 30px">
                <p>題目說明：{{ question.desc }}</p><br><br>
                <p>題目選項：</p><br>
                <div class="chose">
                    <label v-if="question.mode == 1"><input type="radio" value="是" v-model="question.ans[0]" disabled>是</label>
                    <label v-if="question.mode == 1"><input type="radio" value="否" v-model="question.ans[0]" disabled>否</label>
                    <div class="row" v-if="question.mode == 2">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0 && data != ''"><input type="radio" :value="n" v-model="question.ans[0]" disabled>{{ question.options[n] }}</label>
                    </div>
                    <div class="row" v-if="question.mode == 3">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0 && data != ''"><input type="checkbox" v-model="question.ans[n]" disabled>{{ question.options[n] }}</label>
                    </div>
                    <div class="row">
                        <label class="span5" v-if="question.options[0]"><input v-model="question.ans[0]" value="true" :type="question.mode == 2 ? 'radio' : 'checkbox'" disabled>其他：<input style="margin: 0" type="text" v-model="question.else" disabled></label>
                    </div>
                    <label v-if="question.mode == 4"><input style="margin-left: 16px;" type="text" placeholder="自行輸入" v-model="question.ans[0]" disabled></label>
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
                page: 1,
                allquestions: <?=json_encode($questions)?>
            }
        },
        methods:{
            cancel(){
                location.href = "statisticslist.php";
            },
            checknum() {
                if (this.page > this.allquestions.length){
                    this.page = this.allquestions.length
                }else if(this.page < 1){
                    this.page = 1
                }
            }
        }
    })
</script>
</body>
</html>