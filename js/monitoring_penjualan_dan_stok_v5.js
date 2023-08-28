Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };


 function ambil_do(gudang){
 	alert(gudang);
 }
 
 function ambil_penjualan(gudang){
var cari=$("#cari").val();
var dari=$("#dari").val();
var sampai=$("#sampai").val();
var berdasar=$("#berdasar").val();
var pilih=$("#pilihan1").val();
var jenis=$("#txtjenis").val();

 if(jenis==2){
		var json		= "json"; 
	 }else{
		 	var json	= "json";
		 }
			  $.ajax({
			  type: 'POST',
			  url: 'monitoring_penjualan_reshare_proses_v2.php',
			  data: {cari:cari,dari:dari,sampai:sampai,gudang:gudang,berdasar:berdasar,pilih:pilih,jenis:jenis}, 
			  dataType: json,
			  success: function(data){
				   if(jenis==2){
					 
					   }
		 			 $("#load_"+gudang).hide();
					 
			  		$.each(data, function(key, val) 
             		{		
					
						if(gudang=='g'){var om=val.g;}else{var om=val.o;}
						  $("#"+gudang+"_qty"+val.o).html("<span class='mylink' onclick=pindah('"+gudang+"','"+om+"')>"+parseFloat(val.qty).formatMoney(0, '.', ',')+"</span>");
						  $("#"+gudang+"_amount"+val.o).html(parseFloat(val.bruto).formatMoney(2, '.', ','));
						  
						  $("#"+gudang+"_bruto"+val.o).html(parseFloat(val.brut).formatMoney(2, '.', ','));
            		}); 
					if(jenis==2){
						 
						hitung_omset1();
					}else{
						hitung();
						}
			  }
			  
			});
	
}
 
 var n=0;
function hitung_omset1(){
	 
	  var qty_res=0;

 var amount_res=0;
 var bruto_res=0;
 var trr_qty_res=0;
 var trr_amount_res=0;
 var selisih_qty_res=0;
 var selisih_nilai_res=0; 
 var trr_bruto_res=0;
 var tselisih_qty_res=0;
 var tselisih_nilai_res=0;
 var tselisih_bruto_res=0;
	 n++;
	 
	var total_reshare=$("#b_r").val();
	 
		 
		 for(var i=1;i<=total_reshare;i++){
			 
				 var id_res=$("#id_outlet"+i).text().trim();	
				qty_res+=parseFloat($("#r_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				amount_res+=parseFloat($("#r_amount"+id_res).text().trim().replace(/,/g, ''), 10);
				bruto_res+=parseFloat($("#r_bruto"+id_res).text().trim().replace(/,/g, ''), 10);
				
				trr_qty_res+=parseFloat($("#rr_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				trr_amount_res+=parseFloat($("#rr_amount"+id_res).text().trim().replace(/,/g, ''), 10); 
				trr_bruto_res+=parseFloat($("#rr_bruto"+id_res).text().trim().replace(/,/g, ''), 10);
				
				selisih_qty_res=parseFloat($("#r_qty"+id_res).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rr_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				
				
				 
			 
				$("#net_qty"+id_res).text(Number(selisih_qty_res).formatMoney(0, '.', ','));
			 
				selisih_nilai_res=parseFloat($("#r_amount"+id_res).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rr_amount"+id_res).text().trim().replace(/,/g, ''), 10); 
				
				selisih_bruto_res=parseFloat($("#r_bruto"+id_res).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rr_bruto"+id_res).text().trim().replace(/,/g, ''), 10);
			 
				$("#net_amount"+id_res).text(Number(selisih_nilai_res).formatMoney(0, '.', ',')); 
				$("#bruto_amount"+id_res).text(Number(selisih_bruto_res).formatMoney(0, '.', ','));
				tselisih_qty_res+=selisih_qty_res; 
				tselisih_nilai_res+=selisih_nilai_res;  
				tselisih_bruto_res+=selisih_bruto_res;
				 
				
			}
			 $("#r_tqty").html(parseFloat(qty_res).formatMoney(0, '.', ','));
			$("#r_tamount").html(parseFloat(amount_res).formatMoney(0, '.', ','));
			$("#r_bruto").html(parseFloat(bruto_res).formatMoney(0, '.', ','));
			 
			$("#rr_tqty").html(parseFloat(trr_qty_res).formatMoney(0, '.', ','));
			$("#rr_tamount").html(parseFloat(trr_amount_res).formatMoney(0, '.', ','));
			$("#rr_bruto").html(parseFloat(trr_bruto_res).formatMoney(0, '.', ','));
			 
			$("#net_tqty").html(parseFloat(tselisih_qty_res).formatMoney(0, '.', ','));
			$("#net_tamount").html(parseFloat(tselisih_nilai_res).formatMoney(0, '.', ','));  
			$("#bruto_tamount").html(parseFloat(tselisih_bruto_res).formatMoney(0, '.', ',')); 
}


function ambil_retur(gudang){
 
var cari=$("#cari").val();
var dari=$("#dari").val();
var sampai=$("#sampai").val();
var berdasar=$("#berdasar").val();
var pilih=$("#pilihan1").val();
var jenis=$("#txtjenis").val();
 
			  $.ajax({
			  type: 'POST',
			  url: 'monitoring_penjualan_reshare_proses_v2.php',
			  data: {cari:cari,dari:dari,sampai:sampai,gudang:gudang,berdasar:berdasar,pilih:pilih,jenis:jenis}, 
			  dataType: 'json',
			  success: function(data){
				   
		 			// $("#load_"+gudang).hide();
					 
			  		$.each(data, function(key, val) 
             		{		
					// alert(gudang+' '+val.o+' '+val.qty+' '+val.nilai);
						if(gudang=='rg'){var om=val.g; 
						}else{var om=val.o;}
						  $("#"+gudang+"_qty"+val.o).html("<span class='mylink' onclick=pindah_retur('"+gudang+"','"+om+"')>"+parseFloat(val.qty).formatMoney(0, '.', ',')+"</span>");
						  $("#"+gudang+"_amount"+val.o).html(parseFloat(val.nilai).formatMoney(2, '.', ','));  
						  $("#"+gudang+"_bruto"+val.o).html(parseFloat(val.brut).formatMoney(2, '.', ',')); 
            		});  
					if(jenis==2){
						 
						hitung_omset1();
					}else{
						hitung();
						}
			  }
			  
			});
	
}


function ambil_stok(gudang){
var cari=$("#cari").val();
var dari=$("#dari").val();
var sampai=$("#sampai").val();
var berdasar=$("#berdasar").val(); 
var jenis=$("#txtjenis").val();
 
var pilih=$("#pilihan1").val();
			  $.ajax({
			  type: 'POST',
			  url: 'monitoring_penjualan_reshare_proses_v2.php',
			  data: {cari:cari,dari:dari,sampai:sampai,gudang:gudang,berdasar:berdasar,pilih:pilih,jenis:jenis,j:jenis}, 
			  dataType: 'json',
			  success: function(data){
				    $("#load_"+gudang).hide();
			  		$.each(data, function(key, val) 
             		{		
					 if(gudang=='sg'){var om=val.oi;}else{var om=val.o;}
					 
						  $("#"+gudang+"_qty"+val.o).html("<span class='mylink' onclick=pindah1('"+gudang+"','"+om+"')>"+parseFloat(val.stok).formatMoney(0, '.', ',')+"</span>");
						  $("#"+gudang+"_amount"+val.o).html(parseFloat(val.hpj).formatMoney(2, '.', ','));
            		}); 
					hitung_stok();	
					if(jenis==2){
						  
					}else{
						hitung_stok();
						}
			  }
			  
			});
	
}



var hits1=0;
var qty_sr=0; var amount_sr=0;
var qty_sm=0; var amount_sm=0;
var qty_sg=0; var amount_sg=0;
var stotqtyallretur=0;
var stotamountallretur=0;
function hitung_stok(){
hits1++;

if(hits1==6){
	 
		var tot_gud_sr=$("#b_r").val();
		var tot_gud_sm=$("#b_m").val();
		var tot_gud_sg=$("#b_g").val();
		  
		 for(var i=1;i<=tot_gud_sr;i++){
				var id_res=$("#id_outlet"+i).text().trim();	
				qty_sr+=parseFloat($("#sr_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				amount_sr+=parseFloat($("#sr_amount"+id_res).text().trim().replace(/,/g, ''), 10);
			}
			 
			$("#sr_tqty").html(parseFloat(qty_sr).formatMoney(0, '.', ','));
			$("#sr_tamount").html(parseFloat(amount_sr).formatMoney(0, '.', ','));
			 
			 
			$("#sqty_reshare_all").html(parseFloat(qty_sr).formatMoney(0, '.', ','));
			$("#samount_reshare_all").html(parseFloat(amount_sr).formatMoney(0, '.', ','));  
			 
		 
		 for(var i=1;i<=tot_gud_sm;i++){
				var id_m=$("#id_markas"+i).text().trim();	
				qty_sm+=parseFloat($("#sm_qty"+id_m).text().trim().replace(/,/g, ''), 10);
				amount_sm+=parseFloat($("#sm_amount"+id_m).text().trim().replace(/,/g, ''), 10);
			}
			 
			
			$("#sm_tqty").html(parseFloat(qty_sm).formatMoney(0, '.', ','));
			$("#sm_tamount").html(parseFloat(amount_sm).formatMoney(0, '.', ','));
			
			 
			$("#sqty_markas_all").html(parseFloat(qty_sm).formatMoney(0, '.', ','));
			$("#samount_markas_all").html(parseFloat(amount_sm).formatMoney(0, '.', ','));
			
			 
			  
			  
		 for(var i=1;i<=tot_gud_sg;i++){ 	
				var id_g=$("#id_gunas"+i).text().trim()
				qty_sg+=parseFloat($("#sg_qty"+id_g).text().trim().replace(/,/g, ''), 10);
				amount_sg+=parseFloat($("#sg_amount"+id_g).text().trim().replace(/,/g, ''), 10);
			}
			$("#sg_tqty").html(parseFloat(qty_sg).formatMoney(0, '.', ','));
			$("#sg_tamount").html(parseFloat(amount_sg).formatMoney(0, '.', ',')); 
			$("#sqty_gunas_all").html(parseFloat(qty_sg).formatMoney(0, '.', ','));
			$("#samount_gunas_all").html(parseFloat(amount_sg).formatMoney(0, '.', ','));  
			
			var stotqtyall=parseFloat(qty_sr)+parseFloat(qty_sm)+parseFloat(qty_sg);
			var stotamountall=parseFloat(amount_sr)+parseFloat(amount_sm)+parseFloat(amount_sg);
			
			$("#clsqtys").html(parseFloat(stotqtyall).formatMoney(0, '.', ','));
			$("#clsamounts").html(parseFloat(stotamountall).formatMoney(0, '.', ',')); 
			
			 
			 
		 
	}
	pin();
}



var hit=0;
var qty_r=0; var amount_r=0;  var trr_qty=0; var trr_amount=0;
var qty_m=0; var amount_m=0;  var trm_qty=0; var trm_amount=0;
var qty_g=0; var amount_g=0;  var trg_qty=0; var trg_amount=0;


var selisih_qty=0; 		var tselisih_qty=0;
var selisih_nilai=0; 	var tselisih_nilai=0;

var selisih_m_qty=0;		var tselisih_m_qty=0;
var selisih_m_nilai=0; 		var tselisih_m_nilai=0;
  

function hitung(){
	 
hit++;
if(hit==6){
		var tot_gud_r=$("#b_r").val();
		var tot_gud_m=$("#b_m").val();
		var tot_gud_g=$("#b_g").val();
		 for(var i=1;i<=tot_gud_r;i++){
				var id_res=$("#id_outlet"+i).text().trim();	
				qty_r+=parseFloat($("#r_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				amount_r+=parseFloat($("#r_amount"+id_res).text().trim().replace(/,/g, ''), 10);
				
				trr_qty+=parseFloat($("#rr_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				trr_amount+=parseFloat($("#rr_amount"+id_res).text().trim().replace(/,/g, ''), 10); 
				
				selisih_qty=parseFloat($("#r_qty"+id_res).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rr_qty"+id_res).text().trim().replace(/,/g, ''), 10);
				
			 
				$("#net_qty"+id_res).text(Number(selisih_qty).formatMoney(0, '.', ','));
				selisih_nilai=parseFloat($("#r_amount"+id_res).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rr_amount"+id_res).text().trim().replace(/,/g, ''), 10); 
				$("#net_amount"+id_res).text(Number(selisih_nilai).formatMoney(0, '.', ','));
				tselisih_qty+=selisih_qty;
				tselisih_nilai+=selisih_nilai;
				
				
			}
			$("#r_tqty").html(parseFloat(qty_r).formatMoney(0, '.', ','));
			$("#r_tamount").html(parseFloat(amount_r).formatMoney(0, '.', ','));
			 
			$("#rr_tqty").html(parseFloat(trr_qty).formatMoney(0, '.', ','));
			$("#rr_tamount").html(parseFloat(trr_amount).formatMoney(0, '.', ','));
			
			
			$("#qty_retur_reshare_all").html(parseFloat(trr_qty).formatMoney(0, '.', ','));
			$("#amount_retur_reshare_all").html(parseFloat(trr_amount).formatMoney(0, '.', ','));
			 
			
			$("#qty_reshare_all").html(parseFloat(qty_r).formatMoney(0, '.', ','));
			$("#amount_reshare_all").html(parseFloat(amount_r).formatMoney(0, '.', ',')); 
			
			
			$("#net_tqty").html(parseFloat(tselisih_qty).formatMoney(0, '.', ','));
			$("#net_tamount").html(parseFloat(tselisih_nilai).formatMoney(0, '.', ',')); 
			
			
			$("#net_tqty").html(parseFloat(tselisih_qty).formatMoney(0, '.', ','));
			$("#net_tamount").html(parseFloat(tselisih_nilai).formatMoney(0, '.', ','));
			
			$("#qty_net_reshare_all").html(parseFloat(tselisih_qty).formatMoney(0, '.', ','));
			$("#amount_net_reshare_all").html(parseFloat(tselisih_nilai).formatMoney(0, '.', ',')); 
			
			 
			
			  
			
		
		 for(var i=1;i<=tot_gud_m;i++){
				var id_m=$("#id_markas"+i).text().trim();	
				qty_m+=parseFloat($("#m_qty"+id_m).text().trim().replace(/,/g, ''), 10);
				amount_m+=parseFloat($("#m_amount"+id_m).text().trim().replace(/,/g, ''), 10); 
				selisih_m_qty=parseFloat($("#m_qty"+id_m).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rm_qty"+id_m).text().trim().replace(/,/g, ''), 10);
			 	$("#net_m_qty"+id_m).text(Number(selisih_m_qty).formatMoney(0, '.', ','));
				selisih_m_nilai=parseFloat($("#m_amount"+id_m).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rm_amount"+id_m).text().trim().replace(/,/g, ''), 10); 
				$("#net_m_amount"+id_m).text(Number(selisih_m_nilai).formatMoney(0, '.', ','));
				
					trm_qty+=parseFloat($("#rm_qty"+id_m).text().trim().replace(/,/g, ''), 10);
				    trm_amount+=parseFloat($("#rm_amount"+id_m).text().trim().replace(/,/g, ''), 10); 
				
				
			}
			 
			
			$("#m_tqty").html(parseFloat(qty_m).formatMoney(0, '.', ','));
			$("#m_tamount").html(parseFloat(amount_m).formatMoney(0, '.', ','));
			$("#qty_markas_all").html(parseFloat(qty_m).formatMoney(0, '.', ','));
			$("#amount_markas_all").html(parseFloat(amount_m).formatMoney(0, '.', ',')); 
			$("#rm_tqty").html(parseFloat(trm_qty).formatMoney(0, '.', ','));
			$("#rm_tamount").html(parseFloat(trm_amount).formatMoney(0, '.', ','));
			 
			
			
			$("#qty_retur_markas_all").html(parseFloat(trm_qty).formatMoney(0, '.', ','));
			$("#amount_retur_markas_all").html(parseFloat(trm_amount).formatMoney(0, '.', ',')); 
			
			var sel_m_qty=Number(qty_m)-Number(trm_qty);
			var sel_m_amount=Number(amount_m)-Number(trm_amount);
			
			
			$("#net_m_tqty").html(parseFloat(sel_m_qty).formatMoney(0, '.', ','));
			$("#net_m_tamount").html(parseFloat(sel_m_amount).formatMoney(0, '.', ','));
			
			 
			
			$("#qty_net_markas_all").html(parseFloat(sel_m_qty).formatMoney(0, '.', ','));
			$("#amount_net_markas_all").html(parseFloat(sel_m_amount).formatMoney(0, '.', ','));
			
			 
			 
			
			  
			  
		 for(var i=1;i<=tot_gud_g;i++){ 	
				var id_g=$("#id_gunas"+i).text().trim()
				qty_g+=parseFloat($("#g_qty"+id_g).text().trim().replace(/,/g, ''), 10);
				amount_g+=parseFloat($("#g_amount"+id_g).text().trim().replace(/,/g, ''), 10);
				
					trg_qty+=parseFloat($("#rg_qty"+id_g).text().trim().replace(/,/g, ''), 10);
				    trg_amount+=parseFloat($("#rg_amount"+id_g).text().trim().replace(/,/g, ''), 10); 
					
					
					selisih_g_qty=parseFloat($("#g_qty"+id_g).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rg_qty"+id_g).text().trim().replace(/,/g, ''), 10); 
				selisih_g_nilai=parseFloat($("#g_amount"+id_g).text().trim().replace(/,/g, ''), 10)-parseFloat($("#rg_amount"+id_g).text().trim().replace(/,/g, ''), 10);
				
				$("#net_g_qty"+id_g).text(Number(selisih_g_qty).formatMoney(0, '.', ','));
				 
				$("#net_g_amount"+id_g).text(Number(selisih_g_nilai).formatMoney(0, '.', ','));
					
			}
			$("#g_tqty").html(parseFloat(qty_g).formatMoney(0, '.', ','));
			$("#g_tamount").html(parseFloat(amount_g).formatMoney(0, '.', ',')); 
		 
			$("#rg_tqty").html(parseFloat(trg_qty).formatMoney(0, '.', ','));
			$("#rg_tamount").html(parseFloat(trg_amount).formatMoney(0, '.', ','));
			
			$("#qty_retur_gunas_all").html(parseFloat(trg_qty).formatMoney(0, '.', ','));
			$("#amount_retur_gunas_all").html(parseFloat(trg_amount).formatMoney(0, '.', ','));
			  
				
				var sel_g_qty=Number(qty_g)-Number(trg_qty);
				var sel_g_amount=Number(amount_g)-Number(trg_amount);
			$("#net_g_tqty").html(parseFloat(sel_g_qty).formatMoney(0, '.', ','));
			$("#net_g_tamount").html(parseFloat(sel_g_amount).formatMoney(0, '.', ','));
			
			
			
			$("#qty_net_gunas_all").html(parseFloat(sel_g_qty).formatMoney(0, '.', ','));
			$("#amount_net_gunas_all").html(parseFloat(sel_g_amount).formatMoney(0, '.', ','));
			 
			
			
			$("#qty_gunas_all").html(parseFloat(qty_g).formatMoney(0, '.', ','));
			$("#amount_gunas_all").html(parseFloat(amount_g).formatMoney(0, '.', ','));  
			 
			var totqtyall=parseFloat(qty_g)+parseFloat(qty_m)+parseFloat(qty_r);
			var totamountall=parseFloat(amount_g)+parseFloat(amount_m)+parseFloat(amount_r);
			
			$("#clsqty").html(parseFloat(totqtyall).formatMoney(0, '.', ','));
			$("#clsamount").html(parseFloat(totamountall).formatMoney(0, '.', ','));
		 
			 
			
		 
	}
	pin();
}

function pindah(gudang,outlet){
 
	if(gudang=="g"){
			$("#txtlocation").val('');	
			$("#txtgudang").val(outlet);
			$("#txtjenis").val("2")
		}else{
	 		$("#txtlocation").val(outlet);
			$("#txtgudang").val(''); 
		}$("#txtgudang").val(outlet);
	 $("#tgl1").val($("#dari").val());
	 $("#tgl2").val($("#sampai").val());
	 var berdasar=$("#berdasar").val();
	 if(berdasar=="nama"){
		 	$("#txt_nama").val($("#cari").val());
			$("#txtrnama").val($("#cari").val());
			
		 }else if(berdasar=="kode"){
			 $("#txtbarcode").val($("#cari").val());
			 }
	 if(gudang=="r"){
	 	$("#f1").attr("action","laporan_penjualan_reshare_v11.php?action=search"); 
	 	$("#f1").attr("target","_BLANK");
		
	}else if(gudang=="m"){
		$("#f1").attr("action","laporan_penjualan_markas.php?action=search");
		$("#f1").attr("target","_BLANK"); 
		}
		else if(gudang=="g"){
		$("#f1").attr("action","laporan_penjualan_distribusi.php?action=search");
		$("#f1").attr("target","_BLANK");	 
		}
		
		
	 $("#sim").click();
	 $("#f1").attr("action","monitoring_penjualan_dan_stok_v5.php?action=search");
	 $("#f1").attr("target","");
	}
	


function pindah_retur(gudang,outlet){
	//rian retur
	var dari=$("#dari").val();
	var sampai=$("#sampai").val();
	$("#txtlocation").val(outlet);
	$("#tgl1").val(dari);
	$("#tgl2").val(sampai); 
	 var berdasar=$("#berdasar").val();
	 if(berdasar=="nama"){
		 	$("#nama_produk").val($("#cari").val());
			$("#txtrnama").val($("#cari").val()); 
			$("#txt_nama").val($("#cari").val()); 
			$("#txtNama").val($("#cari").val()); 
		 
		 }else if(berdasar=="kode"){
			 $("#txtbarcode").val($("#cari").val());
			 } 
			 $("#f1").attr("method","post");
			 if(gudang=="rr"){
			 	$("#f1").attr("action","laporan_returin_outlet.php?action=search&txtbarcode=&outlet=");
			 }else if(gudang=="rm"){
			 	$("#f1").attr("action","laporan_returin_markas.php?action=search");
			 }
			 
			 
			 	$("#f1").attr("target","_BLANK");
			 
	$("#sim").click();
	$("#f1").attr("action","monitoring_penjualan_dan_stok_v5.php?action=search");
	$("#f1").attr("target","");
	
		 
}	


function pindah1(gudang,outlet){
	 
  
	if(gudang=="sr"){
			$("#txtlocation").val(outlet); 
		} if(gudang=="sm"){
			$("#txtlocation").val(outlet); 
		} if(gudang=="sg"){
			$("#txtlocation").val(outlet); 
		} 
		
	$("#txtgudang").val(outlet);
	 $("#tgl1").val($("#dari").val());
	 $("#tgl2").val($("#sampai").val());
	 $("#txt_pilihan").val($("#pilihan1").val());
	 var berdasar=$("#berdasar").val();
	 if(berdasar=="nama"){
		 	if(gudang=="sg"){
				$("#nama_produk").val($("#cari").val()); 
			}else{ 
				$("#txtNama").val($("#cari").val());
			}
		 }else if(berdasar=="kode"){
			 $("#txtbarcode").val($("#cari").val());
			 }
	 if(gudang=="sr"){
	 	$("#f1").attr("action","laporan_stok_internal_nohpp_v2.php?action=search&b=1"); 
	 	$("#f1").attr("target","_BLANK");
		
	}else if(gudang=="sm"){
		$("#f1").attr("action","laporan_stok_internal_nohpp_v2.php?action=search&b=1");
		$("#f1").attr("target","_BLANK"); 
		}
		else if(gudang=="sg"){
		$("#f1").attr("action","laporan_stok_distribusi_v2.php?action=search&b=1");
		$("#f1").attr("target","_BLANK");	 
		}
		
		
	 $("#sim").click();
	 $("#f1").attr("action","monitoring_penjualan_dan_stok_v5.php?action=search");
	 $("#f1").attr("target","");
	}

function tampil(){
	$(".hill").show();
	$("#sh").hide();
	$("#hi").show();
	}
	
function hilangkan(){
	$(".hill").hide();
	$("#hi").hide();
	$("#sh").show();
	}
var p=0;	
function pin(){
p++;
	if(p==9){
		var tret=parseFloat($(qty_retur_reshare_all).text().replace(/,/g, ''), 10)+parseFloat($(qty_retur_markas_all).text().replace(/,/g, ''), 10)+parseFloat($(qty_retur_gunas_all).text().replace(/,/g, ''), 10);
		var tretm=parseFloat($(amount_retur_reshare_all).text().replace(/,/g, ''), 10)+parseFloat($(amount_retur_markas_all).text().replace(/,/g, ''), 10)+parseFloat($(amount_retur_gunas_all).text().replace(/,/g, ''), 10);
		 $("#clsreturamount").text(parseFloat(tretm).formatMoney(0, '.', ','));
		  $("#clsreturqty").text(parseFloat(tret).formatMoney(0, '.', ','));
		 
		 var tnet=parseFloat($(qty_net_reshare_all).text().replace(/,/g, ''), 10)+parseFloat($(qty_net_markas_all).text().replace(/,/g, ''), 10)+parseFloat($(qty_net_gunas_all).text().replace(/,/g, ''), 10);
		 var tnetm=parseFloat($(amount_net_reshare_all).text().replace(/,/g, ''), 10)+parseFloat($(amount_net_markas_all).text().replace(/,/g, ''), 10)+parseFloat($(amount_net_gunas_all).text().replace(/,/g, ''), 10);
		 
		  $("#clsnetamount").text(parseFloat(tnetm).formatMoney(0, '.', ','));
		  $("#clsnetqty").text(parseFloat(tnet).formatMoney(0, '.', ','));
		 
	}
}

function cek_data1(){
	 var pilihan=$("#pilihan").val();
	  if(pilihan!=""){
	  	$("#cari").val("");
	  }
}