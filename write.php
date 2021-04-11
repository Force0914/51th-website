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
$pcpage = $questiondatarow['pcpage'];
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
    $adata = array("id" => $questionsdatarow['id'],"desc" => $questionsdatarow['description'],"item" => $questionsdatarow['item'],"mode" => $questionsdatarow['mode'],"required" => $questionsdatarow['required'],"options" => $options,"ans" => array_pad(array(), 7, ""),"else" => "");
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
            <div class="bar pcnone" :style="{width: (mpage/questions.length)*100 + '%'}"></div>
            <div class="bar mnone" :style="{width: ((ppage-1)/(Math.ceil(questions.length/pcpage)))*100 + '%'}"></div>
        </div>
        <h1><?=$title?></h1>
        <p style="position: absolute;top: 10px;right: 10px;" class="mnone">已完成 {{ ((ppage-1)/(Math.ceil(questions.length/pcpage)))*100 + '% ' }}題目</p>
        <p style="position: absolute;top: 10px;right: 10px;" class="pcnone">已完成 {{(mpage/questions.length)*100 + '%'}} 題目</p>
        <input type="hidden" name="title" value="<?=$title?>">
    </div>
    <div>
        <div :class="{well:true,active:index == mpage}" v-for="(question, index) in fuck" :key="question.id">
            <div class="well-color-top3" style="margin: 0"><span style="color: red;">{{question.required ?　'*' : '&nbsp;'}}</span>{{index + 1}}<p style="float: right;font-size: 15px;margin-right: 10px;color: green;">{{ types[question.mode] }}</p></div>
            <div style="margin-top: 30px">
                <p>題目說明：{{ question.desc }}</p><br><br>
                <p>題目選項：</p><br>
                <div class="chose">
                    <label v-if="question.mode == 1"><input type="radio" value="是" v-model="question.ans[0]">是</label>
                    <label v-if="question.mode == 1"><input type="radio" value="否" v-model="question.ans[0]">否</label>
                    <div class="row" v-if="question.mode == 2">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0 && data != ''"><input type="radio" :value="n" v-model="question.ans[0]">{{ question.options[n] }}</label>
                    </div>
                    <div class="row" v-if="question.mode == 3">
                        <label class="span5" v-for="(data,n) in question.options" v-if="n > 0 && data != ''"><input type="checkbox" v-model="question.ans[n]">{{ question.options[n] }}</label>
                    </div>
                    <div class="row">
                        <label class="span5" v-if="question.options[0]"><input v-model="question.ans[0]" value="true" :type="question.mode == 2 ? 'radio' : 'checkbox'">其他：<input style="margin: 0" type="text" v-model="question.else" :disabled="question.ans[0] !== true && question.ans[0] !== 'true'"></label>
                    </div>
                    <label v-if="question.mode == 4"><input style="margin-left: 16px;" type="text" placeholder="自行輸入" v-model="question.ans[0]"></label>
                </div>
                <br>
                <div style="width: 322px;" class="row pcnone next">
                    <table style="width: 100%;table-layout: fixed;">
                        <td style="text-align: center;" v-show="index != 0"><input type="button" class="btn" value="上一頁" @click="mpage--"></td>
                        <td style="text-align: center;" v-show="index == 0"><input type="button" class="btn" value="取消" @click="cancel()"></td>
                        <td style="text-align: center;" v-show="index != questions.length - 1"><input type="button" class="btn" value="下一頁" @click="mpage++"></td>
                        <td style="text-align: center;" v-show="index == questions.length - 1"><input type="button" class="btn" value="送出" @click="check()"></td>
                    </table>
                    <br>
                </div>
            </div>
        </div>
        <div style="display: flex;justify-content: center;align-items: center;" class="well">
                <input style="margin-right: 15px" type="button" class="btn" value="上一頁" @click="ppage--" v-show="ppage != 1">
                <input style="margin-right: 15px" type="button" class="btn" value=取消 @click="cancel()" v-show="ppage == 1">
                <input style="margin-left: 15px" type="button" class="btn" value="下一頁" @click="ppage++" v-show="ppage != Math.ceil(questions.length/pcpage)">
                <input style="margin-left: 15px" type="button" class="btn" value="送出" @click="check()" v-show="ppage == Math.ceil(questions.length/pcpage)">
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
                mpage: 0,
                pcpage: <?=$pcpage?>,
                ppage: 1,
                innerWidth: $(window).width()
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
                        if(question.ans[0] === true || question.ans[0] === "true"){
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
        },
        computed:{
            fuck(){
                if (this.innerWidth > 412){
                    return this.questions.filter((value,i) => {
                        return i >= (this.pcpage*(this.ppage - 1)) && i < (this.pcpage*(this.ppage))
                    })
                }else{
                    return this.questions
                }
            }
        },
        mounted(){
            const _this  = this
            window.onresize = function () {
                _this.innerWidth = $(window).width()
            }
        }
    })
</script>
</body>
</html>