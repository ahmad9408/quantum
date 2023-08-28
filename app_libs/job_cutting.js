$(document).ready(function () {

});

function tampil_edit(barcode, kode_transaksi, qty) {
    $("#tqty_" + barcode).html("<input type=text id='qty_" + barcode + "' size=3 value='" + qty + "'>");

    $("#qty_" + barcode).keydown(function (event) {
        if (event.keyCode == 13) {
            var qty_update = $("#qty_" + barcode).val();
            var tgl_so = $("#tgl_so").val();

            if (tgl_so == '') {
                alert('Silahkan Input Tanggal SO');
                return false;
            }
            // alert(qty_update);
            $.ajax({
                url: "update_qty_so.php",
                type: "POST",
                cache: false,
                dataType: 'text',
                data: {
                    b: barcode,
                    q: qty_update

                },
                success: function (data) {
                    // alert(data);
                    if (data == 'sukses') {
                        $("#transaksi").load("input_so_refresh.php");
                    } else if (data = 'Gagal Transaksi') {
                        alert(data);
                    }

                } // END SUCSESS
            }); // end ajax 
        } // end event
    }); // end keydown function */

}