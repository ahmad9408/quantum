var detailsWindow;
	function showVendor(textid,txtname,txtaddr,mode)
	{
	   detailsWindow = window.open("window_vendor.php?textid="+textid+"&txtname="+txtname+"&txtaddr="+txtaddr+"&mode="+mode+"","vendor","width=400,height=800,top=300,scrollbars=yes");
	   detailsWindow.focus();   
	}
	function barcode_load() {
		if(document.getElementById("srcbasic").value!=""){basic=document.getElementById("srcbasic").value;}else{basic="_";}
		if(document.getElementById("srckategori").value!=""){kategori=document.getElementById("srckategori").value;}else{kategori="_";}
		if(document.getElementById("srckelas").value!=""){kelas=document.getElementById("srckelas").value;}else{kelas="_";}
		if(document.getElementById("srcstyle").value!=""){style=document.getElementById("srcstyle").value;}else{style="__";}
		if(document.getElementById("srcmodel").value!=""){model=document.getElementById("srcmodel").value;}else{model="__";}
		if(document.getElementById("srcsize").value!=""){size=document.getElementById("srcsize").value;}else{size="__";}
		if(document.getElementById("srcsupplier").value!=""){supplier=document.getElementById("srcsupplier").value;}else{supplier="___";}
		if(document.getElementById("srcwarna").value!=""){warna=document.getElementById("srcwarna").value;}else{warna="___";}
		if(document.getElementById("srcgrade").value!="a" && document.getElementById("srcgrade").value!=""){supplier="99B";}
		document.getElementById("srckode").value=basic+kategori+kelas+style+model+size+supplier+warna;
	}
	
$(document).ready(function(e) {
    $('#srcbasic').chosen({});	$('#srckategori').chosen({});	$('#srckelas').chosen({});
	$('#srcstyle').chosen({});	$('#srcmodel').chosen({});	$('#srcsize').chosen({});
		$('#srcwarna').chosen({});	$('#srcgrade').chosen({});	$('#pilihan').chosen({});
	$('#md').chosen({});
	
	
});
