<?php
session_start();
@$username = $_SESSION["username"];
if (empty($username)) {
  die('You can\'t see this page');
}

require_once("config.php");
include_once('DateControl.php');
$dc = new DateControl();

@$barcode = $_POST['b'];
@$jenis = $_POST['j'];

@$supplier = $_POST['supplier'];
@$supplier2 = $_POST['supplier2'];
@$id_supplier = $_POST['id_supplier'];
@$tglinv = $_POST['tglinv'];
@$duedate = $_POST['duedate'];
@$inv = $_POST['inv'];
@$fkp = $_POST['fkp'];
@$ket = $_POST['ket'];
@$counter = $_POST['counter'];
$datetime = date('Y-m-d h:i:s');





$isJson = 1;

if ($jenis == 'get_barcode') {
  // $sql="SELECT DATE_FORMAT(b.periode,'%Y-%m') AS periode,b.id,SUM(b.nilai) AS nilai FROM customer_affiliate_bonus b INNER JOIN 
  //     customer_affiliate  AS a  ON b.id=a.id   
  //     WHERE b.periode BETWEEN '$filter_tgl1' AND '$filter_tgl2'  
  //     AND a.updateby='apache_cli/rmall' $filter_area GROUP BY b.id,DATE_FORMAT(b.periode,'%Y-%m')";

  $sql = "SELECT
          id_suratjalan
          ,keterangan
          ,(subtotal/qty)
          ,qty
          ,subtotal
          ,total_bayar
          ,ppn
          FROM
          fob_receiving
          where id_suratjalan='$barcode'";

  $query = mysql_query($sql);
  list($kode, $deskripsi, $harga, $qty, $jumlah, $sudahbayar,$tot_ppn) = mysql_fetch_array($query);

  echo $kode . ";" . $deskripsi . ";" . $harga . ";" . $qty . ";" . $jumlah  . ";" . $sudahbayar . ";" . $tot_ppn;

  die();
} else if ($jenis == 'simpan_invoice') {

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  /*entry to fob_receiving*/
  $sql = "INSERT INTO fob_invoice (id_invoice,id_supplier,tanggal,no_faktur_pajak,total_qty,total_jumlah,total_ppn,total_harus_bayar,total_sudah_bayar,total_sisa_bayar,updatedate,updateby,`status`,tgl_harus_bayar)

            VALUES ('$inv','$supplier','$tglinv','$fkp',0,0,0,0,0,0,'$datetime','$username',1,'$duedate');";

  $query = mysql_query($sql) or die('Error Query, No Invoice Sudah di Input atau Tidak Boleh Sama');


  $c = "COMMIT;";
  $qc = mysql_query($c);

  echo "Berhasil di Input";

  die();
} else if ($jenis == 'simpan_invoice_detail') {
  /*Get nama supplier*/
  $sup = "SELECT nama from pabrik where id='$id_supplier'";
  $qsup = mysql_query($sup) or die('error query get nama supplier');
  list($nama_supplier) = mysql_fetch_array($qsup);

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  if ($qsup) {
    //echo"sukses eksekusi ".$sql."<br>";

    for ($i = 0; $i <= $counter; $i++) {
      $kode = $_POST['kode' . $i];
      $deskripsi = $_POST['deskripsi' . $i];
      $qty = $_POST['qty' . $i];
      $jumlah = $_POST['jumlah' . $i];
      $ppn = $_POST['ppn' . $i];
      $tot_harga = $_POST['tot_harga' . $i];
      $diskon = $_POST['diskon' . $i];
      $diskon_nilai = $_POST['diskon_nilai' . $i];
      $tot_harusbayar = $_POST['tot_harusbayar' . $i];
      $tot_sudahbayar = $_POST['tot_sudahbayar' . $i];
      $tot_sisabayar = $_POST['tot_sisabayar' . $i];
      // $tot_totalsudah = $tot_sudahbayar+$tot_dibayar;

      $sql2 = "INSERT INTO fob_receiving 
                            (id_suratjalan,
                            id_supplier,
                            nama_supplier,
                            keterangan,
                            qty,
                            subtotal,
                            ppn,
                            total_harga,
                            id_invoice,
                            faktur_pajak,
                            tgl_jatuhtempo,
                            tgl_bayar,
                            disc_persen,
                            disc_nilai,
                            total_harus_bayar,
                            total_bayar,
                            sisa_bayar,
                            update_date,
                            updateby)
                   VALUES ('$kode',
                            '$id_supplier',
                            '$nama_supplier',
                            '$deskripsi',
                            '$qty',
                            '$jumlah',
                            '$ppn',
                            '$tot_harga',
                            '$inv',
                            '$fkp',
                            '$duedate',
                            '$datetime',
                            '$diskon',
                            '$diskon_nilai',
                            '$tot_harusbayar',
                            '$tot_bayar',
                            '$tot_sisabayar',
                            '$datetime',
                            '$username')
                  ON DUPLICATE KEY UPDATE
                              ppn = '$ppn',
                              total_harga = '$tot_harga',
                              id_invoice = '$inv',
                              faktur_pajak = '$fkp',
                              tgl_jatuhtempo = '$duedate',
                              tgl_bayar = '$datetime',
                              disc_persen = '$diskon',
                              disc_nilai = '$diskon_nilai',
                              total_harus_bayar = '$tot_harusbayar',
                              total_bayar = '$tot_bayar',
                              sisa_bayar = '$tot_sisabayar',
                              update_date = '$datetime',
                              updateby = '$username';";

      // $query2=mysql_query($sql2) or die ($sql2);
      $query2 = mysql_query($sql2) or die($sql2);
      //echo"sukses eksekusi detail ".$sql2."<br>";        

    }

    $c = "COMMIT;";
    $qc = mysql_query($c);

    $sup2 = "SELECT     
            SUM(qty)
          , SUM(ppn)
          , SUM(subtotal)
          , SUM(total_harga)
          , SUM(disc_nilai)
          , SUM(total_harus_bayar)
          , SUM(total_bayar)
          , SUM(sisa_bayar)
          FROM
          fob_receiving
          WHERE id_invoice='$inv'";
    $qsup2 = mysql_query($sup2) or die('error query get nama supplier');
    list($total_qty,
          $total_ppn,
          $subtotal,
          $total_harga,
          $disc_nilai,
          $total_harus_bayar,
          $total_sudah, 
          $total_sisa
    ) = mysql_fetch_array($qsup2);

    $q = "SET autocommit = 0;";
    $qq = mysql_query($q);
  
    $q2 = "START TRANSACTION;";
    $qq2 = mysql_query($q);

    $up = "UPDATE fob_invoice SET 
                  total_qty=$total_qty, 
                  total_ppn=$total_ppn, 
                  total_jumlah=$subtotal,
                  total_harga=$total_harga,
                  disc_nilai=$disc_nilai, 
                  total_harus_bayar=$total_harus_bayar, 
                  total_sudah_bayar=$total_sudah,  
                  total_sisa_bayar=$total_sisa
                  where id_invoice='$inv'";

    $qup = mysql_query($up) or die($up);
  }

  $sql5 = "SELECT total_sisa_bayar
    FROM fob_invoice
    WHERE id_invoice='$inv'";
    $query4 = mysql_query($sql5) or die($sql5);
    while (list($total_sisa_bayar) = mysql_fetch_array($query4)) {

      if ($total_sisa_bayar == 0) {

        $up = "UPDATE fob_invoice SET 
        status_pembayaran=1
        where id_invoice='$inv'";

        $qup = mysql_query($up) or die($up);
      } else {
        $up = "UPDATE fob_invoice SET 
        status_pembayaran=0
        where id_invoice='$inv'";

        $qup = mysql_query($up) or die($up);
      }
    }

  $c = "COMMIT;";
  $qc = mysql_query($c);

  echo "Berhasil di Input";

  die();
} else { //get Total Jual

  die('tidak ada jenis [' . $jenis . ']');
}




if ($isJson == 1) {
  if ($username == 'iwan-it') {
    $sql_debug = mysql_escape_string($sql);
    $sql_insert = "INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('" . basename(__FILE__) . " -j $jenis', '$sql_debug');";
    mysql_query($sql_insert);
  }


  // $res = mysql_query($sql) or die($sql . ' # ' . mysql_error());
  // $result = array();
  // while ($row = mysql_fetch_object($res)) {
  //   array_push($result, $row);
  // }

  // echo json_encode($result);
}
