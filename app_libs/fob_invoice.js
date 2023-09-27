var detailsWindow;
function showlistSJ(textid) {
   detailsWindow = window.open("fob_suratjalan_list.php?textid=" + textid, "fob_suratjalan_list", "width=1200,height=600,scrollbars=yes");
   detailsWindow.focus();
}


function get_data_barcode(no) {
   var subdata = '';
   var barcode = '';
   var deskripsi = '';
   var harga = '';
   var qty = '';
   var jumlah = '';
   var sudahbayar = '';
   var tot_ppn = '';

   // var hargasatuan=$('#hargasatuan').val();

   $("#kode" + no).keypress(function (event) {
      if (event.keyCode == 13) {
         try {
            var barcode = $("#kode" + no).val();
            $.ajax({
               type: 'POST',
               url: 'fob_invoice_input_data.php',
               data: { j: 'get_barcode', b: barcode },
               dataType: 'text',
               success: function (data) {
                  //alert(data);

                  subdata = data.split(';');
                  barcode = subdata[0];
                  deskripsi = subdata[1];
                  harga = subdata[2];
                  qty = subdata[3];
                  jumlah = subdata[4];
                  sudahbayar = subdata[5];
                  tot_ppn = subdata[6];
                  var harga2 = Number(jumlah) / Number(qty);
                  var tot_harga = Number(tot_ppn) + Number(jumlah);
                  var akan_bayar = 0;

                  if (barcode == '') {
                     alert('Surat Jalan Tidak Ditemukan');
                  } else {
                     $("#barcode" + no).val(barcode);
                     $("#deskripsi" + no).val(deskripsi);
                     $('#harga' + no).val(harga2.toFixed(2));
                     $("#qty" + no).val(qty);
                     $("#jumlah" + no).val(jumlah);
                     $("#ppn" + no).val(tot_ppn);
                     $("#tot_harga" + no).val(tot_harga.toFixed(2));
                     $("#tot_sudahbayar" + no).val(sudahbayar);
                     // $("#tot_dibayar" + no).val(akan_bayar.toFixed(2));
                     // $("#tot_sisabayar" + no).val(tot_harga.toFixed(2));

    
                  }

                  $("#diskon" + no).focus();
                  // var i=Number(no)+1;
                  // $("#kode"+i).focus();

               },
               error: function (xhr, ajaxOptions, thrownError) {
                  // $('#txt_cari').attr('disabled',false);
                  // $('#btn_cari2').attr('disabled',false);
                  alert('Error get kode ' + no);
               }
            });

         } catch (err) {
            alert(err.message);
         }
      }
   });

}

function tampil_edit(faktur) {

   //    $("#tsubtotal_" + id_suratjalan).html("<input type=text id='subtotal_" + id_suratjalan + "' value='" + subtotal + "' style='width:300px' class='form-control'>");
      $("[id='fkp']").html("<input type=text id='nfkp' value='" + faktur + "' style='font-size: 8pt;width:300px;' class='form-control'>");
   
   
      $("[id='nfkp']").keydown(function (event) {
          if (event.keyCode == 13) {
              var fkp_baru = $("[id='nfkp']").val();
              var invoice = $("#inv").val();
              
              // alert(qty_update);
              $.ajax({
                  url: "fob_invoice_input_data.php",
                  type: "POST",
                  cache: false,
                  dataType: 'text',
                  data: {
                      fkp: fkp_baru,
                      j: 'tampil_edit_fkp',
                      inv: invoice
   
                  },
                  success: function (data) {
                      // alert(data);
                      if (data == 'sukses') {
                        //   $("#transaksi").load("fob_pembayaran_refresh.php");
                          window.location = 'fob_invoice_detail_input.php?no_inv='+ invoice;
                      } else if (data = 'Gagal Transaksi') {
                          alert(data);
                      }
   
                  } // END SUCSESS
              }); // end ajax 
          } // end event
      }); // end keydown function */
   
   }

function get_diskon(no) {
   var tot_harga = $("#tot_harga" + no).val();
   var diskon = $("#diskon" + no).val();
   var tot_sudahbayar = $("#tot_sudahbayar" + no).val();
   var persen = 100;
   var subdata = 0;

   $('#diskon' + no).keypress(function (e) {
      var e = window.event || e;
      var keyUnicode = e.charCode || e.keyCode;
      if (e !== undefined) {
         diskon_nilai = Number(tot_harga) * Number(diskon) / Number(persen);
         $("#diskon_nilai" + no).val(diskon_nilai.toFixed(2));
         total_harus_bayar = Number(tot_harga) - Number(diskon_nilai);
         $("#tot_harusbayar" + no).val(total_harus_bayar.toFixed(2));
         total_sisa_bayar = Number(total_harus_bayar) - Number(tot_sudahbayar);
         $("#tot_sisabayar" + no).val(total_sisa_bayar.toFixed(2));
      }

   });

}

// function get_tot_bayar(no) {
//    var jumlah = $("#jumlah" + no).val();
//    var tot_harga = $("#tot_harga" + no).val();
//    var tot_sudahbayar = $("#tot_sudahbayar" + no).val();
//    var subdata = 0;


//    $('#ppn2' + no).keypress(function (e) {
//       var e = window.event || e;
//       var keyUnicode = e.charCode || e.keyCode;
//       if (e !== undefined) {

//          tot_harga = Number(jumlah) + Number(tot_ppn);
//          $("#tot_harga" + no).val(tot_harga.toFixed(2));
//          tot_sisa = Number(tot_harga) - Number(tot_sudahbayar);
//          $("#tot_sisabayar" + no).val(tot_sisa.toFixed(2));
//       }
      
//    });

// }

// function get_sisa_bayar(no) {
//    var tot_harga = $("#tot_harga" + no).val();
//    var tot_dibayar = $("#tot_dibayar" + no).val();
//    var tot_sudahbayar = $("#tot_sudahbayar" + no).val();
//    var subdata = 0;

//    $('#tot_dibayar' + no).keydown(function (e) {
//       var e = window.event || e;
//       var keyUnicode = e.charCode || e.keyCode;
//       if (e !== undefined) {
//          tot_sisabayar = Number(tot_harga) - Number(tot_dibayar) - Number(tot_sudahbayar);
//          $("#tot_sisabayar" + no).val(tot_sisabayar.toFixed(2));
//       }

//    });

// }


function simpan() {
   // alert('Klik simpan cukup sekali.. Mohon tunggu sebentar');

   var supplier = $("#supplier").val();
   var tglinv = $("#tglinv").val();
   var duedate = $("#duedate").val();
   var inv = $("#inv").val();


   if (supplier == '') {
      alert('Silahkan Input Supplier');
      return false;
   }
   if (tglinv == '') {
      alert('Silahkan Input Tanggal Invoice');
      return false;
   }
   if (duedate == '') {
      alert('Silahkan Input Tanggal DueDate');
      return false;
   }
   if (inv == '') {
      alert('Silahkan Input No. Invoice');
      return false;
   }


   $.ajax({
      type: 'POST',
      url: 'fob_invoice_input_data.php',
      data: $('#f1').serialize() + '&j=simpan_invoice',
      dataType: 'text',
      success: function (data) {
  

         alert(data);
         if (data.trim() == 'Berhasil di Input') {
            window.location = 'fob_invoice.php';
         }

      }

   });

}

function simpan_detail() {
   // alert('Klik simpan cukup sekali.. Mohon tunggu sebentar');
   $.ajax({
      type: 'POST',
      url: 'fob_invoice_input_data.php',
      data: $('#f1').serialize() + '&j=simpan_invoice_detail',
      dataType: 'text',
      success: function (data) {

         alert(data);
         if (data.trim() == 'Berhasil di Input') {
            window.location = 'fob_invoice.php';
         }

      }

   });

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



$(document).ready(function () {

   activateAutoCompleteAll();

});

function activateAutoCompleteAll() {

   activateAutoComplete($('#pabrik'));
   activateAutoComplete($('#supplier'));

}


function activateAutoComplete(component) {
   component.chosen({});
}	
