var detailsWindow;
function showlistSJ(textid) {
   detailsWindow = window.open("produk_list_for_receiving.php?textid=" + textid, "produk_list_for_receiving", "width=1200,height=600,scrollbars=yes");
   detailsWindow.focus();
}


function get_data_barcode(no){
      var subdata='';
      var barcode='';
      var size='';
      var namasize='';
      var warna='';
      var namawarna='';
      var namaproduk='';
      var hargajual='';
      // var hargasatuan=$('#hargasatuan').val();

      $("#kode"+no).keydown(function(event){
         if(event.keyCode == 13){
               try{
               var barcode=$("#kode"+no).val();  
                  $.ajax({
                    type: 'POST',
                    url: 'fob_receiving_input_data.php',
                    data: {j:'get_barcode',b:barcode},
                    dataType: 'text',
                    success: function(data){
                      //alert(data);
                                    
                      subdata=data.split(';');
                      barcode=subdata[0];
                      namaproduk=subdata[1];
                      size=subdata[2];
                      namasize=subdata[3];
                      warna=subdata[4];
                      namawarna=subdata[5];
                      hargajual=subdata[6];

                      if(barcode==''){
                        alert('Barcode tidak ditemukan');
                      } else {
                         $("#barcode"+no).val(barcode);
                         $("#nama"+no).val(namaproduk);
                         $("#size"+no).val(size);
                         $("#namasize"+no).val(namasize);
                         $("#warna"+no).val(warna);
                         $("#namawarna"+no).val(namawarna); 
                         $("#hargajual"+no).val(hargajual); 
                         // $("#qty"+no).val('1');    
                      }

                      $("#qty"+no).focus();
                      // var i=Number(no)+1;
                      // $("#kode"+i).focus();

                      hitung_total('txt_qty');
                      hitung_total_qty('txt_subtotal');

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                     // $('#txt_cari').attr('disabled',false);
                     // $('#btn_cari2').attr('disabled',false);
                     alert('Error get kode '+ no);     
                    }
                  }); 
                   
                }catch(err){
                  alert(err.message);
                }   
          }
      });  

   }

   function hitung_total(kelas){
      // alert(kelas);
       var nilai_total=0;
         $('.'+kelas).each(function(key,element){
             // alert(this.id);
            nilai_total+=Number($('#'+this.id).val());
         });
         // alert(nilai_total);
         // alert('#total_'+kelas);	
      $('#total_'+kelas).html(nilai_total);
   }
   
   function hitung_total_qty(kelas){
      // alert(kelas);
       var nilai_total=0;
         $('.'+kelas).each(function(key,element){
             // alert(this.id);
            nilai_total+=Number($('#'+this.id).val());
         });
         // alert(nilai_total);
         // alert('#total_'+kelas);	
      $('#total_'+kelas).html(nilai_total);
   }
   

   function get_subtotal(no){
     var hargajual=$("#hargajual"+no).val();
     var qty=$("#qty"+no).val();
     var subtotal=$("#subtotal"+no).val();

     var subdata=0;
     $('#hargajual'+no).keyup(function(e) {
        var e = window.event || e;
        var keyUnicode = e.charCode || e.keyCode;
        if (e !== undefined) {
           subtotal=Number(hargajual)*Number(qty);
           $("#subtotal"+no).val(subtotal);
           hitung_total('txt_qty');
           hitung_total_qty('txt_subtotal');
        }   
                                         
    });

    $('#qty'+no).keyup(function(e) {
        var e = window.event || e;
        var keyUnicode = e.charCode || e.keyCode;
        if (e !== undefined) {
           subtotal=Number(hargajual)*Number(qty);
           $("#subtotal"+no).val(subtotal);
           hitung_total('txt_subtotal');
           hitung_total_qty('txt_subtotal');

        }   
                                         
    }); 


}

  function simpan(){
  // alert('Klik simpan cukup sekali.. Mohon tunggu sebentar');
   var supplier = $("#supplier").val();
   var tgldatang = $("#tgldatang").val();
   var sj = $("#sj").val();
   var ket = $("#ket").val();
   var hargasatuan = $("#hargasatuan").val();

   if (supplier == '') {
      alert('Silahkan Input Supplier');
      return false;
   }
   if (tgldatang == '') {
      alert('Silahkan Input Tanggal Kedatangan');
      return false;
   }
   if (sj == '') {
      alert('Silahkan Input No. Surat Jalan');
      return false;
   }
   if (ket == '') {
      alert('Silahkan Input Deskripsi');
      return false;
   }

   if (hargasatuan == '') {
      alert('Silahkan Input NO CO Mapping');
      return false;
   }

   if (hargasatuan.length != 7) {
      alert('Cek Ulang Kembali NO CO Mapping');
      exit();
   }

    $.ajax({
      type : 'POST',
      url: 'fob_receiving_input_data.php',
      data : $('#f1').serialize()+'&j=simpan_receiving',
      dataType: 'text',
      success: function(data){
         
         alert(data);
         if (data.trim()=='Berhasil Di Input'){
            window.location = 'fob_receiving.php';
         }
      
      }
      
   }); 
  
}

function approve2(id_suratjalan){

   $.ajax({
       url: "fob_receiving_input_data.php",
       type: "POST",
       cache: false,
       dataType: 'text',
       data: {
           j: 'approve2',
           sj: id_suratjalan
       },
       success: function (data) {
           alert(data);
           if (data.trim() == 'Berhasil !') {
            window.location = 'fob_receiving.php';
           }

       } // END SUCSESS
   }); // end ajax 

}


$(document).ready(function () {
	$(".detail_baris").hide();
});

$(function () {
   $("#tampildetail").click(function () {
       if ($(this).is(":checked")) {
           $(".detail_baris").show();
       } else {
           $(".detail_baris").hide();
       }
   });
});


$(document).ready(function(){
 		
   activateAutoCompleteAll();

});	

function activateAutoCompleteAll(){

activateAutoComplete($('#pabrik'));		

} v

// function deactivateAutoComplete(component){
//    component.chosen("destroy");	
// }

function activateAutoComplete(component){
component.chosen({});	


}	
