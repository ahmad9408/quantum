<?php
session_start();
@$username = $_SESSION["username"];
if (empty($username)) {
  die('You can\'t see this page');
}

require_once("config.php");
include_once('DateControl.php');
$dc = new DateControl();


$jenis = $_POST['j'];
$invoice_entry = $_POST['inv'];
$id_pembayaran = $_POST['ib'];
$supplier = $_POST['s'];
$tgl_bayar = $_POST['tb'];
$id_bank = $_POST['iba'];
$no_rek = $_POST['nk'];
$nominal = $_POST['nom'];
$id_suratjalan = $_POST['b'];
$subtotal_update = $_POST['q'];
$nilai = $_POST['n'];
$id_sj = $_POST['sj'];
@$kode_bayar = $_POST['id_pembayaran'];

unset($_SESSION['kode_pembayaran']);
$_SESSION['kode_pembayaran'] = $id_pembayaran;

$tgl_skg = date('Y-m-d');
$datenow = date('Y-m-d h:i:s');

//------SIMPAN INVOICE---------------------------------------------------------------------------------------------
if ($jenis == 'simpan_invoice') {

  if ($invoice_entry == '') {

    die('Silahkan Input No Invoice');
  } else {

    $sql = "SELECT id_invoice, tgl_jatuhtempo, id_suratjalan, tgl_datang, subtotal, ppn, total_harus_bayar, total_bayar, sisa_bayar FROM fob_receiving where id_invoice='$invoice_entry'";
    $query = mysql_query($sql) or die($sql);
    while (list($id_invoice, $tgl_inv, $id_suratjalan, $tgl_sj, $subtotal, $ppn, $total_harus_bayar, $bayar_sebelumnya, $sisa_bayar_sebelumnya) = mysql_fetch_array($query)) {

      $i++;
      $nilai_bayar = $total_harus_bayar;
      $nilai_bayar2 = $total_harus_bayar - $bayar_sebelumnya;

      $q = "SET autocommit = 0;";
      $qq = mysql_query($q);

      $q2 = "START TRANSACTION;";
      $qq2 = mysql_query($q);

      /*entry to fob_receiving*/
      $sql2 = "INSERT INTO fob_pembayaran_detail (id_pembayaran,id_invoice,tgl_inv,id_suratjalan,tgl_sj,seq,tgl_bayar,nilai,ppn,subtotal,bayar_sebelumnya,sisa_bayar,updatedate,updateby,status)

                VALUES ('$id_pembayaran','$id_invoice','$tgl_inv','$id_suratjalan','$tgl_sj','$i','$tgl_bayar','$nilai_bayar','$ppn','0','$bayar_sebelumnya','$nilai_bayar2','$datenow','$username','0');";


      $query2 = mysql_query($sql2) or die('Error Query, No Invoice Sudah di Input atau Tidak Boleh Sama');
    }

    $c = "COMMIT;";
    $qc = mysql_query($c);


    $s2 = "SELECT * FROM fob_pembayaran WHERE id_pembayaran = '$id_pembayaran' AND updateby = '$username'";
    $qs2 = mysql_query($s2);

    $t = "SELECT sum(nilai),sum(ppn),sum(subtotal),sum(sisa_bayar)  from fob_pembayaran_detail where id_pembayaran='$id_pembayaran'";
    $qt = mysql_query($t);
    list($tot_nilai, $tot_ppn, $tot_subtotal, $tot_sisabayar) = mysql_fetch_array($qt);



    if (mysql_num_rows($qs2) == 0) {

      $q = "SET autocommit = 0;";
      $qq = mysql_query($q);

      $q2 = "START TRANSACTION;";
      $qq2 = mysql_query($q);

      $sql = "INSERT INTO fob_pembayaran (id_pembayaran,id_supplier,tgl_bayar,id_bank,rekening,total_harus_bayar,ppn,nominal,total_sisa_bayar,updatedate,updateby) 
              values('$_SESSION[kode_pembayaran]','$supplier','$tgl_bayar','$id_bank','$no_rek','$tot_nilai','$tot_ppn','$tot_subtotal','$tot_sisabayar','$datenow','$username')";
    } else {
      $sql = "UPDATE fob_pembayaran SET tgl_bayar='$tgl_bayar', total_harus_bayar='$tot_nilai', ppn='$tot_ppn',nominal='$tot_subtotal', total_sisa_bayar='$tot_sisabayar', updatedate='$datenow', updateby ='$username' 
                  WHERE id_pembayaran='$_SESSION[kode_pembayaran]' AND updateby ='$username'";
    }

    $query3 = mysql_query($sql);

    $c = "COMMIT;";
    $qc = mysql_query($c);


    if ($query) {
      echo "sukses";
    } else {
      echo $sql;
    }
  }

//------EDIT INVOICE---------------------------------------------------------------------------------------------
} else if ($jenis == 'tampil_edit') {

  $sisabayar = $nilai - $subtotal_update;

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  $sql2 = "UPDATE fob_pembayaran_detail set subtotal='$subtotal_update', sisa_bayar='$sisabayar'
  WHERE id_suratjalan='$id_suratjalan' AND id_pembayaran='$id_pembayaran'";
  $query2 = mysql_query($sql2);

  $c = "COMMIT;";
  $qc = mysql_query($c);


  $tp = "SELECT id_pembayaran FROM fob_pembayaran WHERE id_pembayaran ='$id_pembayaran'";
  $qtp = mysql_query($tp);
  list($temp_trans) = mysql_fetch_array($qtp);

  if ($temp_trans != '') {

    $t = "SELECT sum(nilai),sum(ppn),sum(subtotal),sum(sisa_bayar)  from fob_pembayaran_detail where id_pembayaran='$temp_trans'";
    $qt = mysql_query($t);
    list($tot_nilai, $tot_ppn, $tot_subtotal, $tot_sisabayar) = mysql_fetch_array($qt);

    $q = "SET autocommit = 0;";
    $qq = mysql_query($q);

    $q2 = "START TRANSACTION;";
    $qq2 = mysql_query($q);

    $update = "UPDATE fob_pembayaran SET nominal='$tot_subtotal', total_sisa_bayar='$tot_sisabayar' 
    WHERE id_pembayaran='$temp_trans' AND updateby ='$username'";
    $q_update = mysql_query($update);

    $c = "COMMIT;";
    $qc = mysql_query($c);
  }

  if ($q_update) {
    echo "sukses";
  } else {
    echo "gagal<br>" . $sql2;
  }

//------HAPUS ITEM INVOICE---------------------------------------------------------------------------------------------
} else if ($jenis == 'hapus_item') {

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  $sql = "DELETE FROM fob_pembayaran_detail where id_pembayaran='$id_pembayaran' and id_suratjalan='$id_suratjalan'";
  $query = mysql_query($sql);

  $c = "COMMIT;";
  $qc = mysql_query($c);

  $tp = "SELECT id_pembayaran FROM fob_pembayaran WHERE id_pembayaran ='$id_pembayaran'";
  $qtp = mysql_query($tp);
  list($temp_trans) = mysql_fetch_array($qtp);

  if ($temp_trans != '') {

    $t = "SELECT sum(nilai),sum(ppn),sum(subtotal),sum(sisa_bayar)  from fob_pembayaran_detail where id_pembayaran='$temp_trans'";
    $qt = mysql_query($t);
    list($tot_nilai, $tot_ppn, $tot_subtotal, $tot_sisabayar) = mysql_fetch_array($qt);

    $q = "SET autocommit = 0;";
    $qq = mysql_query($q);

    $q2 = "START TRANSACTION;";
    $qq2 = mysql_query($q);

    $update = "UPDATE fob_pembayaran SET total_harus_bayar='$tot_nilai', ppn='$tot_ppn', nominal='$tot_subtotal', total_sisa_bayar='$tot_sisabayar' 
    WHERE id_pembayaran='$temp_trans' AND updateby ='$username'";
    $q_update = mysql_query($update);

    $c = "COMMIT;";
    $qc = mysql_query($c);
  }

  if ($q_update) {
    echo "sukses";
  } else {
    echo "gagal<br>" . $sql;
  }

//------SIMPAN PEMBAYARAN---------------------------------------------------------------------------------------------
} else if ($jenis == 'simpan_pembayaran') {

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  $sql_update = "UPDATE fob_pembayaran_detail SET STATUS=1 WHERE id_pembayaran='$kode_bayar'";
  $query = mysql_query($sql_update);

  $sqlupdate = "UPDATE fob_pembayaran SET STATUS=1 WHERE id_pembayaran='$kode_bayar'";
  $query = mysql_query($sqlupdate);

  $c = "COMMIT;";
  $qc = mysql_query($c);

  $sql = "SELECT id_pembayaran, id_invoice FROM fob_pembayaran_detail WHERE id_pembayaran='$kode_bayar' AND STATUS=1 GROUP BY id_suratjalan";
  $query = mysql_query($sql);
  while (list($id_pembayaran1, $id_invoice1) = mysql_fetch_array($query)) {

    $sql2 = "SELECT id_invoice, id_suratjalan, tgl_bayar, nilai, SUM(subtotal) AS bayar, nilai - SUM(subtotal) AS sisa_bayar, updateby, updatedate
          FROM fob_pembayaran_detail WHERE id_invoice ='$id_invoice1' AND  STATUS=1 GROUP BY id_suratjalan";
    $query2 = mysql_query($sql2);
    while (list($id_invoice2, $id_suratjalan, $tgl_bayar, $nilai, $bayar, $sisa_bayar, $updateby, $updatedate) = mysql_fetch_array($query2)) {

      $i++;

      $q = "SET autocommit = 0;";
      $qq = mysql_query($q);

      $q2 = "START TRANSACTION;";
      $qq2 = mysql_query($q);

      /*entry to fob_receiving*/
      $sql3 = "INSERT INTO fob_receiving (id_suratjalan,tgl_bayar,total_bayar,sisa_bayar,approve_date,approve_by)
      VALUES ('$id_suratjalan','$tgl_bayar','$bayar','$sisa_bayar','$datenow','$datenow')
      ON DUPLICATE KEY UPDATE tgl_bayar='$tgl_bayar', total_bayar= '$bayar', sisa_bayar='$sisa_bayar'";

      $query3 = mysql_query($sql3);
    }

    $sql4 = "SELECT SUM(qty), SUM(ppn), SUM(subtotal), SUM(total_harga), SUM(total_harus_bayar), SUM(total_bayar), SUM(sisa_bayar)
    FROM fob_receiving
    WHERE id_invoice='$id_invoice1'";
    $query3 = mysql_query($sql4);
    while (list($total_qty, $total_ppn, $jumlah, $total_harga,$total_harus, $total_sudah, $total_sisa) = mysql_fetch_array($query3)) {

      $up = "UPDATE fob_invoice SET 
      total_qty=$total_qty, 
      total_ppn=$total_ppn, 
      total_jumlah=$jumlah,
      total_harga=$total_harga,
      total_harus_bayar=$total_harus, 
      total_sudah_bayar=$total_sudah,  
      total_sisa_bayar=$total_sisa
      where id_invoice='$id_invoice1'";

      $qup = mysql_query($up);
    }

    $sql5 = "SELECT total_sisa_bayar
    FROM fob_invoice
    WHERE id_invoice='$id_invoice1'";
    $query4 = mysql_query($sql5);
    while (list($total_sisa_bayar) = mysql_fetch_array($query4)) {

      if ($total_sisa_bayar == 0) {

        $up = "UPDATE fob_invoice SET 
        status_pembayaran=1
        where id_invoice='$id_invoice1'";

        $qup = mysql_query($up);
      } else {
        $up = "UPDATE fob_invoice SET 
        status_pembayaran=0
        where id_invoice='$id_invoice1'";

        $qup = mysql_query($up);
      }
    }
  }

  $c = "COMMIT;";
  $qc = mysql_query($c);


  if ($query) {
    echo "Pembayaran Berhasil di Simpan";
  } else {
    echo "gagal<br>" . $sql;
  }
}
