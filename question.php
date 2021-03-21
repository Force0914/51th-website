<?php
    $title = $_POST['title'];
    $num = $_POST['num'];
    $invitecodemod = $_POST['invitecodemod'];
    $invitecode = $_POST['invitecode'];
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
                title: "<?=$title?>",
                num: <?=$num?>,
                questions: [],
                invitecode:<?=json_encode($invitecode)?>,
                invitecodemod:<?=$_POST['invitecodemod']?>,
                questionnum:<?=$_POST['questionnum']?>
            }
        },
        methods:{
            sand(){
                $.post(`api.php?do=creatquestion`,this.$data,function (a){
                    alert(a);
                    if(a == "新增成功"){
                        location.href = "admin.php";
                    }
                })
            },
            cancel(){
                location.href = "admin.php";
            },
            add(){
                this.num++;
                this.questions.push({item: this.num, mode: 0, desc: '',required: false, options: [false,'', '', '', '', '', '']})
            },
            del(index){
                this.questions.splice(index,1);
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
        },
        mounted () {
            for(let i=0;i<this.num;i++){
                this.questions.push({item: i, mode: 0, desc: '',required: false, options: [false,'', '', '', '', '', '']})
            }
        }
    })
</script>
</body>
</html>