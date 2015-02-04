var chart;
var hsChart;
var titre, sousTitre;


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
                    chartOptions.chart.events.click = function () {};
                    hsChart = new Highcharts.Chart(chartOptions);
                    hsChart.setTitle({text: titre}, { text: sousTitre});
                }
                this.hasChart = true;
            }
        }
    };
}



function majWeibull(lat, lng){

    $.getJSON("http://eol.calsimeol.fr/fics_php/dataWeibull.php?latitude="+lat+"&longitude="+lng, function(json){
        
        var cpt=0;
        var dataWeibull = [];
        titre = 'Loi de Weibull : '+json.nom;
        sousTitre = 'Lat: '+json.latitude+' - Long: '+json.longitude+' ('+ json.altitude +'m) <br/> <br/> k: '+json.facteurForme+' A: '+json.facteurEchelle;
        //alert("NOM : "+json.nom+" LATITUDE : "+json.latitude+" LONGITUDE : "+json.longitude);
        $.each(json.dataWeibull, function(e,jsonFils){
            dataWeibull[cpt] = parseFloat(jsonFils.weibull);
            cpt=cpt+1;
        });

        var options = {
            chart: {
                renderTo: 'weibull',
                defaultSeriesType: 'line',
                events: {}
            },
            title:{
                text: titre
            },
            subtitle:{
                text: sousTitre
            },
            xAxis: {
                title:{
                    text: 'Vitesse du vent (m/s)',
                }
            },
            yAxis: {
                title: {
                    text: 'Probabilité (%)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                crosshairs:[true,true],
                borderColor: '#4b85b7',
                shared: true,
                backgroundColor: '#edf1c8',
                formatter: function() {
                var s;
                $.each(this.points, function(i, point) {
                    s = '<b>'+ point.series.name +'</b>';
                    s = s + '<br> Probabilité : '+point.y+'%';
                    s = s + '<br> Vitesse : '+point.x+'m/s';
                    });
                    return s;
                },
            },
            credits: {
                text: '©EolAtlas',
                href: 'http://creativecommons.org/licenses/by-nc/3.0/'
            },

            series: [{
                name:"WeiBull",
                data: dataWeibull
            }]
        }

        createGraph(options);

    });
}

//majWeibull(48,2);
