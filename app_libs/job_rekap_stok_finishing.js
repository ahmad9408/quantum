$(document).ready(function(){
		/*$('#btnExportDataStok').click(function(){
			//$('#frmFilter').attr('target','_blank');
			$('#frmFilter').submit();
			//$('#frmFilter').attr('target','_self');
		})*/
		
		activateAutoCompleteAll();
		$('#btnExportDataStok2').hide();
		
		
	})
	
	function exportStok(){
		console.log('exportStok()');
		$('#frmFilter').attr('target','_blank');
		try{
			//$('#frmFilter').submit();
			$('#btnExportDataStok').click();
		}catch(e){
		   alert(e.message);	
		}
		console.log('exportStok()....');
		$('#frmFilter').attr('target','_self');
	}
	
	function exportStokAkhir(){
		console.log('exportStok()');
		$('#frmFilter').attr('action','rekap_stok_finishing_complete_stock_only_export.php?action=export');
		$('#frmFilter').attr('target','_blank');
		try{
			//$('#frmFilter').submit();
			$('#btnExportDataStok').click();
		}catch(e){
		   alert(e.message);	
		}
		console.log('exportStok()....');
		$('#frmFilter').attr('action','job_rekap_stok_finishing.php?action=search');
		$('#frmFilter').attr('target','_self');
	}
	
	function activateAutoCompleteAll(){
		/*for (var selector in config) {//bisa menggunankan ini
		  $(selector).chosen(config[selector]);
		}*/
		activateAutoComplete($('#txt_organization'));
		activateAutoComplete($('#txtjenis'));
		activateAutoComplete($('#pabrik1'));
		activateAutoComplete($('#jenis_pabrik'));
		activateAutoComplete($('#txtmarkas'));
		activateAutoComplete($('#txtprodukpilihan'));
		activateAutoComplete($('#tanggal'));
		
		
		
	}
	
	function deactivateAutoComplete(component){
	   component.chosen("destroy");	
	}
	
	function activateAutoComplete(component){
		component.chosen({});		
	}
	


 
	
	
	//end chosen


function validation(){
	//alert('TEST');
	splitSupplier();
	return true;
}

function splitSupplier(){
    var supplier;
	
	supplier=$.trim($('#txt_supp').val());
	
	//alert(supplier);
	
	
	if(supplier==''){
		
	}else{
		var d=supplier.split('#');
		var kode_supplier='';
		var nama_supplier='';
		try{
		   kode_supplier=d[0];		
		   nama_supplier=d[1];
		}catch(e){
			
		}
		kode_supplier=$.trim(kode_supplier);
		$('#txt_kode_supp').val(kode_supplier);
	}
	
}

function hitungStokPertanggal(){
	    $('#debug').text("hitung stok pertanggal");
		var outlet='';
		var barcode='';
		var barcode13='';
		var d1;
		var stokAkhir=0;
		var stokAwal=0;
		var si=0;
		var ro=0;
		var so=0;
		var ri=0;
		var k=0;
		try{
			$('.datarow').each(function(index,elemen){
				//alert(this.id );	
				
				d1=this.id.split('_');
				outlet=d1[0];
				barcode=d1[1];
				barcode13=d1[2];
				stokAwal=removeCommas($('#stokawal_'+ outlet+'_'+barcode).text());
				stokAkhir=Number(stokAwal);
				//alert ('Stok AWal ' +stokAwal+' outlet :' + outlet +' Barcode '+ barcode); 
				for(i=1;i<=tanggalAkhir;i++){
					si=removeCommas($('#si_'+ outlet + '_'+ barcode+'_'+ i).text());				
					ro=removeCommas($('#ro_'+ outlet + '_'+ barcode+'_'+ i).text());
					so=removeCommas($('#so_'+ outlet + '_'+ barcode+'_'+ i).text());
					ri=removeCommas($('#ri_'+ outlet + '_'+ barcode+'_'+ i).text());
					k=removeCommas($('#k_'+ outlet + '_'+ barcode+'_'+ i).text());
					stokAkhir+=Number(si) -Number(ro)-Number(so)+Number(ri) + Number(k);
					if(isNaN(Number(si))){alert('#si_'+ outlet + '_'+ barcode+'_'+ i);}
					if(isNaN(Number(so))){alert('#so_'+ outlet + '_'+ barcode+'_'+ i);}
					if(isNaN(Number(ri))){alert('#ri_'+ outlet + '_'+ barcode+'_'+ i);}
					if(isNaN(Number(ro))){alert('#ro_'+ outlet + '_'+ barcode+'_'+ i);}
					if(isNaN(Number(k))){alert('#k_'+ outlet + '_'+ barcode+'_'+ i);}
					$('#stok_'+ outlet +'_'+ barcode13 +  '_'+ barcode+'_'+ i).text(format('#,##0.#',stokAkhir));
					$('#debug').text('stok akhir :'+ stokAkhir );
					try{
						$('#stok_'+ outlet +'_'+ barcode13 +  '_'+ barcode).text(format('#,##0.#',stokAkhir));
						$('#debug').text('stok akhir :'+ stokAkhir );
					}catch(ec){
						$('#debug').text(ec.message);
					}
					
					
					/*if(today==i){
						//alert('hari ini adalah ' + '#'+i+'#'+outlet+'#'+periode+'#'+barcode13+'#'+barcode);
						$('#debug').text('stok akhir :'+ stokAkhir +'  : ' + '#'+i+'#'+outlet+'#'+periode+'#'+barcode13+'#'+barcode);//document.getElementById(i+'#'+outlet+'#'+periode+'#'+barcode13+'#'+barcode).innerHTML=format('#,##0.#',stokAkhir);
						$("[id='"+i+'#'+outlet+'#'+periode+'#'+barcode13+'#'+barcode+"']").text(stokAkhir);
						//$('#'+i+'#'+outlet+'#'+periode+'#'+barcode13+'#'+barcode).text(format('#,##0.#',stokAkhir));
					}*/
				}
			});
			
			
		}catch(e){
		   alert('hitungStokPertanggal() ' + e.message);	
		}
		
	}
	
	function cariProduk(){
		//var barcode=document.getElementById("txt_barcode").value;
		//var barcode=document.getElementById("txt_barcode").innerHTML;
		var barcode=$('#barcode').val();
		//alert(barcode);
		var model=$('#txt_model').val();
		var url="window_model.php?textid=barcode&b="+ barcode +"&id_nm=txt_model&m="+ model;
		//alert(url);
		detailsWindow = window.open(url,"window_model","width=800,height=600,scrollbars=yes");
		detailsWindow.focus();
	}
	
	function checkBarcode(){
	   var barcode=$('#txt_barcode').val();
	   if(barcode==""){
		   alert("barcode barang masih kosong, silahkan pilih produk yang akan dilihat");
		   return false;	
	   }else{
		  return true;	 
	   }
	   
	}
	
	function clearBarcode(){
		$('#txt_barcode').val('');
	}
function showTable(){
	$('#txtTable').show('slow');
}

function removeCommas(nilai){
	  var hasil='';
	  hasil=nilai.replace(/[^\d\.\-\ ]/g, '');  
	  return hasil;	  
  }

function addReshare(kode_reshare){
	var nilai_lama=$('#otl_export').val();
	if($("[id='cb_"+ kode_reshare +"']").is(":checked")==true){
		
        $('#otl_export').val(nilai_lama  + kode_reshare + ';');
	}else{
		 $('#otl_export').val(nilai_lama.replace(kode_reshare + ';',''));
	}
  	
}

function showPilihan(){
   $('#pil_reshare_export').modal({minHeight:500,maxHeight:500,minWidth:400,maxWidth:400,autoResize:true});
   return false;	
}


function updateAktifAll(){
	var d;
	if($('#cb_all').is(":checked")==true){
		$('.cb_outlet').each(function(){
			 $('#' + this.id).attr('checked',true);
			 d=this.id.split('_');
			 addReshare(d[1]);
		});
		
	}else{
		
		$('.cb_outlet').each(function(){
			 $('#' + this.id).attr('checked',false);
			  $('#otl_export').val('');
		});
	}
}

function pilihDetail(){

    //alert($('#pabrik1').val());
	if($('#pabrik1').val()=='ALL'){
		
	}else{
	   return;	
	}
	
 
   if($('#cb_detail').is(":checked")){
	   $('#btnpilih').show();
	   $('#exprt').show();
   }else{
	  $('#btnpilih').hide(); 
	   $('#exprt').hide(); 
   }
	
} 