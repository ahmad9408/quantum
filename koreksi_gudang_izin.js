var noInput=0;
var lastIdInsert='';
var myOptions = [{ text: 'All', value: '+-'}, {text : 'Naik', value: '+'}, {text : 'Turun', value: '-'}];
//efect blink dengan timer
   var count = 0;
   var timer = $.timer(
	function(){
		count++;
		// style="background-color:#FF0000;background-color:#FFFFFF"
		if(count % 2 ==0){
		   $('.blink').css('background-color','#FF0000');
		}else{
		   $('.blink').css('background-color','#FFFFFF');
		}
		
	},1000,true);


$(document).ready(function(){
  // var jquery4=$.noConflict(true);
   try{   
   timer.play(true);
   $('.kettgl').hide();
   $('#add').click(function(){
	   //alert('ok');
	   try{
		   addRow();
		}catch(e){
		    alert(e.message);	
		}
      
   });
   
   }catch(e){
     alert(e.message);
   }
});


function batal(kdPinjam,no,tgl,id_gudang){
	var ans=confirm('Anda yakin akan membatalkan');
	if(ans==true){
	   try{
		  // alert(elm.id + "Value " + value);
		   $.ajax({
			url:"koreksi_gudang_data.php",
			type:"POST",
			cache: false,
			dataType:'text',
			data:{kd:kdPinjam ,j:'biz',id:no,t:tgl,g:id_gudang},// target distribusi insert
			success: function(data) {
				//alert(data);
				var d=data.split('#');
				//$('#data_' + d[1]).hide();
				$('#data_' + d[1]).remove();
				//return 0;
								
			}				
		}); 
		   
	   }catch(e){
		   alert(e.message);
	   }
	
	}

}

function daysInMonth(month,year) {
   var dd = new Date(year, month, 0);
   return dd.getDate();
} 

function cari(){
	var tgl1='';
	var tgl2='';
	var nama=''
	$.ajax({
			url:"peminjaman_data.php",
			type:"POST",
			cache: false,
			dataType:'text',
			data:{t1:tgl1,t2:tgl2,b:nama,j:'cd'},
			success: function(data) {
				
			}				
		});
				

}
	
function setTgl(){
   var tahun=$('#tahun1').val();
   var bulan=$('#bulan1').val();
   
   var jmlhHari=daysInMonth(Number(bulan),Number(tahun));
   $('#tgl1').val(tahun +'-' + bulan + '-01');
   $('#tgl2').val(tahun +'-' + bulan + '-' + jmlhHari);
   
  // alert(jmlhHari + ' untuk Bulan dan tahun ' + tahun + ' ' + bulan);
}


function dateBetween( date1, date2 ) {
  //Get 1 day in milliseconds
  var one_day=1000*60*60*24;

  // Convert both dates to milliseconds
  var date1_ms = date1.getTime();
  var date2_ms = date2.getTime();

  // Calculate the difference in milliseconds
  var difference_ms = date2_ms - date1_ms;
    
  // Convert back to days and return
  return Math.round(difference_ms/one_day); 
}

function update(no){
 // alert(nilai);
   var keterangan='';
   var gudang='';
   var tgl='';
   var pilihan='';
   
  try{
	  // $('#debug').append("tr 1 </br>" );
	    keterangan=$('#kt_' + no).val();
	   gudang=$('#gd_' + no).val();
	   pilihan=$('#p_'+ no).val();
	   //alert(pilihan);
	    //$('#debug').append(gudang +" </br>" );
	   if(gudang.length == 0){
		 alert('isi Nama Gudangnya ');
		 return;	  
	  }
	  if(gudang.indexOf("]") == -1){
		 alert('gudang tidak ditemukan');
		 return;
	  }
	  
	 
	  var ans=confirm('Simpan izin koreksi  ?');
	 
	  if(ans==true){
		  //alert("update " + nilai);
		  
		  
		  tgl=$('#tglp_' + no).val();
		  
		  
		 // alert('tgl' + tgl + ' Peminjam ' + peminjam  + ' ' + barang);
		  //return;
		 
		  $.ajax({
				url:"koreksi_gudang_data.php",
				type:"POST",
				cache: false,
				dataType:'text',
				data:{j:'ip',id:no,t1:tgl,k:keterangan,g:gudang,pl:pilihan},
				success: function(data) {	
					$('#debug').append( data + "<br>");
					console.log(data);
					var rsl=data.split('#');
					if(rsl[0]=='ok'){
					 // alert(rsl[1]);
					   $('#kt_' + rsl[1]).attr('readonly',true);
					   $('#gd_' + rsl[1]).attr('readonly',true);
					   $('#tglp_'+ rsl[1]).attr('readonly',true);
					   $('#kt_' + rsl[1]).addClass('flat');
					   $('#gd_' + rsl[1]).addClass('flat');
					   $('#tglp_' + rsl[1]).addClass('flat');
					   jquery4('#tglp_' + rsl[1]).datepicker("destroy");
					   $('#app_'+ rsl[1]).text(username);
					   //alert(username);
					  
					   $('#btnUpdate_' + rsl[1]).remove();
					   $('#btnCancel_' + rsl[1]).remove();
					   
					   //var btnKembali='<input type="button" value="Kembali" onclick="kembali(\''+ rsl[2] +'\',\''+ rsl[1] +'\',\''+ rsl[3] +'\')" id="btnkembali_'+ rsl[2] +'"/>';
					   //$('#kb_' + rsl[1]).append(btnKembali);
					   
									   
					}else{
					   alert("gagal simpan data \n" + rsl[1]);
					}	
					
								
				}				
			});  
	  
	  }
  }catch(e){
	  alert('Error ' + e.message);  
  }//end try
 

}







   
   
   function removeRow(id){
     $('#' + id).remove();
	 noInput--;
       
   }
   function removeRow2(id){
     $('#' + id).remove();
	 no--;
       
   }
   
   function addRow(){
	   //alert('test addrow');
   try{
       no++;
      var code='';
	  var dtpTgl='<input type="text" name="tglp_'+ no +'" id="tglp_'+ no +'" value="'+ today +'" size="10" class="tanggal"/>';
	  var txtKeterangan='<input name="kt_'+ no +'" id="kt_'+ no +'" size="90">';
	  var txtGudang='<input name="gd_'+ no +'" id="gd_'+ no +'" size="30" class="gudang">';
	  var lstPilihan='<select name="pilihan" id="p_'+ no +'" class="pil"></select>';
	  var btnApprove='<input type="button" value="Approved" onclick="update(\''+ no +'\')" id="btnUpdate_'+ no +'"/>';
	 //alert('Try To Add');
	 
	 //asal tbody:first).append jadi tr:first).after
	  jquery4("#myTable tr:first").after("<tr id='data_"+no+"' class='row' bgcolor='#FFFFFF'><td>"+ no +"</td><td>"+ dtpTgl 
			+"</td><td>"+ txtGudang +"</td><td>"+ lstPilihan +"</td><td>"+txtKeterangan +"</td><td id='app_"+ no +"'>"+btnApprove +"</td><td id='kb_" + no +"'>&nbsp;</td></tr>");
						
	    jquery4("#tglp_"+ no ).datepicker({
	      dateFormat      : "yy-mm-dd"
	    });
		
		$(".gudang").autocomplete("proses_complete_gudang.php",{width: 350});
		 $.each(myOptions, function(i, el) {    
		   $('#p_'+no).append( new Option(el.text,el.value) );
		});
		
	  }catch(e){
	     alert(e.message);
	  
	  }
     
   }
