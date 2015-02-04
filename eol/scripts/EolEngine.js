
  var coordonnes=[], //coord lat lon des recherches
  feature, //data postes 
  svg; //��l��ments svg 
  var mouse = {x: 0, y: 0};

  //cr��ation carte
    var map = L.map('map').setView([47.22,4.3], 6);
        mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        var layer = L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',{
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="http://cartodb.com/attributions">CartoDB</a>'
}).addTo( map );
        
        L.control.scale({"imperial":false}).addTo(map);

        L.BingLayer = L.TileLayer.extend({
        	  options: {
        	    subdomains: [0, 1, 2, 3],
        	    type: 'Aerial',
        	    attribution: 'Bing',
        	    culture: ''
        	  },

        	  initialize: function(key, options) {
        	    L.Util.setOptions(this, options);

        	    this._key = key;
        	    this._url = null;
        	    this.meta = {};
        	    this.loadMetadata();
        	  },

        	  tile2quad: function(x, y, z) {
        	    var quad = '';
        	    for (var i = z; i > 0; i--) {
        	      var digit = 0;
        	      var mask = 1 << (i - 1);
        	      if ((x & mask) !== 0) digit += 1;
        	      if ((y & mask) !== 0) digit += 2;
        	      quad = quad + digit;
        	    }
        	    return quad;
        	  },

        	  getTileUrl: function(p, z) {
        	    var zoom = this._getZoomForUrl();
        	    var subdomains = this.options.subdomains,
        	      s = this.options.subdomains[Math.abs((p.x + p.y) % subdomains.length)];
        	    return this._url.replace('{subdomain}', s)
        	        .replace('{quadkey}', this.tile2quad(p.x, p.y, zoom))
        	        .replace('{culture}', this.options.culture);
        	  },

        	  loadMetadata: function() {
        	    var _this = this;
        	    var cbid = '_bing_metadata_' + L.Util.stamp(this);
        	    window[cbid] = function (meta) {
        	      _this.meta = meta;
        	      window[cbid] = undefined;
        	      var e = document.getElementById(cbid);
        	      e.parentNode.removeChild(e);
        	      if (meta.errorDetails) {
        	        return;
        	      }
        	      _this.initMetadata();
        	    };
        	    var url = document.location.protocol + '//dev.virtualearth.net/REST/v1/Imagery/Metadata/' + this.options.type + '?include=ImageryProviders&jsonp=' + cbid +
        	              '&key=' + this._key + '&UriScheme=' + document.location.protocol.slice(0, -1);
        	    var script = document.createElement('script');
        	    script.type = 'text/javascript';
        	    script.src = url;
        	    script.id = cbid;
        	    document.getElementsByTagName('head')[0].appendChild(script);
        	  },

        	  initMetadata: function() {
        	    var r = this.meta.resourceSets[0].resources[0];
        	    this.options.subdomains = r.imageUrlSubdomains;
        	    this._url = r.imageUrl;
        	    this._providers = [];
        	    if (r.imageryProviders) {
        	      for (var i = 0; i < r.imageryProviders.length; i++) {
        	        var p = r.imageryProviders[i];
        	        for (var j = 0; j < p.coverageAreas.length; j++) {
        	          var c = p.coverageAreas[j];
        	          var coverage = {zoomMin: c.zoomMin, zoomMax: c.zoomMax, active: false};
        	          var bounds = new L.LatLngBounds(
        	              new L.LatLng(c.bbox[0]+0.01, c.bbox[1]+0.01),
        	              new L.LatLng(c.bbox[2]-0.01, c.bbox[3]-0.01)
        	          );
        	          coverage.bounds = bounds;
        	          coverage.attrib = p.attribution;
        	          this._providers.push(coverage);
        	        }
        	      }
        	    }
        	    this._update();
        	  },

        	  _update: function() {
        	    if (this._url === null || !this._map) return;
        	    this._update_attribution();
        	    L.TileLayer.prototype._update.apply(this, []);
        	  },

        	  _update_attribution: function() {
        	    var bounds = this._map.getBounds();
        	    var zoom = this._map.getZoom();
        	    for (var i = 0; i < this._providers.length; i++) {
        	      var p = this._providers[i];
        	      if ((zoom <= p.zoomMax && zoom >= p.zoomMin) &&
        	          bounds.intersects(p.bounds)) {
        	        if (!p.active && this._map.attributionControl)
        	          this._map.attributionControl.addAttribution(p.attrib);
        	        p.active = true;
        	      } else {
        	        if (p.active && this._map.attributionControl)
        	          this._map.attributionControl.removeAttribution(p.attrib);
        	        p.active = false;
        	      }
        	    }
        	  },

        	  onRemove: function(map) {
        	    for (var i = 0; i < this._providers.length; i++) {
        	      var p = this._providers[i];
        	      if (p.active && this._map.attributionControl) {
        	        this._map.attributionControl.removeAttribution(p.attrib);
        	        p.active = false;
        	      }
        	    }
        	          L.TileLayer.prototype.onRemove.apply(this, [map]);
        	  }
        	});

        	L.bingLayer = function (key, options) {
        	    return new L.BingLayer(key, options);
        	};

        	var bing = new L.BingLayer("Aq4xvZkzTmc0kegk4hHGC43vhPG2OlDZkoD8oLBK2JnsjyJ0-TgGVfmnPNVMW6RG");
        	map.addControl(new L.Control.Layers({'Plan':layer,"Satellite":bing}, {}));
    
 // initialization leaflet de la couche D3
 map._initPathRoot()    

 // liaison carte D3
 svg = d3.select("#map").select("svg"),
 g = svg.append("g");
 
//fetch data json
 d3.json("http://eol.calsimeol.fr/eol/fics_php/dataStations.php", function(collection) {
  collection.stations.forEach(function(d) {
   d.LatLng = new L.LatLng(d.latitude,d.longitude)
   d.id = d.nom;
   d.alt= d.altitude;
  })
  var load=true;

//cr��ation cercles stations
  feature = g.selectAll("circle")
   .data(collection.stations)
   .enter().append("circle")
   .style("stroke", "grey")  
   .style("opacity", .8) 
   .style("fill", "#58e1ff")
   .attr("r", 4)
   .attr("name",function(d){d.nom})
   .on("mousemove",function(d){
    //pointeur nom station
    var e = window.event;
    M = mouse.x ;
    N = mouse.y ;
    $("#pointer").css({top:N,left:M});
    $("#pointer").html("Station: "+d.id+"<br/>Altitude: "+d.alt+" m");
   });

   //initialisation de chaque couche svg
   var plane,plane2,plane3,relief, radar,danger,border,back,chauvesouris,zee,tri,parc,hab,valid;
   var visible={};
   if(load){

  d3.xml("CSV/border.svg", "image/svg+xml", function(xml) {  
      //transaltion xml en noeuds svg D3
      plane4 = document.importNode(xml.documentElement, true);
      //cr��ation couche classe css 
      border=svg.append("g").attr('class','borderlayer layer')
      .each(function(d, i){ 
        //attache noeud svg 
          this.appendChild(plane4.cloneNode(true)); 
      });
      update();
    });

  function LoadPostes(){
     d3.xml("CSV/postes.svg", "image/svg+xml", function(xml) {  
  plane5 = document.importNode(xml.documentElement, true);
    back=svg.append("g").attr('class','backlayer layer')
    .each(function(d, i){ 
        this.appendChild(plane5.cloneNode(true)); 
    }).on("mousemove",function(){
      if(visible["postes"]){
        
      $("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Hors poste source");
      }
    }).style("opacity", 0.8);  
    visible["postes"]=true;
    update();
    });
   }

   function LoadDanger(){
  d3.xml("CSV/danger.svg", "image/svg+xml", function(xml) {  
  plane6 = document.importNode(xml.documentElement, true);
    danger=svg.append("g").attr('class','dangerlayer layer')
    .each(function(d, i){ 
        this.appendChild(plane6.cloneNode(true)); 
    }).on("mousemove",function(){
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Avifaune");}).style("opacity",0.6); 
    visible["danger"]=true;
    update();
    });
    }

    function LoadHabitations(){
    d3.xml("CSV/habitations.svg", "image/svg+xml", function(xml) {  
      plane7 = document.importNode(xml.documentElement, true);
      hab=svg.append("g").attr('class','hablayer layer')
      .each(function(d, i){ 
          this.appendChild(plane7.cloneNode(true)); 
      }).on("mousemove",function(){
        
      $("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Habitations");
      }).style("opacity",0.6);
      visible["hab"]=true;  
      update();

    });
    }
    function LoadValid(){
     d3.xml("CSV/valid.svg", "image/svg+xml", function(xml) {  
      plane1 = document.importNode(xml.documentElement, true);
      valid=svg.append("g").attr('class','validlayer layer')
      .each(function(d, i){ 
          this.appendChild(plane1.cloneNode(true)); 
      }).on("mousemove",function(){

        
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Zone valide");
      }).style("opacity",0.5);
      visible["valid"]=true;  
      update();

    });
   }
   function LoadSouris(){
    d3.xml("CSV/chauvesouris.svg", "image/svg+xml", function(xml) {  
      plane8 = document.importNode(xml.documentElement, true);
      chauvesouris=svg.append("g").attr('class','chauvesourislayer layer')
      .each(function(d, i){ 
          this.appendChild(plane8.cloneNode(true)); 
      }).on("mousemove",function(){
        if(visible["chauvesouris"])
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Chiroptères");
      }).style("opacity", 0.6);
      visible["chauvesouris"]=true;
      update();
 
    })
  }
  function LoadRadar(){
    d3.xml("CSV/radar.svg", "image/svg+xml", function(xml) {  
      plane9 = document.importNode(xml.documentElement, true);
      radar=svg.append("g").attr('class','radarlayer layer')
      .each(function(d, i){ 
          this.appendChild(plane9.cloneNode(true)); 
      }).on("mousemove",function(){
        if(visible["radar"])
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Aviation");
      }).style("opacity", 0.6);;
      visible["radar"]=true;
      update();
 
    });
    }
    function LoadParc(){
     d3.xml("CSV/parc.svg", "image/svg+xml", function(xml) {  
      plane10 = document.importNode(xml.documentElement, true);
      parc=svg.append("g").attr('class','parclayer layer')
      .each(function(d, i){ 
          this.appendChild(plane10.cloneNode(true)); 
      }).on("mousemove",function(){
        if(visible["parc"])
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Parc naturel");
console.log(mouse.x);
      }).style("opacity",0.6);  
      visible["parc"]=true;
      update();

    });
   }
   function LoadZee(){
     d3.xml("CSV/zee.svg", "image/svg+xml", function(xml) {  
      plane11 = document.importNode(xml.documentElement, true);
      zee=svg.append("g").attr('class','zeelayer layer')
      .each(function(d, i){ 
          this.appendChild(plane11.cloneNode(true)); 
      }).on("mousemove",function(){
        if(visible["zee"])
      $("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("ZNIEFF");
    console.log(mouse.x);
      }).style("opacity", 0.4); 
      visible["zee"]=true;
      update();

    });
   }
   function LoadTri(){
     d3.xml("CSV/tri.svg", "image/svg+xml", function(xml) {  
      plane12 = document.importNode(xml.documentElement, true);
      tri=svg.append("g").attr('class','trilayer layer')
      .each(function(d, i){ 
          this.appendChild(plane12.cloneNode(true)); 
      }).on("mousemove",function(){
        if(visible["tri"])
$("#alert").css({top:mouse.y,left:mouse.x});$("#alert").html("Station radioélectrique");
console.log(mouse.x);
      }).style("opacity",0.6);  
      visible["tri"]=true;
      update();

    });
   }
   }

  //toggle selection calque
      $('#zeecheck').change(function() {
        visible["zee"]=this.checked;
        if(this.checked){
            LoadZee();
            update();
        }else{
            zee=null;
            svg.selectAll(".zeelayer").remove()
        }
      });

      $('#postescheck').change(function() {
        visible["postes"]=this.checked;
        if(this.checked){
            LoadPostes();
            update();
        }else{
          back=null;
          svg.selectAll(".backlayer").remove()
        }
      });

      $('#tricheck').change(function() {
        visible["tri"]=this.checked;
        if(this.checked){
            LoadTri();
            update();
        }else{
          tri=null;
          svg.selectAll(".trilayer").remove()
        }
        
      });

      $('#habcheck').change(function() {
        visible["hab"]=this.checked;
        if(this.checked){
            LoadHabitations();
            update();
        }else{
          hab=null;
          svg.selectAll(".hablayer").remove()
        
        }
      });

      $('#parccheck').change(function() {
        visible["parc"]=this.checked;
        if(this.checked){
            LoadParc();
            update();
        }else{
          parc=null;
          svg.selectAll(".parclayer").remove()
        }
        
      });

      $('#dangercheck').change(function() {
        visible["danger"]=this.checked;
        if(this.checked){
            LoadDanger();
            update();
        }else{
          danger=null;
          svg.selectAll(".dangerlayer").remove()
        
        }
      });

      $('#radarcheck').change(function() {
        visible["radar"]=this.checked;
        if(this.checked){
            LoadRadar();
            update();
        }else{
          radar=null;
          svg.selectAll(".radarlayer").remove()
        
        }
      });

      $('#sourischeck').change(function() {
        visible["chauvesouris"]=this.checked;
        if(this.checked){
            LoadSouris();
            update();
        }else{
          chauvesouris=null;
          svg.selectAll(".chauvesourislayer").remove()
        
        }

      });

      $('#validcheck').change(function() {
        visible["valid"]=this.checked;
        if(this.checked){
           LoadValid();
           update();
        }else{
          valid=null;
          svg.selectAll(".validlayer").remove()
        }
        

      });

      $('#all').change(function() {
        if(this.checked){
          d3.select(".layer").style("opacity",0.5);
        }else{
          d3.select(".layer").style("opacity",0);
        }
      });


      $('#onoffswitch').change(function() {
        if(this.checked){
          
        }else{
          //console.log(map);
        }
      });

  //event listenner pour zoom scroll
   map.on("viewreset", update);


   /* function passThru(d) {
        var e = d3.event;

        var prev = this.style.pointerEvents;
        this.style.pointerEvents = 'none';

        var el = document.elementFromPoint(d3.event.x, d3.event.y);

        var e2 = document.createEvent('MouseEvent');
        e2.initMouseEvent(e.type,e.bubbles,e.cancelable,e.view, e.detail,e.screenX,e.screenY,mouse.x,mouse.y,e.ctrlKey,e.altKey,e.shiftKey,e.metaKey,e.button,e.relatedTarget);

        el.dispatchEvent(e2);

        this.style.pointerEvents = prev;
    }*/
  

  function update() {
    $("#pointer").css({top:-100,left:0});
    //translation claque stations: 
   feature.attr("transform", function(d) { return "translate("+ map.latLngToLayerPoint(d.LatLng).x +","+ map.latLngToLayerPoint(d.LatLng).y +")";});
   //initialization zoom en fonction du zoom de la carte
   var zoom=0.818*Math.pow(2,map.getZoom()-8);
   //points d'attache des calques
   var lon=48.7;
   var lat=2.58;
   var newCoord=map.latLngToLayerPoint(new L.LatLng(lon,lat));
   order="translate("+newCoord.x +","+newCoord.y +") scale("+zoom+") rotate(1)";
   d3.selectAll(".layer").attr("transform", function(d) { return order;});
  }
  function zoomHeight(mapScale,latitude,ppi){
    var MetersPerInch = 2.54 / 100;
    var EarthCircumference = 6378137 * Math.PI * 2;
    var realLengthInMeters = EarthCircumference * Math.cos(latitude* Math.PI / 180);
    var zoomLevelExp = (realLengthInMeters*ppi) / (256*MetersPerInch*mapScale);
    return Math.log(zoomLevelExp, 2);
  }
 }) 

  $(".close").click(function(){
    $("#intro").fadeOut(300);
  });

  var currentQuery;
  $("#search").keyup(function(e){
    var query=$("#terms").val();
    query.replace(/ */g,'+');
    console.log(query);
    coordonnes=[];
    $("#resultat").html("patientez...");
    if(currentQuery)currentQuery.abort();
    currentQuery=$.getJSON("http://nominatim.openstreetmap.org/search?q="+query+"&format=json&countrycodes=fr",function(json){
      console.log(json);
      var html="";
      $.each(json,function(key){
        var val= json[key];
        html+='<a href="#" class="destination" id='+key+'>'+val.display_name+'</a><br/>';
        coordonnes[key]={"lat":val.boundingbox[0],"lat2":val.boundingbox[1],"lon":val.boundingbox[2],"lon2":val.boundingbox[3]};
      });
      $("#resultat").html(html);
    });
  });

  $("#resultat").on("click",".destination",function(){
    var id=$(this).prop('id');

    console.log(coordonnes[id]);
    var co=coordonnes[id];
    /*map.fitBounds([
      [parseInt(co["lat"]), parseInt(co["lon"])]
      [parseInt(co["lat2"]), parseInt(co["lon2"])]
    ]);*/
    map.setView([co["lat"],co["lon"]]);
  });
  

 
    map.on('click', function(e) {
		majWeibull(e.latlng.lat, e.latlng.lng);
		majRoseDesVents(e.latlng.lat, e.latlng.lng);
    });
    
    
    $("#map").on('mouseover', function(e) {
    	$("#pointer").css({top:-100,left:0});
    	$("#alert").css({top:-100,left:0});
    });
 
    map.on('mousemove', function(e){ 
      $("#coord").html("Latitude: "+e.latlng.lat.toFixed(4)+" <br/>Longitude: "+e.latlng.lng.toFixed(4));
       mouse.x = e.containerPoint.x;
       mouse.y = e.containerPoint.y;
    }, false);

  
    



