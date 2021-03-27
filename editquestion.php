<?php
    $questionid = $_GET['id'];
    $link = mysqli_connect("127.0.0.1","admin","1234","51");
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
        $adata = array("id" => $questionsdatarow['id'],"desc" => $questionsdatarow['description'],"item" => $questionsdatarow['item'],"mode" => $questionsdatarow['mode'],"required" => $questionsdatarow['required'],"options" => $options);
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
        <div class="well-color-top"></div>
        <h1><?=$title?></h1>
        <input type="hidden" name="title" value="<?=$title?>">
        <div style="float: right" class="btn-group">
            <input type="button" class="btn" @click="cancel()" value="取消">
            <input type="button" class="btn" @click="sand()" value="送出">
        </div>
            <input style="float: right; margin-right: 10px;" type="button" class="btn" @click="add()" value="新增一個問題">
        <div style="position: absolute;top: 40px;right: 25px;">
            <label for="pcpage">問卷分頁:</label>
            <input style="width: 50px" type="number" min="1" :max="questions.length" id="pcpage" v-model="pcpage">
        </div>
    </div>
    <transition-group name="drog" tag="div">
        <div draggable="true" class="well" v-for="(question, index) in questions" @dragstart="dragStart($event, index)" @dragover="allowDrop" @drop="drop($event, index)" :key="question.item">
            <div class="well-color-left">{{index + 1}}</div>
            <label>問卷題型: </label>
            <label v-for="(item, i) in types"><input type="radio" v-model="question.mode" :value="i">{{ item }}</label>
            <button type="button" class="close" @click="del(index)">×</button>
            <label style="margin-right: 20px;float: right;" v-if="question.mode != 0"><input type="checkbox" name="required" id="required" v-model="question.required">必填</label>
            <br v-if="question.mode != 0">
            <br v-if="question.mode != 0">
            <label v-if="question.mode != 0">題目說明: <input type="text" v-model="question.desc"></label>
            <br v-if="question.mode != 0">
            <br v-if="question.mode != 0">
            <label v-if="question.mode != 0">題目選項:</label><br v-if="question.mode != 0">
            <div class="chose">
                <label v-if="question.mode == 1"><input type="radio" disabled>是</label>
                <label v-if="question.mode == 1"><input type="radio" disabled>否</label>
                <div class="row" v-if="question.mode == 2 || question.mode == 3">
                    <div class="span5" v-for="n in 6"> {{ n }}. <input type="text" v-model="question.options[n]"></div>
                </div>
                <label v-if="question.mode == 2 || question.mode == 3"><input type="checkbox" v-model="question.options[0]"> 其他選項</label>
                <label v-if="question.mode == 4"><input style="margin-left: 16px;" type="text" value="使用者自行輸入" disabled></label>
            </div>
            <br v-if="question.mode != 0">
        </div>
    </transition-group>
</div>
<script>
    new Vue({
        el: "#app",
        data(){
            return {
                questionid: "<?=$_GET['id']?>",
                title: "<?=$title?>",
                num: <?=$num?>,
                questions: <?=json_encode($data)?>,
                delete: [],
                pcpage: <?=$num?>
            }
        },
        methods:{
            cancel(){
                location.href = "admin.php";
            },
            add(){
                this.num++;
                this.questions.push({id:null,item: this.num, mode: 0, desc: '',required: false, options: [false,'', '', '', '', '', '']})
                if(this.pcpage == 0){
                    this.pcpage = this.pcpage + 1
                }
            },
            del(index){
                if (this.questions[index].id != null){
                    this.delete.push(this.questions[index].id)
                }
                this.questions.splice(index,1);
                if(this.pcpage == this.questions.length + 1){
                    this.pcpage = this.pcpage - 1
                }
            },
            sand(){
                $.post(`api.php?do=editquestion`,this.$data,function (a){
                    alert(a);
                    if(a == "編輯成功"){
                        location.href = "admin.php";
                    }else{
                        history.go(0);
                    }
                })
            },
            allowDrop(e){
                e.preventDefault();
            },
            dragStart(e, index){
                let tar = e.target;
                e.dataTransfer.setData('Text', index);
            },
            drop(e, index){
                this.allowDrop(e);
                let dragIndex = e.dataTransfer.getData('Text');
                const temp = this.questions[dragIndex]
                this.questions.splice(dragIndex, 1)
                this.questions.splice(index, 0, temp)
            }
        }
    })
</script>
</body>
</html>