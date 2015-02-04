var chart;
var hsChart;
var titre, sousTitre;
var categories = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];

function createGraph(chartOptions) {

    chartOptions.chart.events.click = function () {
        hs.htmlExpand(document.getElementById(chartOptions.chart.renderTo), {
            width: 9999,
            height: 9999,
            allowWidthReduction: true,
            preserveContent: false
        }, {
            chartOptions: chartOptions
        });
    };

    chart = new Highcharts.Chart(chartOptions); 

    // Create a new chart on Highslide popup open
    hs.Expander.prototype.onAfterExpand = function () {
        if(this.custom!=null){
            if (this.custom.chartOptions) {
                var chartOptions = this.custom.chartOptions;
                if (!this.hasChart) {
                    chartOptions.chart.renderTo = $('.highslide-body')[0];
                    chartOptions.chart.title = {text: titre}, { text: sousTitre};
                    chartOptions.chart.events.click = function () {};
                    hsChart = new Highcharts.Chart(chartOptions);
                    //hsChart.setTitle({text: titre}, { text: sousTitre});
                }
                this.hasChart = true;
            }
        }
    };

}



function majRoseDesVents(lat, lng){
        
    $.getJSON("http://eol.calsimeol.fr/fics_php/dataRoseDesVents.php?latitude="+lat+"&longitude="+lng, function(json){
        //alert("NOM : "+json.nom+" LATITUDE : "+json.latitude+" LONGITUDE : "+json.longitude);
        var cpt = 0, val=1;
        var dataVitesseMoy = [];
        var dataPourcent = [];
        titre = 'Rose des vents : '+json.nom;
        sousTitre = json.latitude+' - '+json.longitude+' ('+ json.altitude +'m)';
        $.each(json.dataVent, function(e,jsonFils){
            dataVitesseMoy[cpt] = [parseFloat(jsonFils.direction),parseFloat(jsonFils.vitesseMoy)];
            dataPourcent[cpt] = [parseFloat(jsonFils.direction),parseFloat(jsonFils.pourcent)];
            cpt = cpt + 1 ;

        });

         var options = {
            chart: {
                renderTo: 'rose',
                polar: true,
                type: 'column',
                events: {},

            },
            title: {
                text: titre
            },
            subtitle:{
                text: sousTitre
            }
            ,
            pane: {
                size: '85%'
            },
            legend: {
                align: 'center',
                layout: 'horizontal'
            },
            xAxis: {
                min: 0,
                max: 360,
                type: "",
                tickInterval: 22.5,
                tickmarkPlacement: 'on',
                labels: {
                    formatter: function () {
                        return categories[this.value / 22.5] + '°';
                    }
                }
            },
            yAxis: {
                min: 0,
                endOnTick: false,
                showLastLabel: true,
                title: {
                    text: 'Frequency (%)'
                },
                labels: {
                    formatter: function () {
                        return this.value + 'm/s';
                    }
                },
                reversedStacks: false
            },
        
            tooltip: {
                crosshairs:[true,true],
                borderColor: '#4b85b7',
                shared: true,
                backgroundColor: '#edf1c8',
                formatter: function() {
                var s;
                $.each(this.points, function(i, point) {
                    if(point.series.name=='Pourcentage des vitesses'){
                        s = 'Pourcentage : '+point.y+'%';
                    }else{
                        s = 'Vitesse : '+point.y;
                    }
                    s = s + '<br> Angle : '+point.x;
                    });
                    return s;
                },
            },
            plotOptions: {
                column: {
                    pointPadding: 0,
                    groupPadding: 0
                }
            },
            credits: {
                text: '©EolAtlas',
                href:  'http://creativecommons.org/licenses/by-nc/3.0/'
            },
            series: [{
                    type: 'area',
                    name: 'Vitesse moyenne selon la direction',
                    //color: 'rgba(0,204,51,0.0001)',
                    data: dataVitesseMoy
                },{
                    type: 'area',
                    name: 'Probabilite de vent pour la direction',
                    //color: 'rgba(0,204,255,0.0001)',
                    data: dataPourcent
            }]
            }

            createGraph(options);

       //alert("chart.series[0].data 1= "+chart.series.data);
    });
     //alert("chart.series[0].data 2= "+chart.series[0].data);
}

//majRoseDesVents(45.975,5.3283);