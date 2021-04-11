<?php
$questionid = $_GET['id'];
$link = mysqli_connect("127.0.0.1","admin","1234","51");
$questiondata = mysqli_query($link,"SELECT * FROM question WHERE id = $questionid");
$questiondatarow = mysqli_fetch_assoc($questiondata);
$questionsdata = mysqli_query($link,"SELECT * FROM questions WHERE questionid = $questionid ORDER BY item");
$title = $questiondatarow['name'];
$pcpage = $questiondatarow['pcpage'];
$num = mysqli_num_rows($questionsdata);
$allans = array();
$data = array();
while ($questionsdatarow = mysqli_fetch_assoc($questionsdata)){
    $answer = mysqli_query($link,"SELECT * FROM answer WHERE questionsid = ".$questionsdatarow['id']);
    if ($questionsdatarow['mode'] == 1) {
        $truetime = 0;
        $falsetime = 0;
    }elseif ($questionsdatarow['mode'] == 2 || $questionsdatarow['mode'] == 3){
        $anstimerow = array(array());
    }
    $i = 0;
    while ($answerrow = mysqli_fetch_assoc($answer)){
        $answerjson = json_decode($answerrow['ans']);
        if ($questionsdatarow['mode'] == 1){
            if ($answerjson[0] == "是"){
                $truetime++;
            }else{
                $falsetime++;
            }
        }elseif ($questionsdatarow['mode'] == 2){
            $options = json_decode($questionsdatarow['options']);
            $row = json_decode($answerrow['ans']);
            $options['true'] = "其他";
            foreach($row as $key => $value){
                $i = 0;
                if($value != ""){
                    if (isset($anstimerow[$i]['name']) && $anstimerow[$i]['name'] != $options[$value]) $i++;
                    $anstimerow[$i]['name'] = $options[$value];
                    if (!isset($anstimerow[$i]['y'])) $anstimerow[$i]['y'] = 0;
                    $anstimerow[$i]['y'] += 1 ;
                }
            }
        }elseif($questionsdatarow['mode'] == 3){
            $i = 0;
            $options = json_decode($questionsdatarow['options']);
            $row = json_decode($answerrow['ans']);
            foreach($row as $key => $value){
                if($value == "true"){
                    if ($key+1 < 7){
                        if (!isset($anstimerow[$i]['name'])) $anstimerow[$i]['name'] = $options[$key+1];
                        if ($anstimerow[$i]['name'] != $options[$key]) $anstimerow[$i]['name'] = $options[$key+1];
                    }else{
                        $anstimerow[$i]['name'] = "其他";
                    }
                    if (!isset($anstimerow[$i]['y'])) $anstimerow[$i]['y'] = 0;
                    $anstimerow[$i]['y'] += 1 ;
                    $i++;
                }
            }
        }
    }
    if ($questionsdatarow['mode'] == 1){
        array_push($allans,array(array("name"=>"是","y"=>$truetime),array("name"=>"否","y"=>$falsetime)));
    }elseif ($questionsdatarow['mode'] == 2 || $questionsdatarow['mode'] == 3){
        array_push($allans,$anstimerow);
    }
    $chart = 1;
    if ($questionsdatarow['mode'] ==3){
        $chart = 2;
    }elseif($questionsdatarow['mode'] == 4){
        $chart = 0;
    }
    $adata = array("id" => $questionsdatarow['id'],"desc" => $questionsdatarow['description'],"item" => $questionsdatarow['item'],"mode" => $questionsdatarow['mode'],"chart" => $chart);
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
    <script src="js/highchart 8.0.0/code/highcharts.js"></script>
    <title>網路民意調查系統</title>
</head>
<body>
<div class="container" id='app'>
    <div class="Top-well">
        <div class="well-color-top"></div>
        <h1>{{ title }}</h1>
        <div style="float: right" class="btn-group">
            <input type="button" class="btn" @click="goback()" value="返回">
        </div>
    </div>
    <div>
        <div style="padding-left: 24px" class="well" v-for="(question, index) in questions">
                <div v-if="question.mode != 4" style="float: left;" class="btn-group"><input type="button" class="btn" value="圓餅圖" @click="question.chart = 1"><input type="button" class="btn" value="長條圖" @click="question.chart = 2"></div>
                <p style="float: right;font-size: 15px;margin-right: 10px;color: green;">{{ types[question.mode] }}</p>
                <div v-if="question.mode != 4"><br><br>
                    <div :class="{allnone:question.chart == 2}"  :id="'pie' + index"></div>
                    <div :class="{allnone:question.chart != 2}" :id="'bar' + index"></div>
                </div>
                <div v-else><p>[統計結果] 略</p></div>
        </div>
    </div>
</div>
<script>
    new Vue({
        el: "#app",
        data(){
            return {
                questionid: "<?=$_GET['id']?>",
                title: "<?=$title?>",
                questions: <?=json_encode($data)?>,
                allans: <?=json_encode($allans)?>,
            }
        },
        methods:{
            goback(){
                location.href = "statisticslist.php";
            }
        },
        mounted(){
            console.log(this.allans)
            const _this = this
            for (i = 0;i < this.questions.length;i++){
                if (this.questions[i].mode != 4){
                        let piechart = Highcharts.chart(`pie${i}`, {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {
                                text: this.questions[i].desc
                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    }
                                }
                            },
                            series: [{
                                name: '百分比',
                                colorByPoint: true,
                                data: [{
                                    name: 'Chrome',
                                    y: 100
                                }]
                            }]
                        });
                        if (this.questions[i].mode != 4){
                            piechart.series[0].setData(this.allans[i]);
                        }
                        let barchart = Highcharts.chart(`bar${i}`, {
                        chart: {
                            type: 'bar'
                        },
                        title: {
                            text: this.questions[i].desc
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.y:.1f}%</b>',
                            shared: true
                        },
                        xAxis: {
                            categories: ['選項1', '選項2', '選項3', '選項4', '選項5', '選項6', '其他']
                        },
                        yAxis: {
                            min: 0,
                            max: 100,
                            title: {
                                text: ''
                            }
                        },
                        legend: {
                            reversed: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'percent'
                            }
                        },
                        series: [{
                            name: '選項',
                            data: [2, 1]
                        }]
                    });
                    if (this.questions[i].mode != 4){
                        let categories = [];
                        let categoriesnum = [];
                        let total = 0;
                        _this.allans[i].forEach(ans=>{
                            total += ans.y
                        })
                        _this.allans[i].forEach(ans=>{
                            categories.push(`${ans.name}`);
                            categoriesnum.push(ans.y/total*100);
                        })
                        barchart.xAxis[0].setCategories(categories);
                        barchart.series[0].setData(categoriesnum);
                    }
                }
            }
        }
    })
</script>
</body>
</html>