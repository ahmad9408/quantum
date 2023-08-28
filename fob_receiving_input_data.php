<?php
session_start();
@$username = $_SESSION["username"];
if (empty($username)) {
  die('You can\'t see this page');
}

require_once("config.php");
include_once('DateControl.php');
include "pdo_produksi/Db.class.php";

$dc = new DateControl();

@$barcode = $_POST['b'];
@$jenis = $_POST['j'];

@$supplier = $_POST['supplier'];
@$tgldatang = $_POST['tgldatang'];
@$hargasatuan = $_POST['hargasatuan'];
@$sj = $_POST['sj'];
@$ket = $_POST['ket'];
@$counter = $_POST['counter'];
$datetime = date('Y-m-d h:i:s');



$isJson = 1;

if ($jenis == 'get_barcode') {

  $sql = "SELECT p.kode, p.nama, p.kode_size, s.size, p.kode_warna, w.warna, p.hargajual
         FROM produk AS p INNER JOIN mst_size AS s  ON (p.kode_size = s.kode)
                          INNER JOIN mst_warna AS w ON (p.kode_warna = w.kode)
         where p.kode='$barcode'";

  $query = mysql_query($sql);
  list($kode, $nama, $kode_size, $size, $kode_warna, $warna, $hargajual) = mysql_fetch_array($query);

  echo $kode . ";" . $nama . ";" . $kode_size . ";" . $size . ";" . $kode_warna . ";" . $warna . ";" . $hargajual;

  die();
} else if ($jenis == 'simpan_receiving') {

  $co = "SELECT COUNT(*) ada FROM job_gelaran WHERE no_co_mapping='$hargasatuan'";
  $co_m = mysql_query($co) or die('NO CO Mapping Tidak Di Temukan, Mohon Periksa Kembali');
  list($ada) = mysql_fetch_array($co_m);

  if ($ada > 0) {

    $hm = "SELECT harga_makloon FROM job_gelaran WHERE no_co_mapping='$hargasatuan'";
    $h_m = mysql_query($hm) or die('Harga Makkloon Belum Di Isi');
    list($ada_harga) = mysql_fetch_array($h_m);

    if ($ada_harga != 0) {

      /*Get nama supplier*/
      $sup = "SELECT nama from pabrik where id='$supplier'";
      $qsup = mysql_query($sup) or die('error query get nama supplier');
      list($nama_supplier) = mysql_fetch_array($qsup);

      /*create no receiving otomatis*/
      $tgl = explode("-", $tgldatang);
      $thn = substr($tgl[0], 2, 2);
      $bln = $tgl[1];
      $subtgl = $tgl[2];

      $char1 = "SJ_FOB";
      $char2 = $supplier;
      //membuat doc no otomatis
      $c = "SELECT max(id_receiving) as maxKode FROM  fob_receiving WHERE substring(id_receiving,1,12)='$char1/$char2' ";
      $qc = mysql_query($c) or die('error query get counter');
      //echo $c; die ();
      list($hasil_cari) = mysql_fetch_array($qc);
      // echo "hasil cari =".$hasil_cari."<br><br>"; 
      $kode = substr($hasil_cari, 22, 3);
      // $kode=intval($kode);
      // echo $kode; die();
      $tambah = $kode + 1;
      //echo $tambah; die();
      //echo $hasil_cari." -".$kode."-".$tambah;
      if ($tambah < 10) {
        $sub_id = "00" . $tambah;
      } else if (($tambah >= 10) && ($tambah < 100)) {
        $sub_id = "0" . $tambah;
      } else if ($tambah >= 1000) {
        $sub_id = $tambah;
      }

      $kode_receiving = $char1 . "/" . $char2 . "/" . $thn . "/" . $bln . "/" . $subtgl . "/" . $sub_id;

      //echo $kode_receiving; die();
      if ($sj == '') {
        $sj_fix = $kode_receiving;
      } else {
        $sj_fix = $sj;
      }

      $group = "SELECT id_group from pabrik where id='$supplier'";
      $q_group = mysql_query($group) or die('error query get nama supplier');
      list($id_group) = mysql_fetch_array($q_group);

      $q = "SET autocommit = 0;";
      $qq = mysql_query($q);

      $q2 = "START TRANSACTION;";
      $qq2 = mysql_query($q);

      /*entry to fob_receiving*/
      $sql = "INSERT INTO fob_receiving (id_receiving,id_suratjalan,id_supplier,nama_supplier,keterangan,tgl_datang,
             qty,subtotal,ppn,total_harga,id_invoice,faktur_pajak,tgl_jatuhtempo,tgl_bayar,total_bayar,sisa_bayar,
             approve_date,approve_by,update_date,updateby,`status`,subtotal_harga_jual,co_mapping,id_group)

            VALUES ('$kode_receiving','$sj_fix','$supplier','$nama_supplier','$ket','$tgldatang',0,0,
            0,0,'','','','',0,0,'$datetime','$username','$datetime','$username',1,0,'$hargasatuan','$id_group');";

      $query = mysql_query($sql) or die('No. Surat Jalan Sama atau sudah di Input');
      // $query=mysql_query($sql) or die ($sql);   

      $c = "COMMIT;";
      $qc = mysql_query($c);


      if ($query) {
        //echo"sukses eksekusi ".$sql."<br>";

        for ($i = 0; $i <= $counter; $i++) {

          $_ppn = "SELECT ppn from pabrik where id='$supplier'";
          $qppn = mysql_query($_ppn) or die('error query get nama supplier');
          list($ppn_) = mysql_fetch_array($qppn);

          if ($ppn_ == '1') {
            $ppn = 0.11;
          } else {
            $ppn = 0;
          }

          $sup = "SELECT harga_makloon from job_gelaran where no_co_mapping='$hargasatuan'";
          $qsup = mysql_query($sup) or die('error query get harga makloon');
          list($harga_satuan) = mysql_fetch_array($qsup);

          $kode = $_POST['kode' . $i];
          $nama = $_POST['nama' . $i];
          $qty = $_POST['qty' . $i];
          $hargajual = $_POST['hargajual' . $i];
          $harga = $harga_satuan;
          $subtotal = $qty * $harga;
          $tothpj = $qty * $hargajual;
          $harga_ppn = $subtotal * $ppn;
          $subtotal_after_ppn = $subtotal + $harga_ppn;

          $q = "SET autocommit = 0;";
          $qq = mysql_query($q);

          $q2 = "START TRANSACTION;";
          $qq2 = mysql_query($q);

          $sql2 = "INSERT INTO fob_receiving_detail (id_receiving,seq,kode_produk,nama_produk,qty,harga,ppn,subtotal,harga_jual,total_harga_jual,co_mapping)
                    VALUES ('$sj_fix','$i','$kode','$nama','$qty','$harga','$harga_ppn','$subtotal_after_ppn','$hargajual','$tothpj','$hargasatuan');";

          // $query2=mysql_query($sql2) or die ($sql2);
          $query2 = mysql_query($sql2);
          //echo"sukses eksekusi detail ".$sql2."<br>";  

          $c = "COMMIT;";
          $qc = mysql_query($c);


          $total_qty += $qty;
          $total_subtotal = $total_qty * $harga;
          $total_ppn += $harga_ppn;
          $total_harga = $total_subtotal + $total_ppn;
          $subtotal_total_harga_jual += $tothpj;
        }

        // $sup2 = "SELECT
        //         fcd.id_receiving
        //         , SUM(fcd.qty)
        //         , fcd.harga*SUM(fcd.qty)
        //         , SUM(fcd.ppn)
        //         , SUM(fcd.subtotal)
        //         , fcd.total_harga_jual

        //         FROM
        //         fob_receiving AS fc
        //         LEFT JOIN fob_receiving_detail AS fcd
        //         ON (fc.id_suratjalan = fcd.id_receiving)
        //         WHERE (fcd.id_receiving ='$sj');";
        // $qsup2 = mysql_query($sup2);
        // list(
        //   $fc_id_receiving,
        //   $total_qty,
        //   $total_harga,
        //   $total_ppn,
        //   $total_subtotal,
        //   $subtotal_total_harga_jual
        // ) = mysql_fetch_array($qsup2);

        $q = "SET autocommit = 0;";
        $qq = mysql_query($q);

        $q2 = "START TRANSACTION;";
        $qq2 = mysql_query($q);

        $up = "UPDATE fob_receiving SET qty=$total_qty,subtotal=$total_subtotal,ppn=$total_ppn,total_harga=$total_harga,subtotal_harga_jual=$subtotal_total_harga_jual where id_suratjalan='$sj'";
        $qup = mysql_query($up);

        $c = "COMMIT;";
        $qc = mysql_query($c);
      }


      echo "Berhasil Di Input";
    } else {
      echo "Harga Makloon Belum Di Input";
    }
  } else {

    echo "NO CO Mapping Tidak Ditemukan, Mohon Periksa Kembali";
  }
} else if ($jenis == 'approve2') {

  $sql = "SELECT approve2 FROM fob_receiving WHERE id_suratjalan='$sj'";
  $hsl = mysql_query($sql);
  list($approve) = mysql_fetch_array($hsl);
  if ($approve == 0) {

    $q = "SET autocommit = 0;";
    $qq = mysql_query($q);

    $q2 = "START TRANSACTION;";
    $qq2 = mysql_query($q);

    $sql = "UPDATE fob_receiving SET approve2='1', approve2_by='$username',approve2_date=NOW() WHERE id_suratjalan='$sj'";
    mysql_query($sql);

    $sql2 = "UPDATE fob_receiving_detail SET status='1' WHERE id_receiving='$sj'";
    mysql_query($sql2);

    $c = "COMMIT;";
    $qc = mysql_query($c);

    $sql = "SELECT id_suratjalan, id_supplier,tgl_datang,qty, subtotal_harga_jual, approve_by, approve_date, approve2, approve2_by, approve2_date, status FROM fob_receiving where id_suratjalan='$sj'";
    $query = mysql_query($sql) or die($sql);
    list($id_suratjalan, $id_supplier, $tgl_datang, $total_qty, $subtotal_harga_jual, $approve_by, $approve_date, $approve2, $approve2_by, $approve2_date, $status) = mysql_fetch_array($query);

    $q = "SET autocommit = 0;";
    $qq = mysql_query($q);

    $q2 = "START TRANSACTION;";
    $qq2 = mysql_query($q);

    /*entry to fob_receiving*/
    $sql2 = "INSERT INTO do_produk_qc (
                            no_do,
                            id_pabrik,
                            tanggal,
                            total_qty,
                            total_amount,
                            approve1,
                            approve1_by,
                            approve1_date,
                            approve2,
                            approve2_by,
                            approve2_date,
                            update_date,
                            update_user,
                            status)
                VALUES ('$id_suratjalan',
                        '$id_supplier',
                        '$tgl_datang',
                        '$total_qty',
                        '$subtotal_harga_jual',
                        1,
                        '$approve_by',
                        '$approve_date',
                        '$approve2',
                        '$approve2_by',
                        '$approve2_date',
                        '$datetime',
                        '$username',
                        '$status')
                ON DUPLICATE KEY UPDATE
                            id_pabrik = '$id_supplier',
                            tanggal = '$tgl_datang',
                            total_qty = '$total_qty',
                            total_amount = '$subtotal_harga_jual',
                            approve1 = 1,
                            approve1_by = '$approve_by',
                            approve1_date = '$approve_date',
                            approve2 = '$approve2',
                            approve2_by = '$approve2_by',
                            approve2_date = '$approve2_date',
                            update_date = '$datetime',
                            update_user = '$username',
                            status = '$status';";
    $query2 = mysql_query($sql2) or die('Error Query Input Data Ke Rekap Data Stock DO Produk QC Deatil');

    $c = "COMMIT;";
    $qc = mysql_query($c);

    $sql_detail = "SELECT id_receiving, kode_produk, harga_jual, qty, total_harga_jual, seq, status, co_mapping FROM fob_receiving_detail where id_receiving='$sj'";
    $query_detail = mysql_query($sql_detail) or die($sql_detail);
    while (list($id_receiving, $kode_produk, $harga_jual, $qty, $total_harga_jual, $seq, $status_detail, $co_mapping) = mysql_fetch_array($query_detail)) {

      $q = "SET autocommit = 0;";
      $qq = mysql_query($q);

      $q2 = "START TRANSACTION;";
      $qq2 = mysql_query($q);

      /*entry to fob_receiving*/
      $sql3 = "INSERT INTO do_produk_qc_detail (
                            no_do,
                            tanggal,
                            barcode,
                            harga_jual,
                            qty,
                            disc,
                            subtotal,
                            polybag,
                            seq,
                            status,
                            co_mapping)
                VALUES ('$id_receiving',
                        '$datetime',
                        '$kode_produk',
                        '$harga_jual',
                        '$qty',
                        0,
                        '$total_harga_jual',
                        0,
                        '$seq',
                        '$status_detail',
                        '$co_mapping')
                ON DUPLICATE KEY UPDATE
                          tanggal = '$datetime',
                          barcode = '$kode_produk',
                          harga_jual = '$harga_jual',
                          qty = '$qty',
                          disc = 0,
                          subtotal = '$total_harga_jual',
                          polybag = 0,
                          seq = '$seq',
                          status = '$status_detail',
                          co_mapping = '$co_mapping';";
      $query3 = mysql_query($sql3) or die('Error Query Input Data Ke Rekap Data Stock DO Produk QC');

      $c = "COMMIT;";
      $qc = mysql_query($c);
    }
    echo "Berhasil !";
  }

  echo "Berhasil !";

  die();
} else if (isset($_POST["batalkan"])) {
  $todayTime = date('Y-m-d H:i:s');
  $no_do = $_POST['no_do'];

  $q = "SET autocommit = 0;";
  $qq = mysql_query($q);

  $q2 = "START TRANSACTION;";
  $qq2 = mysql_query($q);

  $sql = "UPDATE fob_receiving SET id_suratjalan='btl.$no_do', id_receiving='btl.$no_do', STATUS='0' WHERE id_suratjalan='$no_do'";
  mysql_query($sql);

  $no_do = $_POST['no_do'];
  $sql2 = "UPDATE fob_receiving_detail SET id_receiving='btl.$no_do', STATUS='0' WHERE id_receiving='$no_do'";
  mysql_query($sql2);

  $c = "COMMIT;";
  $qc = mysql_query($c);




  echo "
  <script>
      alert ('Berhasil !');
      window.location = 'fob_receiving.php';
  </script>" . $sql . "<br>" . $sql2;
} else { //get Total Jual

  echo "
  <script>
      alert('Gagal!!!!!');
      window.location = 'fob_receiving.php';
  </script>";
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
