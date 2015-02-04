var resolution=50;
var opac=1;
var html="<table>";
for(var i=0;i<=resolution;i++){
	html+="<tr>";
	for(var j=0;j<=resolution;j++){
		opac=i*j/resolution/resolution;
		html+='<td style="opacity:'+opac+'"></td>';
	}
	html+="</tr>";
}
html+="</table>";

document.getElementById("lookilass").innerHTML=html;