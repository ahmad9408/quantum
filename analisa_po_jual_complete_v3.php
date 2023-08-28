<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Evaluasi PO "; ?>
<?php 
 $lihat=1;
 if($lihat==1){ 
    include('header.php');
 }
 include_once('RekapStok.php'); 
 /*
 v3 ajax diperbaiki  dengan bantuan jquery last edit by xtreme 12 jan 2013
 edit last terakhir kode 20 ags 2015
 last  edit 02082017 harus menggunakan get[search] untuk masuk ke halaman
 */

 
  ?>
   <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->

</script>

<style>
.myLink{
	color:#03F;
	cursor:pointer;
	
}

</style>

<?php
    $tgl1=$_POST['tgl1'];
	$tgl2=$_POST['tgl2'];
	$tgl3=$_POST['tgl3'];
	$tgl4=$_POST['tgl4'];
    $model=$_POST['txt_cari'];
	$tujuan=$_POST['tujuan'];
	$kode_model_cari=$_POST['kode_model_cari'];
	$model_input=$model;
	
	//error_reporting(1);
	
    /*if(isset($_SESSION['tgl1'])){
        $tgl1=$_SESSION['tgl1'];
        $tgl2=$_SESSION['tgl2'];
    }else{
       if(empty($tgl1)){
            $tgl1=date("Y-m-d");
            $tgl2=date("Y-m-d");
        
        } 
        
    }*/
	
	function jumlahHari($month,$year) {
	   return date("j", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}
	
	function dateMysql($number){
	   if($number<10){
		  return '0'.$number; 
	   }else{
		  return $number;   
	   }
	  
	}
	
	
	function createMonthRangeArray($strDateFrom,$strDateTo) {
  
   
      $aryRange=array();

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         array_push($aryRange,date('Y-m-01',$iDateFrom)); // first entry
         $month_before=trim(date('Y-m-01',$iDateFrom));
         while ($iDateFrom<$iDateTo) {
           $iDateFrom+=86400; // add 24 hours
		   if($month_before!=trim(date('Y-m-01',$iDateFrom))){
			   array_push($aryRange,date('Y-m-01',$iDateFrom));
			   $month_before=trim(date('Y-m-01',$iDateFrom));
		   }
           
         }
       }
       return $aryRange;
   }
   
   if (isset($_GET['action'])) { 
		session_start();
		$tambah="&action=search";
        if($_POST['tgl1']!=''){
              $_SESSION['tgl1']=$_POST['tgl1'];
              $_SESSION['tgl2']=$_POST['tgl2'];
              $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
			  $_SESSION['tgl3']=$_POST['tgl3'];
              $_SESSION['tgl4']=$_POST['tgl4'];
              $tgl3=$_SESSION['tgl3'];
              $tgl4=$_SESSION['tgl4'];
        }else{
              $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
			  $tgl3=$_SESSION['tgl3'];
			  $tgl4=$_SESSION['tgl4'];
        }
	} else {
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		unset($_SESSION['tgl3']);
        unset($_SESSION['tgl4']);
		
	}
  if(empty($tgl1)){
		$tgl1=date("Y-m-01");
		$tgl2=date('Y-m-'.jumlahHari(date('m'),date('Y')));
		$tgl3=$tgl1;
		$tgl4=$tgl2;
        
  } 

  $data_periode=split('-',$tgl1);
  $filter_periode=$data_periode[0].'-'.$data_periode[1];
	
    ?>

<link type='text/css' href='css/basic.css' rel='stylesheet' media='screen' />
<script type='text/javascript' src='js/jquery.simplemodal.js'></script>
<script type='text/javascript' src='js/basic.js'></script>
<script src="jquery.jeditable.js" type="text/javascript"></script>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script> 
<script language="JavaScript">
    var totalQtyJualReshare=0;
	var totalQtyJualMarkas=0;
	var totalQtyJualDistribusi=0;
	var totalQtySellALL=0;
	var totalJual=new Array();
	var namaID='';
	var dataID=new Array();
	var ID='';
	


function stok(awal,akhir,parameter,group,cari){
			  $.ajax({
			  type: 'POST',
			  url: 'stok2.php',
			  data: {parameter:parameter,awal:awal,akhir:akhir,group:group,cari:cari},
			  dataType: 'json',
			  success: function(data){
			//  alert(group);
			
			  	$.each(data, function(key, val) 
             	{
						if(group!='reshare'){
							var stok_belum=$("#stok"+val.kode).text(); 
							stok_belum1=parseFloat(stok_belum)+parseFloat(val.qty);
							
							if(group=='distribusi'){
								$("#stok"+val.kode).text(parseFloat(stok_belum1).formatMoney(0, '.', ','));
							}else{
								$("#stok"+val.kode).text(stok_belum1);
							}
						}else{
							$("#stok"+val.kode).text(val.qty);
						}
            	});	 
					
					cek_stok_berikutnya(awal,akhir,parameter,group,cari);
			  }
			});	
			   
		 
}

function cek_stok_berikutnya(awal,akhir,parameter,group,cari){

	if(group=='reshare'){
		stok(awal,akhir,parameter,'markas',cari);
	}else if(group=='markas'){
		stok(awal,akhir,parameter,'distribusi',cari);
	}
	
}

function ambil_penjualan_reshare(rekam,awal,akhir,gudang){

//alert(rekam);
			  $.ajax({
			  type: 'POST',
			  url: 'ambil_penjualan_reshare1.php',
			  data: {rekam:rekam,awal:awal,akhir:akhir,gudang:gudang},
			  dataType: 'json',
			  success: function(data){
		  	//alert(data);
		            var totalReshare=0; 
					var totalMarkas=0;
					var totalDistribusi=0;
			  		$.each(data, function(key, val) 
             		{
						if(gudang=='reshare'){ 
							$("#otl"+val.kode).html(val.qty);
							totalReshare+=Number(val.qty);
							$('#totalQtySellReshare').text(format('#,##0.#',totalReshare));
						}else	if(gudang=='markas'){ 						
							$("#mark"+val.kode).html(val.qty);
							totalMarkas+=Number(val.qty);
							$('#totalQtySellMarkas').text(format('#,##0.#',totalMarkas));
						}else	if(gudang=='gunas'){ 
							$("#dist"+val.kode).html(val.qty);
							totalDistribusi+=Number(val.qty);
							$('#totalQtySellDistribusi').text(format('#,##0.#',totalDistribusi));
						}	
            		});
				    
					ambil_retur_penjualan_reshare(rekam,awal,akhir,gudang);
					
			  }
			  
			});
	
}


function ambil_retur_penjualan_reshare(rekam,awal,akhir,gudang){
	$.ajax({
			  type: 'POST',
			  url: 'ambil_retur_reshare1.php',
			  data: {rekam:rekam,awal:awal,akhir:akhir,gudang:gudang},
			  dataType: 'json',
			  success: function(data){
			//alert(data);
			var nilaiLama=0;
			  		$.each(data, function(key, val) 
             		{
						if(gudang=='reshare'){
							var sisa_reshare=parseFloat($("#otl"+val.kode).text())-parseFloat(val.qty);
							$("#otl"+val.kode).text(sisa_reshare);
							nilaiLama=removeCommas($('#totalQtySellReshare').text());
							$('#totalQtySellReshare').text(format('#,##0.#',nilaiLama-Number(val.qty)));
						}else if(gudang=='markas'){
							var sisa_reshare=parseFloat($("#mark"+val.kode).text())-parseFloat(val.qty);
							$("#mark"+val.kode).text(sisa_reshare);
							nilaiLama=removeCommas($('#totalQtySellMarkas').text());
							$('#totalQtySellMarkas').text(format('#,##0.#',nilaiLama-Number(val.qty)));
						}else if(gudang=='gunas'){
							var sisa_reshare=parseFloat($("#dist"+val.kode).text())-parseFloat(val.qty);
							$("#dist"+val.kode).text(sisa_reshare);
							nilaiLama=removeCommas($('#totalQtySellDistribusi').text());
							$('#totalQtySellDistribusi').text(format('#,##0.#',nilaiLama-Number(val.qty)));
							
						}		
            		});
					
					
					if(gudang=='reshare'){
						ambil_penjualan_reshare(rekam,awal,akhir,'markas');
					}if(gudang=='markas'){
						ambil_penjualan_reshare(rekam,awal,akhir,'gunas');
					}if(gudang=='gunas'){
						hitung_total();
					}
					
					
			  }
			  
			  
			  
	});
}

	
	
	function hitung_total(){
		var banyak=$("#banyak").val();
		for(var i=1;i<=banyak;i++){
			var model=$("#nomor"+i).text();
			var total=parseFloat($("#otl"+model).text())+parseFloat($("#mark"+model).text())+parseFloat($("#dist"+model).text());
			$("#otl"+model).text(parseFloat($("#otl"+model).text()).formatMoney(0, '.', ','));
			$("#mark"+model).text(parseFloat($("#mark"+model).text()).formatMoney(0, '.', ','));
			$("#dist"+model).text(parseFloat($("#dist"+model).text()).formatMoney(0, '.', ','));
			$("#totaljual"+model).text(parseFloat(total).formatMoney(0, '.', ','));
			
			//rian test
			var pengiriman=parseFloat($("#do_"+model).text().replace(/,/g, ''), 10)-total;
			$("#selisih"+model).text(parseFloat(pengiriman).formatMoney(0, '.', ','));
			
			
		}
	}
	
	$(document).ready(function(){
	    $('.hide').hide();
		$('.hilang').hide();
	});
	function xhrRequest2(type) {
            type = type ||  "html"; 
            xhrSend =  !window.XMLHttpRequest ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
            if (xhrSend.overrideMimeType) {   xhrSend.overrideMimeType("text/" + type); }
        return xhrSend;
    }
	function whatWhere2(url, boxid,jenis,model) {
		   var reqType = "text";
		   var xhrRec = xhrRequest2(reqType);
		   document.getElementById(boxid).innerHTML = "Loading .....";
		   xhrRec.open("GET", url, true);
		   xhrRec.onreadystatechange =    function () {
				if (xhrRec.readyState == 4 && xhrRec.status == 200) {
				var rt= xhrRec.responseText;
			    document.getElementById(boxid).innerHTML =format("#,##0.####",rt);// format("#,##0.####",rt);
				if(jenis=="o"){
					 hitungTotalQtyReshare(rt);//Masukan Kenilai totalPoin		
					 // alert("hitung Reshare Jenis " + jenis );
				}else if(jenis=="m"){
					 hitungTotalQtyMarkas(rt);//Masukan Kenilai totalPoin
					  //alert("hitung markas Jenis " + jenis );
				}else if(jenis=="d"){
					hitungTotalQtyDistribusi(rt);//Masukan Kenilai totalPoin
				}
				hitungPerModel(model,rt);
				
				//totalJual[boxid]+=Number(rt);
				//var js = tr.replace(/\<script[\w\W]+?\>/i,"").replace(/\<\\script\s*\>/i,"");
				//try { eval(js); } catch(err){document.getElementById(boxid).innerHTML = String(err); }
					   xhrRec = null;        
			}
			   if (xhrRec.readyState == 4 && xhrRec.status == 404) {
					document.getElementById(boxid).innerHTML = xhrRec.statusText;
					xhrRec = null;                
			}
		   }
				xhrRec.send(null);
	 }
	function getCO(tgl1,tgl2,model){
       //alert(id);	  
	   //Pnjualan
	  progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'co',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var persen=0;
				var po=0;
				var totalPo=0;
				try{
				   $.each(data, function(key, val) {
				        $('#co_'+ val.id).text(format('#,##0.#',val.qty));
						po=removeCommas($('#po_'+ val.id).text());
						totalPo+=Number(po);
						persen=(Number(val.qty)/Number(po)) * 100;
						$('#copersen_' + val.id).text(format('#,##0.#0',persen));
						
					});// end for each
					$('#co_persub').text(format('#,##0.#0',totalPo));
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
		
		
     
        
   }
	
	function getDO(tgl1,tgl2,model){
         progressBar();
		var total_kirim=0;
	   $.ajax({
			url:"analisa_po_do_data.php",
			type:"GET",
			cache: false,
			dataType:'json',
			data:{t1:tgl1 ,t2:tgl2,j:'do_po',m:model,s:tujuan},
			success: function(data) {	
				//alert(' getTglUpload() ' + data);
				//return;	
				var persen=0;
				var totalDo=0;
				var total_kirim=0;
				try{
				   $.each(data, function(key, val) {
						 //alert(val.id);		
						 //alert(document.getElementsByName(val.id));  
						if($('#do_' + val.id).length>0){
							$('#do_' + val.id).text(format('#,##0.#',val.jumlah_kirim));	
							total_kirim +=Number(val.jumlah_kirim);
							//persen=( Number(val.jumlah_kirim)/ Number(removeCommas($('#po_' + val.id).text()))) * 100;//edit tanggal 10 nov2012
							if(Number(removeCommas($('#po_' + val.id).text()))==0){
							    persen=0;
							}else{
							    persen=( Number(val.jumlah_kirim)/ Number(removeCommas($('#po_' + val.id).text()))) * 100;
							}
							
							$('#dopersen_' + val.id).text(format('#,##0.#0',persen));
						}// end if
						
					});// end for each
					$('#total_pengiriman').text(format("#,##0.#",total_kirim));
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		});       
   }//end getDo
	
	function hitungQtyReshare(tgl1,tgl2,model){
       //alert(id);	  
	   //Pnjualan
	  progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'so',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlso=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				        /* nlso=removeCommas($('#otl'+ val.id).text());
						 nlso=Number(nlso) + Number(val.qty);
						 $('#otl'+ val.id).text(format('#,##0.#',nlso));
						 hitungPerModel(val.id,nlso);
						 hitungTotalQtyReshare(val.qty);
						 budi */
						 
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
		
		//Retur
		progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'ro',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlro=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				      /*   nlro=removeCommas($('#otl'+ val.id).text());
						 nlro=Number(nlro) -Number(val.qty);
						 $('#otl'+ val.id).text(format('#,##0.#',nlro));
						 hitungPerModel(val.id,nlro);
						 hitungTotalQtyReshare((Number(val.qty) * -1));*/
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
        
   }
   
   function hitungQtyMarkas(tgl1,tgl2,model){
        progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'sm',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlso=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				        /* nlso=removeCommas($('#mark'+ val.id).text());
						 nlso=Number(nlso) + Number(val.qty);
						 $('#mark'+ val.id).text(format('#,##0.#',nlso));
						hitungPerModel(val.id,nlso);
						hitungTotalQtyMarkas(val.qty);
						budi*/
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
		
		//Retur
		progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'rm',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlro=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				       /*  nlro=removeCommas($('#mark'+ val.id).text());
						 nlro=Number(nlro) -Number(val.qty);
						 $('#mark'+ val.id).text(format('#,##0.#',nlro));
						 hitungPerModel(val.id,nlro);
						 hitungTotalQtyMarkas((Number(val.qty) * -1));*/
						
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
        
   }
   
    function hitungQtyDistribusi(tgl1,tgl2,model){ 
       progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'sd',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlsd=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				        /* nlsd=removeCommas($('#dist'+ val.id).text());
						 nlsd=Number(nlsd) + Number(val.qty);
						 $('#dist'+ val.id).text(format('#,##0.#',nlsd));
						 hitungPerModel(val.id,nlsd);
						 hitungTotalQtyDistribusi(val.qty);
						 */
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
		
		//Retur
		progressBar();
      $.ajax({
			url:"analisa_po_jual_data2.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t1:tgl1,t2:tgl2,j:'rd',m:model},
			success: function(data) {	
				//alert('balik ' + data);
				//return;	
				//alert('#otl'+ val.id);
				var nlrd=0;//nilai Lama omset Reshare
				try{
				   $.each(data, function(key, val) {
				       /*  nlrd=removeCommas($('#dist'+ val.id).text());
						 nlrd=Number(nlrd) -Number(val.qty);
						 $('#dist'+ val.id).text(format('#,##0.#',nlrd));
						 hitungPerModel(val.id,nlrd);
						 hitungTotalQtyDistribusi((Number(val.qty) * -1));*/
					});// end for each
					 progressBar();
					}catch(e){
				   alert(e.message);
				}
				
							
			}				
		}); 
        
   }
   
   function hitungTotalQtyReshare(jumlah){
	    totalQtyJualReshare+= Number(jumlah);
		document.getElementById('totalQtySellReshare').innerHTML =format("#,##0.####",totalQtyJualReshare);
		totalQtySellALL=totalQtyJualReshare + totalQtyJualMarkas + totalQtyJualDistribusi;
		document.getElementById('totalQtySellALL').innerHTML =format("#,##0.####",totalQtySellALL);
		
  // totalPoinValue+=Number(jumlah);
  // document.getElementById('total_poin_id').innerHTML =totalPoinValue;
  }
  
  function hitungTotalQtyMarkas(jumlah){
       totalQtyJualMarkas+=Number(jumlah);
	   
	   document.getElementById('totalQtySellMarkas').innerHTML =format("#,##0.####",totalQtyJualMarkas);
	   totalQtySellALL=totalQtyJualReshare + totalQtyJualMarkas + totalQtyJualDistribusi;
	   document.getElementById('totalQtySellALL').innerHTML =format("#,##0.####",totalQtySellALL);
	   
  }
  
  function hitungTotalQtyDistribusi(jumlah){
       totalQtyJualDistribusi+=Number(jumlah);
	   document.getElementById('totalQtySellDistribusi').innerHTML =format("#,##0.####",totalQtyJualDistribusi);
	   totalQtySellALL=totalQtyJualReshare + totalQtyJualMarkas + totalQtyJualDistribusi;
	   document.getElementById('totalQtySellALL').innerHTML =format("#,##0.####",totalQtySellALL);
	   
  }
  
  function hitungTotalQtyDistribusi(jumlah){
	   totalQtyJualDistribusi+=Number(jumlah);
	   document.getElementById('totalQtySellDistribusi').innerHTML =format("#,##0.####",totalQtyJualDistribusi);
	   totalQtySellALL=totalQtyJualReshare + totalQtyJualMarkas + totalQtyJualDistribusi;
	   document.getElementById('totalQtySellALL').innerHTML =format("#,##0.####",totalQtySellALL);  
  }
  
  
  function removeCommas(nilai){
	  var hasil='';
	  hasil=nilai.replace(/[^\d\.\-\ ]/g, '');  
	  return hasil;	  
  }
  
  function hitungPerModel(id,jumlah){
	 try { 
	    var myID="totalJual"+ id; 		
		var periode="<?php echo $filter_periode; ?>";
		var totalJualPermodel=0;
		var targetPerModel=0;
		var targetPermodelString='';
		var target="";
		var otl=0
		var mark=0;
		var dist=0;
		var bandingModel=0;
		
		 //alert(myID);
		
		 totalJual[myID]+=(Number(jumlah) * 1);	
		 //(["id=''"])
		/* otl=Number(removeCommas($('[id="otl'+ id+'"]').text())); 
		 mark=Number(removeCommas($('[id="mark'+  id +'"]').text()));
		 dist=Number(removeCommas($('[id="dist'+  id +'"]').text()));
		 */
		 
		 if(isNaN(otl)){otl=0;}
		 if(isNaN(mark)){mark=0;}
		 if(isNaN(dist)){dist=0;}
		 
		 //alert(otl);
		 totalJualPermodel=otl + mark + dist;
		 document.getElementById(myID).innerHTML =format("#,##0.##",totalJualPermodel);//format("#,##0.####",totalJual[myID]);
		 target= "targetjual#"+id+"#"+periode; 
		 //alert("target " + target);
		 targetPerModelString=document.getElementById(target).innerHTML;
		 targetPerModelString=removeCommas(targetPerModelString);
		 //alert(targetPerModelString);
		 targetPerModel=Number(targetPerModelString);
		 //alert(" Target Permodel " + targetPerModel + " Dari Id :" + target);
		 targetPerModel=isNaN(Number(targetPerModel))?0:Number(targetPerModel);
		 bandingModel=(totalJualPermodel/targetPerModel) * 100;
		 bandingModel=isNaN(Number(bandingModel))?0:Number(bandingModel);
		 if(bandingModel=='Infinity'){
			 document.getElementById("perbandingan"+ id).innerHTML =0;//format("#,##0.####",totalJual[myID]);
		 }else{
			 document.getElementById("perbandingan"+ id).innerHTML =format("#,##0.##",bandingModel);//format("#,##0.####",totalJual[myID]);
		 }
		  
			 
	 
	 } catch(err){
		 //alert(String(err));
		 //alert('check hitungPerModel ' + err.message);
	 }
	 
	 
  }
  
  function hitungTotalPoAwal(){
	  var data=new Array();
	  var newID='';
	  var totalPOAwal=0;
	  $('.edit_text').each(function(index) {
    		//alert(index + ': ' + $(this).text() + " ID " +(this.id));
			
			data=this.id.split('#');
			if(data[0]=='poawal'){
			  newID=document.getElementById(this.id).innerHTML
			  //alert(newID);
			  totalPOAwal +=Number(removeCommas(newID));
			}
			
			document.getElementById("totalPoAwal").innerHTML=format("#,##0.##",totalPOAwal);
			
			
	  }); 
  }
  function hitungTotalTargetPenjualan(){
	  var data2=new Array();
	  var newID2='';
	  var targetJual=0;
	  $('.edit_text').each(function(index) {
    		//alert(index + ': ' + $(this).text() + " ID " +(this.id));
			
			data2=this.id.split('#');
			if(data2[0]=='poawal'){
			  newID2=document.getElementById(this.id).innerHTML
			  //alert(newID);
			   targetJual +=Number(removeCommas(newID2));
			}
			
			document.getElementById("totalTargetPenjualan").innerHTML=format("#,##0.##",targetJual);
			
			
	  }); 
		 
  
  };
  
  $(function () {
     $("#btnKet").toggle(function(){
		 //alert('1');
		 $('#keterangan').show();
	  },function(){
		 //alert('2');
		 $('#keterangan').hide();
	 });
  });
  
  $(function() {
	  $(".edit_text").editable("target_permodel.php", {
		  indicator : "<img src='ajax-loader.gif'>",
		  submitdata: { _method: "post" },
		  select : true,
		  submit : 'Update',
		  cssclass : "editable",
		  width : "10",
		  style   : 'display: inline',
		  loadtext  : 'Updating…',
		  callback : function(value, settings) {
         	console.log(this);
         	console.log(value);
         	console.log(settings);
			//alert(value );
			//alert(this.id);
			dataID=this.id.split('#');
			namaID=dataID[0];
			ID=dataID[1];
			
			if(namaID=='targetjual'){
			  //alert(ID);
			  hitungTotalTargetPenjualan();
			  hitungPerModel(ID,value);
			  //alert(namaID);
			  
			}else if(namaID=='poawal'){
			  hitungTotalPoAwal();
			}
			
			
			//alert(settings.div);
     	}
	  });
	});
	
</script>
<script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
<form method="POST" action="<?php echo $PHP_SELF; ?>?action=search" name="outlet">
  <table width="2008">
			<tr>
			  <td width="196" valign="top">Periode PO-CO-DO Dari</td>
			  <td width="10" valign="top">:</td>
				
              <td width="1786" valign="top"><script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
            <!-- calendar attaches to existing form element -->
            <input type="text" name="tgl1" readonly id="tgl1" value="<?php echo $tgl1; ?>" size="10"/> &nbsp;			 
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl1'
              });
            </script>
			 &nbsp;
			 &nbsp;&nbsp;&nbsp;&nbsp;
			 
			 <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="10"/> &nbsp;
			
            <script language="JavaScript">
              new tcal ({                                                         
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl2'
              }); 
            </script>
                
            	
			  </td>
			</tr>
			<tr>
			  <td>Kode Model</td>
			  <td>:</td>
			  <td><input name="kode_model_cari" type="text" value="<?php echo $kode_model_cari?>" maxlength="7" />
                &nbsp;Nama Model<input name="txt_cari" type="text" value="<?php echo $model?>" /></td>
    </tr>
			<tr>
			  <td valign="top">Periode (Penjualan ) Dari</td>
			  <td valign="top">:</td>
			  <td valign="top"><input type="text" name="tgl3" readonly="readonly" id="tgl3" value="<?php echo $tgl3; ?>" size="16"/>
               <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl3'
              });
            </script>
		      <input type="text" name="tgl4" readonly="readonly" id="tgl4" value="<?php echo $tgl4; ?>" size="16"/>
               <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl4'
              });
            </script></td>
    </tr>
			<tr>
			  <td valign="top">Pilihan</td>
			  <td valign="top">:</td>
			  <td valign="top"><select name="tujuan" size="1" id="tujuan">
			    <?php  
				   #$arrayJenis=array(''=>'ALL','suho'=>'Suho','supplier'=>'Supplier' );
				    $sql="SELECT SQL_CACHE LOWER(IFNULL(nama_po,nama)) as nama_po,nama FROM gudang_distribusi WHERE jenis=1";
				   $res_gdg=mysql_query($sql);
				  # $arrayJenis=array(''=>'ALL','suho'=>'Suho','supplier'=>'Supplier' );
				    $arrayJenis=array(''=>'ALL');
				   while(list($id,$nama)=mysql_fetch_array($res_gdg)){
					   $arrayJenis[$id]=$nama;
				   }
				   foreach($arrayJenis as $key=>$value){
					   if($key==$tujuan){
						    echo "<option value='$key' selected='selected'>$value</option>";
					   }else{
						    echo "<option value='$key'>$value</option>";
					   }
					   
				   }
				
				?>
                
		      </select></td>
    </tr>
			<tr>
			  <td valign="top">&nbsp;</td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input type="submit" value="Cari"/></td>
    </tr>
  </table>
</form>
<table id="gambar_frame" width="100" style="padding:0px; background:#fff;display:none;" >
<tr><td>
<table id="zoom">
	<tr>
		<td id="zoom1"></td>
  </tr>
</table>
  
	<table class="hide">
	<tr>
		<td id="gambar"></td>
	</tr>
  </table>
  
  </td></tr>
</table> 
<span id='btnKet' style="color:#03F;cursor:pointer;"> [ Keterangan Modul ]</span><br />
<span id="keterangan" style="display:none;color:#03F">
Untuk Penjualan analisa Jika Analisa Pada Bulan Aktif maka maksimala tanggal sampai adalah sampai tanggal kemarin <br>
Menyamakan dengan rekap_penjualan_all_bulanan.php (Merchandise -> Rekap Penjualan ALL)

</span>

<?php 
if($_GET['action'] =='search'){
	
}else{
   include_once('footer.php');
   die();	
}
if (isset($_GET['action'])) {include("progress_bar.php");} ?>
        <table border="0" width="100%" style="font-size: 8pt" class="sortable">
          <tr> 
            <td background="images/footer.gif" align="center" width="350"><strong>Model</strong></td>
            <td background="images/footer.gif" align="center" width="100" class="sorttable_numeric"><strong>Qty Po Awal</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22" class="sorttable_numeric"><b>Qty 
              Po </b></td>
            <td background="images/footer.gif" align="center" width="100" class="sorttable_numeric"><strong>Qty CO </strong></td>
            <td background="images/footer.gif" align="center" width="100" class="sorttable_numeric"><strong>% CO</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22" class="sorttable_numeric"><b>Qty 
              DO (APP2)</b></td>
            <td background="images/footer.gif" align="center" width="100" class="sorttable_numeric"><strong>% DO</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22" class="hilang"><b>Target Penjualan[]berdasarkan tgl awal Po]</b></td>
            <td background="images/footer.gif" align="center" width="100" height="22" class="sorttable_numeric"><b>Penjualan Reshare</b></td>
            <td background="images/footer.gif" align="center" width="105" class="sorttable_numeric"><strong>Penjualan Markas</strong></td>
            <td background="images/footer.gif" align="center" width="105" class="sorttable_numeric"><strong>Penjualan Distribusi</strong></td>
            <td background="images/footer.gif" align="center" width="105" height="22" class="sorttable_numeric"><b>Total Penjualan</b></td>
            <td background="images/footer.gif" align="center" width="105" class="hilang" ><strong>[%] Perbandingan Target Dan Penjualan </strong></td>
			<td background="images/footer.gif" align="center" width="105"   ><strong>DO-T.Penjualan</strong></td>
			<td background="images/footer.gif" align="center" width="105"   ><strong>Stok (Real)</strong></td>
          </tr>
          <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=500;
	$page=$hal;
	$tbhn='';
	if (isset($_GET['action'])) { 
		
		if(!empty($kode_model_cari)){
			$tbhn.=" AND CONCAT(m.kode_basic_item,m.kode_kategori,m.kode_kelas,m.kode_style,m.kode) LIKE '$kode_model_cari%'";
		}
		
        $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and p.approve2=1 and m.model like '%$model%' $tbhn
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
					// cari berdasarkan tanggal 
		 $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and p.approve2=1 and m.model like '%$model%'  $tbhn AND p.closeco IS NULL  and  p.request_ke like '$tujuan%' 
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
		$sql3="SELECT FOUND_ROWS()";
		
		
		
		
	} else {
		
		 $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.approvedate2 BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and p.approve2=1 and model ='-' AND p.closeco IS NULL
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`   
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
        $sql3="SELECT FOUND_ROWS()";
		
			
		
	}
	
	
	if($username=='budi-it'){
		echo $sql3.'#'.$sql2;
	}
	
	//echo $sql3.'#'.$sql2;
	//$hsltmp12=mysql_query($sql1,$db) or die('Error euy'.$sql1);
	//$jmlData=mysql_fetch_row($hsltmp12);
   // echo 'Print => jm'.$jmlData[0] .' test';
    //print_r($jmlData);
	$hsltemp2=mysql_query($sql2,$db) or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
    $hsltmp12=mysql_query($sql3,$db) or die ($sql3);
	list($jmlData[0])=mysql_fetch_array($hsltmp12);
	$no=($hal*$jmlHal);
	$stok_persub=0;
	$co_persub=0;
	$pengiriman_persub=0;
	$distribusi_qty_persub=0;
	$markas_qty_persub=0;
	$script_otl='';
	$script_mark='';
	$script_dist='';
	$total_poawal=0;
	$total_target_jual=0;
	
	
	
	
	?>
	<input type="text" id="banyak" value="<?php echo $jmlData[0]?>" class="hilang" />
	<?php 
	
	
    while ( list($model,$po,$kode_model,$kode_style,$kode_kelas,$kode_kategori,$kode_basic_item)=mysql_fetch_array($hsltemp2)) {
        $no++;
		
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		$km="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%";//kode model
		$id_mod="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model";
		
         ?><span id="nomor<?php echo $no?>" class="hilang"><?php echo $id_mod?></span>
          <tr onMouseOver="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>"> 
            <td><?php   $id_mod="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model";
		    $sql1="select nama_file from mst_model_foto where kode_model='$id_mod'";
            $resi=mysql_query($sql1);//or die($sql1);
            if(mysql_num_rows($resi)==0){
                echo "[$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model]$model";
            } else{
                list($gambar)=  mysql_fetch_array($resi);
                 echo "<span class='myLink' onclick=\"lihat('$gambar','$id_mod');\">[$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model]$model</span>";
            }
           
                   
                 ?></td>
            <td align="right" class="edit_text" id="<?php echo "poawal#$id_mod#$filter_periode"; ?>"><?php
                //$sql="SELECT sum(qty),sum(po_awal) FROM target_jual_permodel WHERE periode LIKE '$filter_periode%' AND model like '$id_mod%';";
				$sql="SELECT sum(qty),sum(po_awal) FROM target_jual_permodel WHERE periode between '$tgl1' AND '$tgl2'  AND model like '$id_mod%';";
				$res_dis=mysql_query($sql) or die('Error '. $sql);      
                list($target_jual,$po_awal)=mysql_fetch_array($res_dis);
				$target_jual=($target_jual=='')?0:$target_jual;  
				$po_awal=($po_awal=='')?0:$po_awal;
				$total_poawal+=$po_awal;
				echo number_format($po_awal); 
			
			?></td>
            <td height="21" align="right" id="<?php echo "po_$id_mod"; ?>"><?php echo number_format($po);$po_persub+=$po; ?></td>
            <td align="right" id="<?php echo "co_$id_mod"?>"><?php
			   $sql="SELECT  SUM(cd.qty) FROM  job_cutting_detail AS cd INNER JOIN job_cutting AS c 
                     ON (cd.no_co = c.no_co) WHERE  cd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					 AND  c.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
					 
			   $sql="SELECT   SUM(jd.qty_produk) FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
        			  ON (jd.no_co = j.no_co) WHERE jd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					  AND   j.approvedate BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
					  
			   $sql="SELECT   SUM(jd.qty_produk) FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
        			  ON (jd.no_co = j.no_co) INNER JOIN po_manufaktur p on p.no_manufaktur=j.no_po WHERE jd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					  AND   p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
			   
			  //echo $sql;
			  $res_co=mysql_query($sql) or die('Error '. $sql);      
              list($co)=mysql_fetch_array($res_co);     
			  if($co==''){
				  $co=0;
			  }
			  $co_persub12+=$co;  
			
			  echo number_format($co);
			  $co_persen=($co/$po) * 100;
			
			?></td> 
            <td align="right" id="<?php echo "copersen_$id_mod"?>"><?php echo number_format($co_persen,1,'.',',').' %';?></td>
            <?php
                       $sql="SELECT  ifnull(SUM(md.qty),0) FROM  `do_produk_detail` AS `md`  INNER JOIN `do_produk` AS `m` 
                                ON (`md`.`no_do` = `m`.`no_do`) WHERE `md`.`kd_produk` LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%'
                                AND `m`.tanggal BETWEEN  '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.no_do not like 'BTL%'";
                       //echo $sql;
                        //$res=mysql_query($sql) or die('Error '. $sql);      
                        list($pengiriman)=mysql_fetch_array($res);  
						$pengiriman_persub+=$pengiriman;
						$do_persen=($pengiriman/$po) * 100;
    $rekam_kode.=$id_mod.",";
            
?>
            
            <td height="21" align="right" id="<?php echo "do_$id_mod"?>"><?php echo number_format($pengiriman); ?></td>
            <td align="right" id="<?php echo "dopersen_$id_mod"?>"><?php echo number_format($do_persen,1,'.',',').' %';?></td>
            <td height="21" align="right" class="hilang" id="<?php echo "targetjual#$id_mod#$filter_periode"; ?>"><?php $total_target_jual+=$target_jual;echo number_format($target_jual); ?></td>
            <?php
			        //$kode_model="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%";
					//$rekapStok=new RekapStok($host,$user,$pass,$dbname);// variabel didapat dari connect config
					
                    //$j_outlet=qtySellOutlet($tgl1,$tgl2,$kode_model);
					
					//$j_outlet=$rekapStok->qtyNettoOutletActual($tgl1,$tgl2,$kode_model);// ambil dari penjualan reshare langsung
					//$j_outlet2=$rekapStok->qtySellOutlet($tgl1,$tgl2,$kode_model); ambil dari laporan
					
					//$j_markas=$rekapStok->qtySellMarkas($tgl1,$tgl2,$kode_model);
					//$j_distribusi=$rekapStok->qtySellDistribusi($tgl1,$tgl2,$kode_model);
					
            ?>
            <td height="21" align="right" id="<?php  echo "otl".$id_mod; ?>">0</td>
            <td align="right" id="<?php echo "mark".$id_mod; ?>">0</td>
            <td align="right" id="<?php echo "dist".$id_mod; ?>">0</td>
            <?php
			       
                     // Yang B Tampilkan
                    if(substr($kode_basic_item,0,1)=='B'){
                        $terjual= $pengiriman - $distribusi_qty  -$markas_qty; 
                    }else{
                        $terjual= '';
                    }
                     
                        
           
       
            ?>
            <td height="21" align="right" id="<?php echo "totaljual".$id_mod; ?>">0</td>
            <td align="right" id="<?php  echo "perbandingan".$id_mod ?>" class="hilang"></td>
			<td align="right" id="<?php  echo "selisih".$id_mod ?>"  >0</td>
			<td align="right" id="<?php  echo "stok".$id_mod ?>"  >0</td>
          </tr>
          <?php
	}
   $rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
   
    ?>
         <tfoot>
          <tr> 
            <td height="23" background="images/notupload.jpg" align="center"><i><b>SUBTOTAL</b></i></td>
            <td background="images/notupload.jpg" align="right" id="totalPoAwal"><?php echo number_format($total_poawal);?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($po_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right" id="co_persub1"><?php  echo number_format($co_persub12);?></td>
            <td background="images/notupload.jpg" align="right">&nbsp;</td>
            <td height="23" background="images/notupload.jpg" align="right" id="total_pengiriman"><?php echo number_format($pengiriman_persub); ?></td>
            <td background="images/notupload.jpg" align="right">0</td>
            <td height="23" background="images/notupload.jpg" class="hilang" align="right" id="totalTargetPenjualan"><?php echo number_format($total_target_jual); ?></td>
            <td height="23" background="images/notupload.jpg" align="right" id="totalQtySellReshare">0</td>
            <td background="images/notupload.jpg" width="105" align="right" id="totalQtySellMarkas">0</td>
            <td background="images/notupload.jpg" width="105" align="right" id="totalQtySellDistribusi">0</td>
            
            <td height="23" background="images/notupload.jpg" width="105" align="right" id="totalQtySellALL"><b>0</b></td>
            <td background="images/notupload.jpg" width="105" align="right" class="hilang">&nbsp;</td>
			 <td background="images/notupload.jpg" width="105" align="right"  >&nbsp;</td>
			  <td background="images/notupload.jpg" width="105" align="right"  >&nbsp;</td>
          </tr>
          <tr style="display:none"> 
            <td height="25" background="images/yesupload.jpg" align="center">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg" class="hilang">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg" width="105" align="right" >&nbsp;</td>
            <td background="images/yesupload.jpg" width="105" align="right" >&nbsp;</td>
            <td height="25" background="images/yesupload.jpg" width="105" align="right" >&nbsp;<font color=red><b><?php //echo number_format($jmlData[3]); ?></b></font></td>
            <td background="images/yesupload.jpg" width="105" align="right" class="hilang" >&nbsp;</td>
			<td background="images/yesupload.jpg" width="105" align="right"  >&nbsp;</td>
			<td background="images/yesupload.jpg" width="105" align="right"  >&nbsp;</td>
          </tr>
          </tfoot>
        </table>

<table style="margin-left:10px; margin-top:10px;">
<tr>
						<td class="text_standard">
							Page : 
							<span class="hal" onClick="location.href='analisa_po_jual_complete.php?&hal=0<?php echo $tambah?>';">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual_complete.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual_complete.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
  <?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual_complete.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
							<span class="hal" onClick="location.href='analisa_po_jual_complete.php?&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
				</tr>
</table>
<script language="JavaScript"> 
  var tujuan='<?php echo $tujuan;?>';
function besarkan(){
        $("#zoom").show();
		 $(".hide").hide();
		//alert('besarkan');
        
	}
	
	function kembali(){
			 $(".otomatis").show();
			$("#zoom").hide();
			$(".hide").hide();
			$('#export_form').show();
			
	}
	
	function kembalikan(){
			$(".otomatis").show();
			$("#zoom").hide();
			$(".hide").show();
			$(".sortable").show();
			$("#page").show();
	}

function lihat(gambar,kode){
	   try{
		   
		   
		   $('#export_form').hide();
		   $("#zoom1").html("<img src='ym_it_rabbani/temp/"+gambar+"' alt='ym_it_rabbani/temp/gambar' width='400' height='400' >");
		  /*$("#gambar").html("<img src='ym_it_rabbani/temp/"+gambar+"' alt='ym_it_rabbani/temp/gambar' width='100' onclick='besarkan()'><br><a href='#' onclick='besarkan()'>Zoom</a>&nbsp;&nbsp;<a href='#' onclick='kembali()'>Hide</kembali>");
		  $("#zoom").hide();
		  $(".hide").show();*/
		  
		  $('#gambar_frame').modal({minHeight:420,maxHeight:410,minWidth:410,maxWidth:410,autoResize:true});
	
	   }catch(e){
		  alert(e.message);
	   }
      
     }


//ambil_penjualan_reshare('<?php echo $rekam_kode?>','<?php echo $tgl1?>','<?php echo $tgl2?>','reshare');
//stok('<?php echo $tgl1?>','<?php echo $tgl2?>','<?php echo $rekam_kode?>','reshare','<?php echo $model?>');
<?php
    if (isset($_GET['action'])) {    
?>
  jmlProcess=4;
  getCO(<?php echo "'$tgl1','$tgl2','$model_input'" ?>);
  getDO(<?php echo "'$tgl1','$tgl2','$model_input'" ?>);
  
  <?php
     //$tgl2='2014-01-27';  
	 //di rubah by xtreme untuk menyamakan dengan rekap_penjualan_all_bulanan.php (analisa nya hanya sampai tanggal kemarin)
	 /*$d=explode('-',$tgl2);
	 $periode_aktif=date('Y-m');
	 if($periode_aktif==$d[0].'-'.$d[1]){
		  include_once('DateControl.php');
		  $dc=new DateControl();
		  $tgl2=$dc->yesterday(); 
	 }
	 */
	 
  ?>
  
  ambil_penjualan_reshare('<?php echo $rekam_kode?>','<?php echo $tgl3?>','<?php echo $tgl4?>','reshare');
  stok('<?php echo $tgl3?>','<?php echo $tgl4?>','<?php echo $rekam_kode?>','reshare','<?php echo $model?>');
  
 //hitungQtyReshare(<?php echo "'$tgl1','$tgl2','$model_input'" ?>);
 //hitungQtyMarkas(<?php echo "'$tgl1','$tgl2','$model_input'" ?>);
 //hitungQtyDistribusi(<?php echo "'$tgl1','$tgl2','$model_input'" ?>);

</script>
<?php
}
?>

<?php include_once "footer.php"; ?>