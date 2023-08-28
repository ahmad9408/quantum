$(document).ready(function(e) {
	$('#fupload').change(function(){
				//alert('test');
		$('#form-upload').submit();
		console.log('try submit #form-upload');
		//$('#form-test').submit();	
	});
    $('#form-upload').iframePostForm ({
		post : function (){
			//$('#uploading').modal();
			console.log('#form-upload  upload...');
			var gdg=$('#v_gudang').val();
			//$('#pr_'+gdg).text('Uploading ...');
			$("[id='pr_"+gdg +"']").text('Uploading ...');
			
		},
		
		complete : function (result){
			//$.modal.close();
			console.log('#complete...');
			var d=result.split('#');
			var gdg=$('#v_gudang').val();
			if(d[0]=='ok'){
				//$('#pr_'+gdg).text(d[1]);
				//$('#pr_'+gdg).html('<a href="bukti_koreksi/'+d[1]+'" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a>');
				$("[id='pr_"+gdg +"']").html('<a href="bukti_koreksi/'+d[1]+'" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a>');
			}else{
				/*$('#r_'+d[2]).html(d[1]+'<br/>'+ d[4]);*/
				//$('#pr_'+gdg).html('ERROR '+ d[4]);
				$("[id='pr_"+gdg +"']").html('ERROR '+ d[4]);
				//alert(d[4]);
				//$('#r_'+count).text('');
			}
			console.log('result  '+ result);
			//					$("#up-result_"+ count ).html(result);
		}
	});
	//$('.hilang').hide();//jangan ekseskusi dulu
	
	$('#cb_hpp').change(function(){
			
			if($('#cb_hpp').is(':checked')){
				$('.body_hpp').show();
				$('.head_hpp').show();
				$('.head_dt').attr('colspan',3);	
			}else{
				$('.body_hpp').hide();
				$('.head_hpp').hide();
				$('.head_dt').attr('colspan',2);	
			}
		
		})
	$('.head_dt').attr('colspan',2);	
	$('.body_hpp').hide();
	$('.head_hpp').hide();
});

function removeCommas(nilai){
	  var hasil='';	   
	  hasil=nilai.replace(/[^\d\.\-\ ]/g, '');  	  
	  return hasil;	  
}
 function gantiType(){
	try{
	  $('#formSuho').hide();
	  $('#formFob').hide();
	  $('#formCmt').hide();
	  var tipe=$('#tipe').val();
	 // alert(tipe);
	  $('#'+tipe).show();
	 }catch(e){
		 
	 }
	 
 }

 //======================== START SUHO ==========================//

 function getDataAwalSuho(){
	 var tgl=$('#tgl1').val();
	 $('#procReshare').text("Process get Data....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'sagt',tg:'o',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtyAwal=0;
			var totalHpjAwal=0;
			var totalHppAwal=0;
			$.each(data, function(key, val) {  //alert(val.id);	
				  try{
					  //alert('#qtyawal_'+ val.id);
					  if($('#qtyawal_'+ val.id).length>0){
						  try{
							 $("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						  $('#qtyawal_'+ val.id).text(format('#,##0.#',val.stokawal));
						  $('#hppawal_'+ val.id).text(format('#,##0.#',val.hpp));
						  $('#hpjawal_'+ val.id).text(format('#,##0.#',val.hpj));
						  totalQtyAwal+=Number(val.stokawal);
						  totalHppAwal+=Number(val.hpp);
						  totalHpjAwal+=Number(val.hpj);
						  
						   hitungSelisih(val.id);
					  }
				  }catch(e3){
					   alert('getDataAwalSuho ' + e3.message);  
				  }	
				  				  
				
				});// end for each
			$('#procReshare').text("");	
			$('#totalQtyAwalReshare').text(format('#,##0.#',totalQtyAwal));		
			$('#totalHppAwalReshare').text(format('#,##0.#',totalHppAwal));	
			$('#totalHpjAwalReshare').text(format('#,##0.#',totalHpjAwal));	 
			hitungTotalKoreksiReshare();
		}	//end succes			
	});
}


function getRekapDataAwalSuho(){
	 var tgl=$('#tgl1').val();
	 $('#procReshare').text("Process get Data rekap Stok Awal....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'sarkp',tg:'o',b:barcode},// 
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtyAwal=0;
			var totalHpjAwal=0;
			var totalHppAwal=0;
			$.each(data, function(key, val) {  //alert(val.id);	
				  try{
					  //alert('#qtyawal_'+ val.id);
					  if($('#qtyrawal_'+ val.id).length>0){
						  try{
							 //$("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						  $('#qtyrawal_'+ val.id).text(format('#,##0.#',val.stokawal));
						  $('#hpprawal_'+ val.id).text(format('#,##0.#',val.hpp));
						  $('#hpjrawal_'+ val.id).text(format('#,##0.#',val.hpj));
						  totalQtyAwal+=Number(val.stokawal);
						  totalHppAwal+=Number(val.hpp);
						  totalHpjAwal+=Number(val.hpj);
						  
						
					  }
				  }catch(e3){
					   alert('getRekapDataAwalSuho ' + e3.message);  
				  }	
				  				  
				
				});// end for each
			$('#procReshare').text("");	
			$('#totalRQtyAwalReshare').text(format('#,##0.#',totalQtyAwal));		
			$('#totalRHppAwalReshare').text(format('#,##0.#',totalHppAwal));	
			$('#totalRHpjAwalReshare').text(format('#,##0.#',totalHpjAwal));	 
		
		}	//end succes			
	});
}

function getDataSoSuho(){
	var tgl=$('#tgl1').val();
	$('#procReshareKoreksi').text("Process get Data....");
	$.ajax({
	   url:"laporan_so_suho_rekap_data.php",
	   type:"POST",
	   cache: false,
	   dataType:'json',
	   data:{t:tgl,j:'ssd',tg:'o',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
	   success: function(data) { //alert(data);
		   //alert(data);
		   //return;
		   var count=0;
		   var totalQtySo=0;
		   var totalHpjSo=0;
		   var totalHppSo=0;
		   $.each(data, function(key, val) {  //alert(val.id);	
				 try{
					 //alert('#qtyawal_'+ val.id);
					 if($('#qtyawal_'+ val.id).length>0){
						try{
							$("[id='r_"+ val.id +"']").show();
						}catch(e){
							
						} 
						$('#qtyso_'+ val.id).text(format('#,##0.#',val.so));
						$('#hppso_'+ val.id).text(format('#,##0.#',val.hpp));
						$('#hpjso_'+ val.id).text(format('#,##0.#',val.hpj));
						totalQtySo+=Number(val.so);
						totalHppSo+=Number(val.hpp);
						totalHpjSo+=Number(val.hpj);
						hitungSelisih(val.id);
					 }
					 
				 }catch(e3){
					  alert('getDataSoOutlet ' + e3.message);  
				 }					  
			   
			   });// end for each
		   $('#procReshareKoreksi').text("");	
		   $('#totalQtySoReshare').text(format('#,##0.#',totalQtySo));		
		   $('#totalHppSoReshare').text(format('#,##0.#',totalHppSo));	
		   $('#totalHpjSoReshare').text(format('#,##0.#',totalHpjSo));
		   hitungTotalKoreksiReshare();			 
	   }	//end succes			
   });
}


function hitungTotalKoreksiReshare(){
	var totalQty=0;
	var totalHpp=0;
	var totalHpj=0;
	try{
	$('.qtyKoreksiReshare').each(function(){
		 totalQty+=Number(removeCommas($('#' + this.id).text()));
		
	});	
	$('.hppKoreksiReshare').each(function(){
		 totalHpp+=Number(removeCommas($('#' + this.id).text()));
		
	});	
	
	$('.hpjKoreksiReshare').each(function(){
		 totalHpj+=Number(removeCommas($('#' + this.id).text()));
		
	});	
	$('#totalQtyKoreksiReshare').text(format('#,##0.#',totalQty));
	$('#totalHppKoreksiReshare').text(format('#,##0.#0',totalHpp));
	$('#totalHpjKoreksiReshare').text(format('#,##0.#0',totalHpj));
}catch(e){
	alert('Cek hitungTotalKoreksiReshare()'+ e.message);
}

//alert(' Total  Qty ' + totalQty + ' Total Hpp ' + totalHpp + ' Total  Hpj ' + totalHpj)

}

  function hitungSelisih(id){
	  var qtyAwal=0;
	  var hppAwal=0
	  var hpjAwal=0;
	  var qtySo=0;
	  var hppSo=0
	  var hpjSo=0;
	  var qtyKoreksi=0;
	  var hppKoreksi=0
	  var hpjKoreksi=0;
	  try{
	  qtyAwal=removeCommas($('#qtyawal_' + id).text());
	  hppAwal=removeCommas($('#hppawal_' + id).text());
	  hpjAwal=removeCommas($('#hpjawal_' + id).text());
	  qtySo=removeCommas($('#qtyso_' + id).text());
	  hppSo=removeCommas($('#hppso_' + id).text());
	  hpjSo=removeCommas($('#hpjso_' + id).text());
	  qtyKoreksi=qtySo-qtyAwal;
	  hppKoreksi=hppSo-hppAwal;
	  hpjKoreksi=hpjSo-hpjAwal;
	  $('#qtykoreksi_' + id).text(format('#,##0.#',qtyKoreksi));
	  $('#hppkoreksi_' + id).text(format('#,##0.#',hppKoreksi));
	  $('#hpjkoreksi_' + id).text(format('#,##0.#',hpjKoreksi));
	  
	}catch(e){
		
	}
	
	
}

//======================== END SUHO ==========================//


//======================== START FOB ==========================//

function getDataAwalFob(){
	var tgl=$('#tgl1').val();
	$('#procMarkas').text("Process get Data....");
	$.ajax({
	   url:"laporan_so_suho_rekap_data.php",
	   type:"POST",
	   cache: false,
	   dataType:'json',
	   data:{t:tgl,j:'sagt',tg:'m',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
	   success: function(data) { //alert(data);
		   //alert(data);
		   //return;
		   var count=0;
		   var totalQtyAwal=0;
		   var totalHpjAwal=0;
		   var totalHppAwal=0;
		   $.each(data, function(key, val) {  //alert(val.id);	
				 try{
					 //alert('#qtyawal_'+ val.id);
					 if($('#qtyawalmarkas_'+ val.id).length>0){
						 try{
							$("[id='r_"+ val.id +"']").show();
						}catch(e){
							
						} 
						 $('#qtyawalmarkas_'+ val.id).text(format('#,##0.#',val.stokawal));
						 $('#hppawalmarkas_'+ val.id).text(format('#,##0.#',val.hpp));
						 $('#hpjawalmarkas_'+ val.id).text(format('#,##0.#',val.hpj));
						 totalQtyAwal+=Number(val.stokawal);
						 totalHppAwal+=Number(val.hpp);
						 totalHpjAwal+=Number(val.hpj);
						 
						  hitungSelisihMarkas(val.id);
					 }
				 }catch(e3){
					  alert('getDataAwalFob ' + e3.message);  
				 }	
								   
			   
			   });// end for each
		   $('#procMarkas').text("");	
		   $('#totalQtyAwalMarkas').text(format('#,##0.#',totalQtyAwal));		
		   $('#totalHppAwalMarkas').text(format('#,##0.#',totalHppAwal));	
		   $('#totalHpjAwalMarkas').text(format('#,##0.#',totalHpjAwal));	 
		   hitungTotalKoreksiMarkas();
	   }	//end succes			
   });
}


function getRekapDataAwalFob(){
	 var tgl=$('#tgl1').val();
	 $('#procMarkas').text("Process get Data rekap Stok Awal Markas ....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'sarkp',tg:'m',b:barcode},// 
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtyAwal=0;
			var totalHpjAwal=0;
			var totalHppAwal=0;
			$.each(data, function(key, val) {  //alert(val.id);	
				  try{
					  //alert('#qtyawal_'+ val.id);
					  if($("[id='qtyrawalmarkas_"+ val.id+"']").length>0){
						  try{
							 //$("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						 
						 
						  $("[id='qtyrawalmarkas_"+ val.id+"']").text(format('#,##0.#',val.stokawal));
						  $("[id='hpprawalmarkas_"+ val.id+"']").text(format('#,##0.#',val.hpp));
						  $("[id='hpjrawalmarkas_"+ val.id+"']").text(format('#,##0.#',val.hpj));
						  totalQtyAwal+=Number(val.stokawal);
						  totalHppAwal+=Number(val.hpp);
						  totalHpjAwal+=Number(val.hpj);
						  
						
					  }
				  }catch(e3){
					   alert('getRekapDataAwalFob ' + e3.message);  
				  }	
				  				  
				
				});// end for each
			$('#procMarkas').text("");	
			$('#totalRQtyAwalMarkas').text(format('#,##0.#',totalQtyAwal));		
			$('#totalRHppAwalMarkas').text(format('#,##0.#',totalHppAwal));	
			$('#totalRHpjAwalMarkas').text(format('#,##0.#',totalHpjAwal));	 
		
		}	//end succes			
	});
}
 
function getDataSoFob(){
	 var tgl=$('#tgl1').val();
	 $('#procMarkasKoreksi').text("Process get Data....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'ssd',tg:'m',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtySo=0;
			var totalHpjSo=0;
			var totalHppSo=0;
			$.each(data, function(key, val) {  //alert(val.id);	
				  try{
					  //alert('#qtyawal_'+ val.id);
					  if($('#qtyawalmarkas_'+ val.id).length>0){
						  try{
							 $("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						 $('#qtysomarkas_'+ val.id).text(format('#,##0.#',val.so));
						 $('#hppsomarkas_'+ val.id).text(format('#,##0.#',val.hpp));
						 $('#hpjsomarkas_'+ val.id).text(format('#,##0.#',val.hpj));
						 totalQtySo+=Number(val.so);
						 totalHppSo+=Number(val.hpp);
						 totalHpjSo+=Number(val.hpj);
						 hitungSelisihMarkas(val.id);
					  }
					  
				  }catch(e3){
					   alert('getDataSoFob ' + e3.message);  
				  }					  
				
				});// end for each
			$('#procMarkasKoreksi').text("");	
			$('#totalQtySoMarkas').text(format('#,##0.#',totalQtySo));		
			$('#totalHppSoMarkas').text(format('#,##0.#',totalHppSo));	
			$('#totalHpjSoMarkas').text(format('#,##0.#',totalHpjSo));
			hitungTotalKoreksiMarkas();			 
		}	//end succes			
	});
}

function hitungTotalKoreksiMarkas(){
  var totalQty=0;
  var totalHpp=0;
  var totalHpj=0;
  $('.qtyKoreksiMarkas').each(function(){
	   totalQty+=Number(removeCommas($('#' + this.id).text()));
	  
  });	
  $('.hppKoreksiMarkas').each(function(){
	   totalHpp+=Number(removeCommas($('#' + this.id).text()));
	  
  });	
  
  $('.hpjKoreksiMarkas').each(function(){
	   totalHpj+=Number(removeCommas($('#' + this.id).text()));
	  
  });	
  $('#totalQtyKoreksiMarkas').text(format('#,##0.#',totalQty));
  $('#totalHppKoreksiMarkas').text(format('#,##0.#0',totalHpp));
  $('#totalHpjKoreksiMarkas').text(format('#,##0.#0',totalHpj));
  //alert(' Total  Qty ' + totalQty + ' Total Hpp ' + totalHpp + ' Total  Hpj ' + totalHpj)
  
}
function hitungSelisihMarkas(id){
	var qtyAwal=0;
	var hppAwal=0
	var hpjAwal=0;
	var qtySo=0;
	var hppSo=0
	var hpjSo=0;
	var qtyKoreksi=0;
	var hppKoreksi=0
	var hpjKoreksi=0;
	
	qtyAwal=removeCommas($('#qtyawalmarkas_' + id).text());
	hppAwal=removeCommas($('#hppawalmarkas_' + id).text());
	hpjAwal=removeCommas($('#hpjawalmarkas_' + id).text());
	qtySo=removeCommas($('#qtysomarkas_' + id).text());
	hppSo=removeCommas($('#hppsomarkas_' + id).text());
	hpjSo=removeCommas($('#hpjsomarkas_' + id).text());
	qtyKoreksi=qtySo-qtyAwal;
	hppKoreksi=hppSo-hppAwal;
	hpjKoreksi=hpjSo-hpjAwal;
	$('#qtykoreksimarkas_' + id).text(format('#,##0.#',qtyKoreksi));
	$('#hppkoreksimarkas_' + id).text(format('#,##0.#',hppKoreksi));
	$('#hpjkoreksimarkas_' + id).text(format('#,##0.#',hpjKoreksi));
	
	
}

//======================== END FOB ==========================//



//======================== START CMT ==========================//

 function getDataAwalCmt(){
	 var tgl=$('#tgl1').val();
	 $('#procDistribusi').text("Process get Data....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'sagt',tg:'g',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtyAwal=0;
			var totalHpjAwal=0;
			var totalHppAwal=0;
			$.each(data, function(key, val) {  //alert(val.id);	
				  try{
					  //alert('#qtyawal_'+ val.id);
					  //alert('[id="qtyawaldistribusi_'+ val.id +'"]');
					  if($('#qtyawaldistribusi_'+ val.id).length>0){
						  try{
							 $("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						  $('#qtyawaldistribusi_'+ val.id).text(format('#,##0.#',val.stokawal));
						  $('#hppawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpp));
						  $('#hpjawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpj));
						  totalQtyAwal+=Number(val.stokawal);
						  totalHppAwal+=Number(val.hpp);
						  totalHpjAwal+=Number(val.hpj);
						  
						   hitungSelisihDistribusi(val.id);
					  }
				  }catch(e3){
					   alert('getDataAwalCmt ' + e3.message);  
				  }	
				  				  
				
				});// end for each
			$('#procDistribusi').text("");	
			$('#totalQtyAwalDistribusi').text(format('#,##0.#',totalQtyAwal));		
			$('#totalHppAwalDistribusi').text(format('#,##0.#',totalHppAwal));	
			$('#totalHpjAwalDistribusi').text(format('#,##0.#',totalHpjAwal));	 
			hitungTotalKoreksiDistribusi();
		}	//end succes			
	});
}

function getRekapDataAwalCmt(){
	var tgl=$('#tgl1').val();
	$('#procDistribusi').text("Process get Data rekap Stok Awal Distribusi ....");
	$.ajax({
	   url:"laporan_so_suho_rekap_data.php",
	   type:"POST",
	   cache: false,
	   dataType:'json',
	   data:{t:tgl,j:'sarkp',tg:'g',b:barcode},// 
	   success: function(data) { //alert(data);
		   //alert(data);
		   //return;
		   var count=0;
		   var totalQtyAwal=0;
		   var totalHpjAwal=0;
		   var totalHppAwal=0;
		   $.each(data, function(key, val) {  //alert(val.id);	
				 try{
					 //alert('#qtyawal_'+ val.id);
					 if($('#qtyrawaldistribusi_'+ val.id).length>0){
						 try{
							//$("[id='r_"+ val.id +"']").show();
						}catch(e){
							
						} 
						 $('#qtyrawaldistribusi_'+ val.id).text(format('#,##0.#',val.stokawal));
						 $('#hpprawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpp));
						 $('#hpjrawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpj));
						 
						 // $('#qtyrawaldistribusi_'+ val.id).text(format('#,##0.#',val.stokawal));
						 // $('#hpprawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpp));
						 // $('#hpjrawaldistribusi_'+ val.id).text(format('#,##0.#',val.hpj));
						 totalQtyAwal+=Number(val.stokawal);
						 totalHppAwal+=Number(val.hpp);
						 totalHpjAwal+=Number(val.hpj);
						 
					   
					 }
				 }catch(e3){
					  alert('getRekapDataAwalCmt ' + e3.message);  
				 }	
								   
			   
			   });// end for each
		   $('#procDistribusi').text("");	
		   $('#totalRQtyAwalDistribusi').text(format('#,##0.#',totalQtyAwal));		
		   $('#totalRHppAwalDistribusi').text(format('#,##0.#',totalHppAwal));	
		   $('#totalRHpjAwalDistribusi').text(format('#,##0.#',totalHpjAwal));	 
	   
	   }	//end succes			
   });
}


function getDataSoCmt(){
	 var tgl=$('#tgl1').val();
	 $('#procDistribusiKoreksi').text("Process get Data....");
	 $.ajax({
		url:"laporan_so_suho_rekap_data.php",
		type:"POST",
		cache: false,
		dataType:'json',
		data:{t:tgl,j:'ssd',tg:'g',b:barcode,n:nama,jk:jenis_selisih},// jenis distribusi
		success: function(data) { //alert(data);
			//alert(data);
			//return;
			var count=0;
			var totalQtySo=0;
			var totalHpjSo=0;
			var totalHppSo=0;
			$.each(data, function(key, val) {  

				  try{
				  	  alert(val.id);	
					  //alert('#qtyawal_'+ val.id);
					  if($('#qtyawaldistribusi_'+ val.id).length>0){
						  try{
							 $("[id='r_"+ val.id +"']").show();
						 }catch(e){
							 
						 } 
						 $('#qtysodistribusi_'+ val.id).text(format('#,##0.#',val.so));
						 $('#hppsodistribusi_'+ val.id).text(format('#,##0.#',val.hpp));
						 $('#hpjsodistribusi_'+ val.id).text(format('#,##0.#',val.hpj));
						 totalQtySo+=Number(val.so);
						 totalHppSo+=Number(val.hpp);
						 totalHpjSo+=Number(val.hpj);
						 hitungSelisihDistribusi(val.id);
					  }
					  
				  }catch(e3){
					   alert(' getDataSoCmt ' + e3.message);  
				  }					  
				
				});// end for each
			$('#procDistribusiKoreksi').text("");	
			$('#totalQtySoDistribusi').text(format('#,##0.#',totalQtySo));		
			$('#totalHppSoDistribusi').text(format('#,##0.#',totalHppSo));	
			$('#totalHpjSoDistribusi').text(format('#,##0.#',totalHpjSo));
			hitungTotalKoreksiDistribusi();			 
		}	//end succes			
	});
}

function hitungTotalKoreksiDistribusi(){
  var totalQty=0;
  var totalHpp=0;
  var totalHpj=0;
  try{
	  $('.qtyKoreksiDistribusi').each(function(){
		   totalQty+=Number(removeCommas($('[id="' + this.id +'"]').text()));
		  
	  });	
	  $('.hppKoreksiDistribusi').each(function(){
		   totalHpp+=Number(removeCommas($('[id="' + this.id +'"]').text()));
		  
	  });	
	  
	  $('.hpjKoreksiDistribusi').each(function(){
		   totalHpj+=Number(removeCommas($('[id="' + this.id +'"]').text()));
		  
	  });	
	  $('#totalQtyKoreksiDistribusi').text(format('#,##0.#',totalQty));
	  $('#totalHppKoreksiDistribusi').text(format('#,##0.#0',totalHpp));
	  $('#totalHpjKoreksiDistribusi').text(format('#,##0.#0',totalHpj)); 
  }catch(e){
	  alert('Cek hitungTotalKoreksiDistribusi()'+ e.message);
  }
  
  //alert(' Total  Qty ' + totalQty + ' Total Hpp ' + totalHpp + ' Total  Hpj ' + totalHpj)
  
}


function hitungSelisihDistribusi(id){
	var qtyAwal=0;
	var hppAwal=0
	var hpjAwal=0;
	var qtySo=0;
	var hppSo=0
	var hpjSo=0;
	var qtyKoreksi=0;
	var hppKoreksi=0
	var hpjKoreksi=0;
	try{
		qtyAwal=removeCommas($('#qtyawaldistribusi_' + id).text());
		hppAwal=removeCommas($('#hppawaldistribusi_' + id).text());
		hpjAwal=removeCommas($('#hpjawaldistribusi_' + id).text());
		qtySo=removeCommas($('#qtysodistribusi_' + id).text());
		hppSo=removeCommas($('#hppsodistribusi_' + id).text());
		hpjSo=removeCommas($('#hpjsodistribusi_' + id).text());
		qtyKoreksi=qtySo-qtyAwal;
		hppKoreksi=hppSo-hppAwal;
		hpjKoreksi=hpjSo-hpjAwal;
		$('#qtykoreksidistribusi_' + id).text(format('#,##0.#',qtyKoreksi));
		$('#hppkoreksidistribusi_' + id).text(format('#,##0.#',hppKoreksi));
		$('#hpjkoreksidistribusi_' + id).text(format('#,##0.#',hpjKoreksi));
		
	}catch(e){
		
	}
	
	
}

//======================== END CMT ====================== //

function reFreshDistribusi(){
	getDataAwalCmt();
	getDataSoCmt();
 }
 function reFreshMarkas(){
	 getDataAwalFob();
	getDataSoFob();
 }
 function reFreshReshare(){
	 getDataAwalSuho();
	 getDataSoOutlet();
 }

//Detail
function uploadPernyataan(id_gudang,tanggal,jenis){
	$('#v_gudang').val(id_gudang);
	$('#v_tgl').val(tanggal);
	$('#v_jenis_gudang').val(jenis);
    $('#fupload').click();	
	
}
 