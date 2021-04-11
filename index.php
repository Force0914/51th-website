<!doctype html>
<html lang="zh_tw">
<head>
    <?php include("head.php");?>
    <script src="js/jquery.min.js" charset="utf-8"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/vue.js"></script>
    <title>網路民意調查系統</title>
</head>
<body class="a">
    <div class="box" id="app">
        <h2 v-if="admin">後台問卷管理系統</h2>
        <h2 v-else>前台意見調查系統</h2>
        <hr>
        <h3 v-if="admin">登入</h3>
        <h3 v-else>您好!</h3>
            <input type="text" name="account" id="account" placeholder="帳號" v-model="account" v-if="admin" @keydown.enter="submit('login')">
            <br v-if="admin">
            <input type="password" name="password" id="password" placeholder="密碼" v-model="password" v-if="admin" @keydown.enter="submit('login')">
            <input type="text" name="invitecode" id="invitecode" placeholder="請輸入填寫問卷邀請碼" v-model="invitecode" @keydown.enter="submit('run')" v-else>
            <hr>
            <input type="submit" class="btn" value="登入" @click="submit('login')" v-if="admin">
            <input type="submit" class="btn" value="繼續" @click="submit('run')" v-else>
        <hr>
        <a @click="aclick" v-if="admin">使用者介面</a>
        <a @click="aclick" v-else>管理者介面</a>
    </div>
<script>
    new Vue({
        el:'#app',
        data(){
            return{
                admin: true,
                account: null,
                password: null,
                invitecode: null
            }
        },
        methods:{
            aclick(){
                this.admin = !this.admin
            },
            submit(ddo){
                const _this = this
                $.post(`api.php?do=${ddo}`,this.$data,function (a){
                        if (a == "admin"){
                            alert("登入成功");
                            location.href = "admin.php";
                        }else if (a == "run"){
                            alert("成功辨識問卷");
                            location.href = `write.php?code=${_this.invitecode}`;
                        }else {
                            alert(a);
                        }
                })
            }
        }
    })
</script>
</body>
</html>