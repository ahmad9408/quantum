<?php
ob_start();
session_start();
@$username=$_SESSION["username"];
if(empty($username)){
 die('You can\'t see this page');
}

 // menggunakan class phpExcelReader
include "excel_reader2.php";

// koneksi ke mysql
include"connect.php";

// nilai awal counter untuk jumlah data yang sukses dan yang gagal diimport
$sukses = 0;
$gagal = 0;
$datetime=date('Y-m-d h:i:s');
$id_jurnal=$_POST['nomor'];
$bank=$_POST['temp_bank'];
$jenis=$_POST['temp_jenis'];
$id_import=date('Ymdhis');
$pabrik=$_POST['pabrik'];
$tanggal_so=$_POST['tanggal_so'];
$ket=$_POST['ket'];

// $pabrik="P0001";
// $tanggal_do="2022-01-01";

$tgl=explode("-",$tanggal_so);
$thn=$tgl[0];
$bln=$tgl[1];
$subtgl=$tgl[2];


$time=date('his');


// function no_do($tanggal_do,$pabrik){

       $char1="SO_FINISH";
       $char2=$pabrik;
       //membuat doc no otomatis
       $c = "SELECT max(kode_so) as maxKode FROM  so_finishing WHERE tgl_stok_pagi='$tanggal_so%' 
       AND substring(kode_so,1,15)='$char1/$char2' ";
       $qc = mysql_query($c) or die ($c);

       //echo $c; //die ();
       
       list($hasil_cari)=mysql_fetch_array($qc);

         // echo "hasil cari =".$hasil_cari."<br><br>"; 
         $kode=substr($hasil_cari,28,4);
         // $kode=intval($kode);
         $tambah=$kode+1;


         //echo $hasil_cari." -".$kode."-".$tambah;
         if($tambah<10){
          $sub_id="00".$tambah;
         } else if (($tambah>=10) && ($tambah<100)){
          $sub_id="0".$tambah;
         } else if ($tambah>=1000){
             $sub_id=$tambah;
         } 

       //echo $hasil_cari."-".$kode."-".$tambah."-".$sub_id."<br>";

       //membuat format tahun co:/  19 dari 2019
       $subthn=substr($thn, 2,2);

       
       //echo $sub_id;
       //return 
      $docno =$char1."/".$char2."/".$thn."/".$bln."/".$subtgl."/".$sub_id;
// }

      $kode_so=$docno; 



if (!empty($_FILES['userfile']['name'])) // jika nama file tidak kosong
{
    $extensionList = array("xls"); //ini list tipe file yang akan kita perbolehkan untuk di unggah
    $fileName = $_FILES['userfile']['name']; // pengambilan nama file dari form
    $pecah = explode(".", $fileName); // proses pemecahan nama file untuk pengambilan extension file
    $belah = count($pecah);
    $ekstensi = strtolower($pecah[$belah-1]); //pengambilan extension file sekaligus strtolower untuk merubah string menjadi huruf kecil semua
    // proses untuk pengecekan file extensi, in_array maksudnya apabila data string ada di dalam array maka ...
    if (in_array($ekstensi, $extensionList))
    {
     unset($_SESSION['id_import']);
     $_SESSION['id_import']=$id_import;  
	  // membaca file excel yang diupload
     $data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);
     //echo $data."--<br>";
     // membaca jumlah baris dari data excel
     $baris = $data->rowcount($sheet_index=0);
     //echo $baris."--<br>";
     //echo"baris = $baris";

     // import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom)
           $sql="SET autocommit = 0;";
        $query=mysql_query($sql);

        $sql="START TRANSACTION;";
        $query=mysql_query($sql);
     for ($i=2; $i<=$baris; $i++)
     {
	   
     $no=$i-1;
       //echo $i;
       // membaca data dari mulai baris kedua
       $itemcode = trim($data->val($i, 1));
       $itemcode = str_replace("'","",$itemcode);

       $itemname    = $data->val($i, 2);

       $variantcode    = trim($data->val($i, 3));
       $variantcode    = str_replace("'","",$variantcode);

       $barcode=$itemcode.$variantcode;
       
       $unitprice   = $data->val($i, 4);
       $unitprice   = str_replace(',','',$unitprice);

       $qty = $data->val($i, 5);
       $disc = $data->val($i, 6);
       $subtotal = $data->val($i, 7);
       $subtotal =  str_replace(',','',$subtotal);

       $polybag = $data->val($i, 8);

       $co_mapping = $data->val($i, 9);
       
       //menghapus semua format number bawaan excel
    
       // echo"<br><br>".$docno;
       
	     // setelah data dibaca, sisipkan ke dalam tabel
 
       
       $query = "INSERT INTO so_finishing_detail (kode_so,tgl_stok_pagi,barcode,stok,price)
       VALUES ('$kode_so','$tanggal_so','$barcode','$qty','$unitprice') ON DUPLICATE KEY UPDATE stok=stok+$qty ";

       $hasil = mysql_query($query);
      
        //echo $query; die();
    //   if ($hasil) {
    //      echo"<br>$query-data sukses diimport";
    //   } else {
    //      echo"<br>$query-data gagal diimport-$query";
    //   }
   
      $total_qty+=$qty;
      $total_subtotal+=$subtotal;
   }

   $in="INSERT INTO so_finishing (kode_so,tgl_stok_pagi,pabrik,total_qty,total_hpj,input_date,update_by,
                 upload_date,complete,keterangan,is_batal)
                 VALUES ('$kode_so','$tanggal_so','$pabrik','$total_qty','$total_subtotal',NOW(),'$username',NOW(),
                0,'$ket',0);";
   $qin = mysql_query($in);    
   
   $c="COMMIT;";  
$qc=mysql_query($c);
//    echo $in."<br>";         
   //die();
   // tampilan status sukses dan gagal
   if($qin){
      echo "<h3>Proses import data selesai.</h3>";
   // echo $query."<br>";
      header('location:rekap_so_finishing.php?action=search');  
   } else {
      echo $qin;
   }
   
		
} else
  {
   echo "<script>";
   echo "alert('Salah Format File, harus .xls');";
   echo "document.location=\"rekap_so_finishing.php?action=search\";";
   echo "</script>";
  }

}// end main IF
