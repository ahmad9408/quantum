var counterProcess=0;
var totalProcess=6;
var prosentasi=0;

$(document).ready(function(){
	$('#txt_organization').chosen({});
	$('#srcbasic').chosen({});
	$('#srckategori').chosen({});
	$('#srcmodel').chosen({});
	$('#srcsize').chosen({});
	
	$('#srckelas').chosen({});
	$('#srcgrade').chosen({});
	$('#txt_outlet').chosen({});
	$('#txt_markas').chosen({});
	
	//Not by me
	$(".trhidetgl").hide();
	date_view();
})

function getStokOutlet(){
	progress();
	$('#ProcessDetail').append("Get data Stok outlet ...</br>");
	$.ajax({
			url:"stok_data.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t:'jo',org:organization,g:outlet},// jenis internal t:1 (internal)
			success: function(data) {  			   
				$.each(data, function(key, val) {  
					 try{
						 
						   $("#o_"+val.id).html("<span class='mylink' onclick=pindah('"+val.id+"')>"+format('#,##0.#',val.qty)+"</span>");
					  }catch(e3){
						   alert('changeJenis ' + e3.message);  
					  }					  
					  
					});// end for each	
					progress();
					
			}//end success				
		});//end ajax
}

function getStokMarkas(){
	progress();
	$('#ProcessDetail').append("Get data Stok Markas ...</br>");
	$.ajax({
			url:"stok_data.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t:'jm',org:organization,g:markas},// jenis internal t:1 (internal)
			success: function(data) { //alert(data);			   
				$.each(data, function(key, val) {  
					 try{
						   $("#m_"+val.id).html("<span class='mylink' onclick=pindah('"+val.id+"')>"+format('#,##0.#',val.qty)+"</span>");
					  }catch(e3){
						   alert('changeJenis ' + e3.message);  
					  }					  
					  
					});// end for each	
					progress();
			}//end success				
		});//end ajax
}

function getStokDistribusi(){
	progress();
	$('#ProcessDetail').append("Get data Stok Distribusi ...</br>");
	$.ajax({
			url:"stok_data.php",
			type:"POST",
			cache: false,
			dataType:'json',
			data:{t:'jd',org:organization},// jenis internal t:1 (internal)
			success: function(data) { //alert(data);			   
				$.each(data, function(key, val) {  
									  
					 try{
						   $("#d_"+val.id).html("<span class='mylink' onclick=pindah('"+val.id+"')>"+format('#,##0.#',val.qty)+"</span>");
					  }catch(e3){
						   alert('changeJenis ' + e3.message);  
					  }					  
					  
					});// end for each	
					progress();
			}//end success				
		});//end ajax
}


function pindah(barcode){
	$("#load_space").html("<form method='post' id='f2' action='monitoring_stok_produk_pilihan_pergudang_v7.php?action=search' target='_BLANK'>"+
						  "<input  type='text' name='txt_barcode' id='txt_barcode' value='"+barcode+"'>"+ 
						  "</form>");
	
	$("#f2").submit();
	$("#load_space").html("");
}

function changeOutlet(){
	var markas='';
	
	markas=$('#txt_outlet').val();
	console.log(markas);
	markas=markas.substr(0,4) +'-O0000';
	console.log(markas);
	//$("#txt_markas").val(markas);
	console.log('TESt'+ markas);
	//$("#txt_markas").val(markas).change();
	//$('#txt_markas').val(markas);
	//$("#txt_markas").prop("selectedIndex", 3); 
	/*$("#txt_markas option").prop('selected', false).filter(function() {
		return $(this).val() == markas;  
	}).prop('selected', true);*/ 
	$('#txt_markas').val(markas);
    $('#txt_markas').trigger("chosen:updated");
	
}

function progress(){
    counterProcess++;
	prosentasi=Number(counterProcess)/Number(totalProcess) * 100;
	$('#Process').text("Process "+format("#,##0.##",(prosentasi)) + " %");
	if(counterProcess>=totalProcess){
		$('#ProcessDetail').hide("slow");
	}
}
	
function date_view(){
	if($("#chek").attr("checked")==true){
		$(".trhidetgl").show();
	}else{		
		$(".trhidetgl").hide();
		
	}
}	