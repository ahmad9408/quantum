
var detailsWindow;
function showlistSJ(textid) {
    detailsWindow = window.open("fob_invoice_list.php?textid=" + textid, "fob_invoice_list", "width=1200,height=600,scrollbars=yes");
    detailsWindow.focus();
}


$(document).ready(function () {

    $("#invoice_entry").keydown(function (event) {
        if (event.keyCode == 13) {
            var invoice_entry = $("#invoice_entry").val();
            var id_pembayaran = $("#id_pembayaran").val();
            var supplier = $("#supplier").val();
            var tgl_bayar = $("#tgl_bayar").val();
            var id_bank = $("#id_bank").val();
            var no_rek = $("#no_rek").val();
            var nominal = $("#nominal").val();

            if (tgl_bayar == '') {
                alert('Silahkan Input Tanggal Bayar');
                return false;
            }
            if (supplier == '') {
                alert('Silahkan Input Supplier');
                return false;
            }
            //alert(barcode+'-'+trans);
            $.ajax({
                url: "fob_pembayaran_input_data.php",
                type: "POST",
                cache: false,
                dataType: 'text',
                data: {
                    inv: invoice_entry,
                    j: 'simpan_invoice',
                    ib: id_pembayaran,
                    s: supplier,
                    tb: tgl_bayar,
                    iba: id_bank,
                    nk: no_rek,
                    nom: nominal
                },
                success: function (data) {
                    //alert(data);
                    if (data == 'sukses') {
                        //location.reload();
                        $("#transaksi").load("fob_pembayaran_refresh.php");
                        $("#invoice_entry").val('');
                        $("#tgl_bayar").attr('readonly', true);
                        $("#supplier").attr('readonly', true);
                        // $("#barcode").show(); 
                    } else if ((data == 'Silahkan Cek Kembali Data Yang Anda Input') || (data = 'Silahkan Cek Kembali Data Yang Anda Input') || (data = 'Silahkan Cek Kembali Data Yang Anda Input')) {
                        alert(data);
                        //$("#invoice_entry").val('');
                    }

                } // END SUCSESS
            }); // end ajax*/
        }
    });


});


function tampil_edit(id_suratjalan, kode_pembayaran, nilai, subtotal) {

//    $("#tsubtotal_" + id_suratjalan).html("<input type=text id='subtotal_" + id_suratjalan + "' value='" + subtotal + "' style='width:300px' class='form-control'>");
   $("[id='tsubtotal_"+id_suratjalan+"']").html("<input type=text id='subtotal_" + id_suratjalan + "' value='" + subtotal + "' style='width:300px' class='form-control'>");


   $("[id='subtotal_"+id_suratjalan+"']").keydown(function (event) {
       if (event.keyCode == 13) {
           var subtotal_update = $("[id='subtotal_"+id_suratjalan+"']").val();
           var tgl_bayar = $("#tgl_bayar").val();
           

           if (tgl_bayar == '') {
               alert('Silahkan Input Tanggal Bayar');
               return false;
           }
           // alert(qty_update);
           $.ajax({
               url: "fob_pembayaran_input_data.php",
               type: "POST",
               cache: false,
               dataType: 'text',
               data: {
                   b: id_suratjalan,
                   j: 'tampil_edit',
                   q: subtotal_update,
                   n: nilai,
                   ib: kode_pembayaran

               },
               success: function (data) {
                   // alert(data);
                   if (data == 'sukses') {
                       $("#transaksi").load("fob_pembayaran_refresh.php");
                   } else if (data = 'Gagal Transaksi') {
                       alert(data);
                   }

               } // END SUCSESS
           }); // end ajax 
       } // end event
   }); // end keydown function */

}

function hapus(id_suratjalan, id_pembayaran) {
    var tgl_bayar = $("#tgl_bayar").val();
    if (tgl_bayar == '') {
        alert('Silahkan Input Tanggal Bayar');
        return false;
    }
    // alert(barcode);
    $.ajax({
        url: "fob_pembayaran_input_data.php",
        type: "POST",
        cache: false,
        dataType: 'text',
        data: {
            j: 'hapus_item',
            b: id_suratjalan,
            ib: id_pembayaran
        },
        success: function (data) {
            if (data == 'sukses') {
                $("#transaksi").load("fob_pembayaran_refresh.php");
            }

        } // END SUCSESS
    }); // end ajax 
}

function simpan() {
    // alert('Klik simpan cukup sekali.. Mohon tunggu sebentar');
    $.ajax({
       type: 'POST',
       url: 'fob_pembayaran_input_data.php',
       data: $('#f1').serialize() + '&j=simpan_pembayaran',
       dataType: 'text',
       success: function (data) {
   
 
          alert(data);
          if (data.trim() == 'Pembayaran Berhasil di Simpan') {
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