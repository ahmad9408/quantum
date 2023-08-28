<?php
  session_start();
  @$username=$_SESSION["username"];
  if(empty($username)){
       die('You can\'t see this page');
  }

  
  /*Last Update Tgl 9 juli 2012 udah dibackup
  Edit 1 feb 2019 betulkan dinamis untuk memunculkan nilai hpp
  */
 require_once("config.php");
// require_once('array2json.php');
 @$tgl=$_POST['t'];
 @$barcode=$_POST['b'];
 @$lstbarcode=$_POST['lb'];
 @$txt_nama=$_POST['n'];
 @$barcode_13=$_POST['b13'];
 @$gudang=$_POST['g']; 
 @$qty=$_POST['q'];
 @$jenis=$_POST['j'];
 @$tp_gudang=$_POST['tg'];//tipe gudang terdiri dari o suho,m=fob,g=cmt
 @$sa=$_POST['sa']; //Stok Awal
 @$sk=$_POST['sk']; //Stok Akhir
 //Digunakan untuk pembatasan tampilan record
 @$bts1=$_POST['b1'];
 @$bts2=$_POST['b2'];
 @$id_caller=$_POST['ic'];
 @$time1=$_POST['t1'];
 @$time2=$_POST['t2'];
 @$interval=$_POST['inv'];
  @$nama_file=trim($_POST['kf']);
  @$id_koreksi=$_POST['ik'];
  @$nama_sm=$_POST['nm_sm'];
  
  //====Nip SM
  @$nipSM=$_POST['nsm'];
  @$nipInputer=$_POST['ni'];
  
  @$nama=$_POST['n'];
  @$barcode=$_POST['b'];
  @$md=$_POST['md'];
  @$markas=$_POST['m'];
  
  @$jumlah_koreksi=$_POST['jk'];//28102020 minus positif

 //die('Jenis'.$jenis);
 
  $isShowHpp=0; 
 $sql="SELECT COUNT(*) ada  FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
 $res_show_hpp=mysql_query($sql);
 list($check_hpp)=mysql_fetch_array($res_show_hpp);
 if($check_hpp>=1){
	 $isShowHpp=1;
 }
 
 
 if($jenis=='otl'){// hitung bruto menghasilkan 2 variable qty bruto dan nilai penjualan bruto  
	
	$sql_qty="SELECT qty,hpj FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	$res=mysql_query($sql_qty) or die($sql_qty.' # '. mysql_error());
	list($qty_lama,$hpj)=mysql_fetch_array($res);
	if($qty_lama==""){	
		 $qty=1;
		 $sql_produk="select hargajual,nama from produk where kode='$barcode' limit 1";
		 $res_harga=mysql_query($sql_produk) or die($sql_produk . ' '.mysql_error() . " hargajual Produk");
		 list($hpj,$nama)=mysql_fetch_array($res_harga);
		 
		 	
		 $sql='REPLACE INTO temp_koreksi_outlet(kode_gudang, tgl_koreksi,kode_produk,nama_produk,qty,hpj,user_id)';
	     $sql.="VALUES ('$gudang','$tgl','$barcode','$nama','$qty','$hpj','$username');";
	}else{
		$qty=$qty_lama + $qty;
		$sql="update temp_koreksi_outlet set qty='$qty' WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	    
		
	}
	
	$res=mysql_query($sql) or die($sql . ' '.mysql_error() . " Qty Lma $qty_lama");
    
	echo "$barcode#$qty#$hpj#$sql_qty#$sql Qty Lma $qty_lama qty Baru  $qty";
	
	//echo "qty=$qty#bruto=$bruto";
 }elseif($jenis=='mrk'){// hitung bruto menghasilkan 2 variable qty bruto dan nilai penjualan bruto  
	
	$sql_qty="SELECT qty,hpj FROM temp_koreksi_markas WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	$res=mysql_query($sql_qty) or die($sql_qty.' # '. mysql_error());
	list($qty_lama,$hpj)=mysql_fetch_array($res);
	if($qty_lama==""){	
		 $qty=1;
		 $sql_produk="select hargajual,nama from produk where kode='$barcode' limit 1";
		 $res_harga=mysql_query($sql_produk) or die($sql_produk . ' '.mysql_error() . " hargajual Produk");
		 list($hpj,$nama)=mysql_fetch_array($res_harga);
		 
		 	
		 $sql='REPLACE INTO temp_koreksi_markas(kode_gudang, tgl_koreksi,kode_produk,nama_produk,qty,hpj,user_id)';
	     $sql.="VALUES ('$gudang','$tgl','$barcode','$nama','$qty','$hpj','$username');";
	}else{
		$qty=$qty_lama + $qty;
		$sql="update temp_koreksi_markas set qty='$qty' WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	    
		
	}
	
	$res=mysql_query($sql) or die($sql . ' '.mysql_error() . " Qty Lma $qty_lama");
    
	echo "$barcode#$qty#$hpj#$sql_qty#$sql Qty Lma $qty_lama qty Baru  $qty";
	
	//echo "qty=$qty#bruto=$bruto";
 }elseif($jenis=='dst'){// hitung bruto menghasilkan 2 variable qty bruto dan nilai penjualan bruto  
	$gudang=str_replace("-",".",$gudang);
	$sql_qty="SELECT qty,hpj FROM temp_koreksi_distribusi WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	$res=mysql_query($sql_qty) or die($sql_qty.' # '. mysql_error());
	list($qty_lama,$hpj)=mysql_fetch_array($res);
	if($qty_lama==""){	
		 $qty=1;
		 $sql_produk="select hargajual,nama from produk where kode='$barcode' limit 1";
		 $res_harga=mysql_query($sql_produk) or die($sql_produk . ' '.mysql_error() . " hargajual Produk");
		 list($hpj,$nama)=mysql_fetch_array($res_harga);
		 
		 	
		 $sql='REPLACE INTO temp_koreksi_distribusi(kode_gudang, tgl_koreksi,kode_produk,nama_produk,qty,hpj,user_id)';
	     $sql.="VALUES ('$gudang','$tgl','$barcode','$nama','$qty','$hpj','$username');";
	}else{
		$qty=$qty_lama + $qty;
		$sql="update temp_koreksi_distribusi set qty='$qty' WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	    
		
	}
	
	$res=mysql_query($sql) or die($sql . ' '.mysql_error() . " Qty Lma $qty_lama");
    
	echo "$barcode#$qty#$hpj#$sql_qty#$sql Qty Lma $qty_lama qty Baru  $qty";
	
	//echo "qty=$qty#bruto=$bruto";
 }elseif($jenis=='s_otl'){// search data koreksi outlet
	 $sql="SELECT trim(kode_produk),qty,hpj,trim(nama_produk),approved FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);
	 //die($sql);
	 $hasil='';
	 while(list($barcode,$qty,$hpj,$nama_produk,$approved)=mysql_fetch_array($res)){
		  $hasil.="$barcode#$qty#$hpj#$nama_produk#$approved^";
	 }
	 echo $hasil;
 }elseif($jenis=='s_mrk'){// search data koreksi outlet
	 $sql="SELECT trim(kode_produk),qty,hpj,trim(nama_produk),approved FROM temp_koreksi_markas WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);
	 //die($sql);
	 $hasil='';
	 while(list($barcode,$qty,$hpj,$nama_produk)=mysql_fetch_array($res)){
		  $hasil.="$barcode#$qty#$hpj#$nama_produk#$approved^";
	 }
	 echo $hasil;
 }elseif($jenis=='s_dst'){// search data koreksi distribusi
     $gudang=str_replace("-",".",$gudang);
	 $sql="SELECT trim(kode_produk),qty,hpj,trim(nama_produk),approved FROM temp_koreksi_distribusi WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);
	 //die($sql);
	 $hasil='';
	 while(list($barcode,$qty,$hpj,$nama_produk)=mysql_fetch_array($res)){
		  $hasil.="$barcode#$qty#$hpj#$nama_produk#$approved^";
	 }
	 echo $hasil;
 }elseif($jenis=='h_otl'){// hapus koreksi dari koreksi outlet
	 $sql="delete FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' and kode_produk='$barcode'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='h_mrk'){// hapus koreksi dari koreksi outlet
	 $sql="delete FROM temp_koreksi_markas WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' and kode_produk='$barcode'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='h_dst'){// hapus koreksi dari koreksi outlet
     $gudang=str_replace("-",".",$gudang);
	 $sql="delete FROM temp_koreksi_distribusi WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' and kode_produk='$barcode'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='c_otl'){// Clear koreksi dari koreksi outlet
	 $sql="delete FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='c_mrk'){// Clear koreksi dari koreksi outlet
	 $sql="delete FROM temp_koreksi_markas WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='c_dst'){// Clear koreksi dari koreksi distribusi
     $gudang=str_replace("-",".",$gudang);
	 $sql="delete FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);	 
	 
	 echo 'ok'.$sql;
 }elseif($jenis=='s_g_periode'){// gudang Perperiode pagi
     //die($tgl);
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     $tgl_k=$data_tgl[2] * 1;
	 
	 $today=date('Y-m-d');
	 $periode=$thn.'-'.$bln.'-01';
	 
	 $fields=' stok_awal  ';
	if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
	   
	}else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		  
		} 	
	}
	
	if($tp_gudang=='o'){
		$query_stok="select $fields from rekap_stok_outlet where  kode_outlet='$gudang' AND periode like '$periode%' AND barcode_15='$barcode'";
		if($today==$tgl){
			$query_stok="SELECT stok FROM outlet_stok_perpagi WHERE kode_produk='$barcode' AND kode_outlet='$gudang';";
		}else{
		    $query_stok="select stok_awal from temp_stok_awal_outlet_pertgl where tgl='$tgl' and kode_outlet='$gudang' and kode_produk='$barcode'";	
		}
		
		
		
	}elseif($tp_gudang=='m'){
		$query_stok="select $fields from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%' AND barcode_15='$barcode'";
		$query_stok="select stok_awal from temp_stok_awal_markas_pertgl where tgl='$tgl' and kode_markas='$gudang' and kode_produk='$barcode'";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$query_stok="select $fields from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%' AND barcode_15='$barcode' ; ";
		$query_stok="select stok_awal from temp_stok_awal_distribusi_pertgl where tgl='$tgl' and kode_distribusi='$gudang' and kode_produk='$barcode'";
	}else{
		
	}
	// print_r($data_tgl);
	 //die($periode.' '.$query_stok);
    $hasil=mysql_query($query_stok) or die($query_stok.' # '.mysql_error());          
                    
    list($stk)=mysql_fetch_array($hasil);
    if(empty($stk)){
      $stk=0;
    }   
		 
	 
	 echo "$barcode#$stk#$id_caller#".$sql.''.$tg;
 }elseif($jenis=='k_g'){// Koreksi Gudang
     //die($tgl);
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     $tgl_k=$data_tgl[2] * 1;
	 
	 $periode=$thn.'-'.$bln.'-01';
	 $field="k$tgl_k";
	 $fields=' stok_awal  ';
	if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
	   
	}else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		  
		} 	
	}
	
	if($sa==11111111){//posisi data tidak langsung ambil kedatabase harus di hitung di akhir
	    $today=date('Y-m-d');
	    if($tp_gudang=='o'){
			$query_stok="select $fields from rekap_stok_outlet where  kode_outlet='$gudang' AND periode like '$periode%' AND barcode_15='$barcode'";//mengurangi bebas server
			
			if($today==$tgl){
				$query_stok="SELECT stok FROM outlet_stok_perpagi WHERE kode_outlet='$gudang' AND kode_produk='$barcode';";
			}else{
				$query_stok="select stok_awal from temp_stok_awal_outlet_pertgl where tgl='$tgl' and kode_outlet='$gudang' and kode_produk='$barcode'";
			}
			
		}elseif($tp_gudang=='m'){
			$query_stok="select $fields from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%' AND barcode_15='$barcode'";
		    if($today==$tgl){
				$query_stok="SELECT stok FROM markas_stok_perpagi WHERE kode_markas='$gudang' AND kode_produk='$barcode';";
			}else{
				$query_stok="select stok_awal from temp_stok_awal_outlet_pertgl where tgl='$tgl' and kode_outlet='$gudang' and kode_produk='$barcode'";
			}
			
			
		}elseif($tp_gudang=='g'){
			$gudang=str_replace("-",".",$gudang);
			$query_stok="select $fields from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%' AND barcode_15='$barcode' ; ";
			$query_stok="select stok_awal from temp_stok_awal_distribusi_pertgl where tgl='$tgl' and kode_outlet='$gudang' and kode_produk='$barcode'";
		}else{
			
		}
		// print_r($data_tgl);
		 //die($periode.' '.$query_stok);
		$hasil=mysql_query($query_stok) or die('Error #'.$query_stok.' # '.mysql_error());          
						
		list($sa)=mysql_fetch_array($hasil);
		if(empty($sa)){
		  $sa=0;
		}  
	
	    $qty=$sa-$sk;
		
		
	}
	
    /*
	if($sa!=11111111 && $qty==0){// tidak perlu di update karena tidak ada data yang dikoreksi, rubah tanggal 2012-09-05, proses reset
	    if($tp_gudang=='o'){
			$sql_koreksi="DELETE FROM koreksi_outlet_stok where tanggal='$tgl' and kd_outlet='$gudang' and  kode_15='$barcode' ";
			$table_movement='rekap_stok_outlet';
			$table='outlet';
			$sql_update_rekap="update rekap_stok_outlet set $field='0' where kode_outlet='$gudang' and periode='$periode' and barcode_15='$barcode'";
		}elseif($tp_gudang=='m'){
			$sql_koreksi="DELETE FROM koreksi_markas_stok where tanggal='$tgl' and kd_markas='$gudang' and  kode_15='$barcode' ";
			$table_movement='rekap_stok_markas';
			$table='markas';
			$sql_update_rekap="update rekap_stok_markas set $field='0' where kode_markas='$gudang' and periode='$periode' and barcode_15='$barcode'";
		}elseif($tp_gudang=='g'){
			$sql_koreksi="DELETE FROM koreksi_distribusi_stok where tanggal='$tgl' and kd_distribusi='$gudang' and  kode_15='$barcode' ";
			$table_movement='rekap_stok_distribusi';
			$table='distribusi';
			$sql_update_rekap="update rekap_stok_distribusi set $field='0' where kode_distribusi='$gudang' and periode='$periode' and barcode_15='$barcode'";
		}
	
	    $hasil=mysql_query($sql_koreksi) or die($sql_koreksi.' # '.mysql_error());  
	    $hasil2=mysql_query($sql_update_rekap) or die($sql_update_rekap.' # '.mysql_error()); 
	    echo "ok#$barcode#";
		return;
	}
	*/
	if(empty($barcode_13)){$barcode_13=$barcode;}
	if($tp_gudang=='o'){		
		$sql_koreksi="REPLACE INTO koreksi_outlet_stok(tanggal,kd_outlet, kode_15, kode_13, stok_awal, qty,stok_akhir,update_by, update_date,id) ";
		$sql_koreksi.="VALUES ('$tgl','$gudang','$barcode','$barcode_13','$sa','$qty','$sk','$username',NOW(),'$id_koreksi');";
		$table_movement='rekap_stok_outlet';
		$table='outlet';
		$sql_update_rekap="update rekap_stok_outlet set $field='$qty' where kode_outlet='$gudang' and periode='$periode' and barcode_15='$barcode'";
		$sql_approved="UPDATE temp_koreksi_outlet SET approved=1  WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	}elseif($tp_gudang=='m'){
		$sql_koreksi="REPLACE INTO koreksi_markas_stok(tanggal,kd_markas, kode_15, kode_13, stok_awal, qty,stok_akhir,update_by, update_date) ";
		$sql_koreksi.="VALUES ('$tgl','$gudang','$barcode','$barcode_13','$sa','$qty','$sk','$username',NOW());";
		
		$table_movement='rekap_stok_markas';
		$table='markas';
		$sql_update_rekap="update rekap_stok_markas set $field='$qty' where kode_markas='$gudang' and periode='$periode' and barcode_15='$barcode' ";
		$sql_approved="UPDATE temp_koreksi_markas SET approved=1  WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$sql_koreksi="select $fields from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%' AND barcode_15='$barcode' ; ";
		$sql_koreksi="REPLACE INTO koreksi_distribusi_stok(tanggal,kd_distribusi, kode_15, kode_13, stok_awal, qty,stok_akhir,update_by, update_date) ";
		$sql_koreksi.="VALUES ('$tgl','$gudang','$barcode','$barcode_13','$sa','$qty','$sk','$username',NOW());";
		$table_movement='rekap_stok_distribusi';
		$table='distribusi';
		$sql_update_rekap="update rekap_stok_distribusi set $field='$qty' where kode_distribusi='$gudang' and periode='$periode' and barcode_15='$barcode'";
		
		$sql_approved="UPDATE temp_koreksi_distribusi SET approved=1  WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl' AND kode_produk='$barcode'";
	}else{
		
	}
	// print_r($data_tgl);
	 //die($periode.' '.$query_stok);
	# die($sql_koreksi);
    $hasil=mysql_query($sql_koreksi) or die('Error #'.$sql_koreksi.' # '.mysql_error());  
	$hasil1=mysql_query($sql_approved) or die('Error #'.$sql_approved.' # '.mysql_error()); 
	/*
	$hasil2=mysql_query($sql_update_rekap) or die($sql_update_rekap.' # '.mysql_error()); 
	if(mysql_affected_rows()<=0){ 
		   $sql_otl="select trim(nama),jenis from  outlet where id='$gudang'"; style
		   $res=mysql_query($sql_otl);
		   list($nama_otl,$jenis)=mysql_fetch_array($res);
			 
		   $sql_pro="select nama,hargadasar,hargajual,kode_grade_a from produk where kode='$barcode' limit 1";
		   $result_pro=mysql_query($sql_pro);// ;//or die('Error'.$sql_pro);
		   list($nama_prod,$hpp,$hpj,$kode_13)=mysql_fetch_array($result_pro);
		   
		   $nama_prod=addslashes($nama_prod);
		   $sql_movement_stok_awal="REPLACE INTO $table_movement(periode,tgl_stok_awal,kode_$table,nama_$table,barcode_13,barcode_15,nama,hpp,hpj,$field,jenis)
									VALUES ('$periode','$tgl1','$gudang','$nama_otl','$kode_13','$barcode','$nama_prod','$hpp','$hpj','$qty','$jenis');";
		   $rslt=mysql_query($sql_movement_stok_awal) or die($sql_movement_stok_awal.' '.mysql_error());
		  
		   
		  
	}
	  */        
                    
      mysql_free_result($hasil);
	  mysql_free_result($hasil1);
	 # mysql_free_result($hasil2);
	  
		 
	 
	 echo "ok#$barcode#".$sql_update_rekap.''.$tg.'#'.$sql_koreksi.'#'.$sql_approved.'#'.$query_stok;
 }elseif($jenis=='d_kg'){// Delete Koreksi Gudang
     //die($tgl);
	 echo "ok#";//dirubah tanggal 30 agustus 2012, untuk menghindari pengupdate oleh 2 komputer dengan data yang berbeda
	 return;
	if($tp_gudang=='o'){
		$sql_koreksi="DELETE FROM koreksi_outlet_stok WHERE kd_outlet='$gudang' AND tanggal='$tgl' AND ID LIKE 'KR%'";//dirubah tanggal 7 nov 2013
	}elseif($tp_gudang=='m'){
		$sql_koreksi="DELETE FROM koreksi_outlet_stok WHERE kd_outlet='$gudang' AND tanggal='$tgl'";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$sql_koreksi="DELETE FROM koreksi_distribusi_stok WHERE kd_distribusi='$gudang' AND tanggal='$tgl'";
	}else{
		
	}
	// print_r($data_tgl);
	 //die($periode.' '.$query_stok);
    $hasil=mysql_query($sql_koreksi) or die($sql_koreksi.' # '.mysql_error());          
                    
      
		 
	 
	 echo "ok#".$sql_koreksi.''.$tg;
 }elseif($jenis=='m_so'){// monitoring Stok Opname
      //die($tgl);
	if($time1==''){
	   //$time1=date('Y-m-d H:i:s');	
	}
	$interval=$interval * -1;
	if($tp_gudang=='o'){
		$sql_koreksi="SELECT kode_gudang,kode_produk,qty,hpj,`user_id`,DATE_FORMAT(waktu,'%Y-%m-%d %T'),DATE_FORMAT(tgl_koreksi,'%Y-%m-%d') FROM temp_koreksi_outlet";
		//$sql_koreksi.=" WHERE waktu BETWEEN DATE_FORMAT(DATE_ADD(NOW(),INTERVAL $interval SECOND),'%Y-%m-%d %T') AND DATE_FORMAT(NOW(),'%Y-%m-%d %T');"; ganti tanggal 4 sep
		$sql_koreksi.=" WHERE waktu BETWEEN '$time1' AND '$time2';";
	}elseif($tp_gudang=='m'){
		$sql_koreksi="SELECT kode_gudang,kode_produk,qty,hpj,`user_id`,DATE_FORMAT(waktu,'%Y-%m-%d %T') FROM temp_koreksi_markas";
		//$sql_koreksi.=" WHERE waktu BETWEEN DATE_FORMAT(DATE_ADD(NOW(),INTERVAL $interval SECOND),'%Y-%m-%d %T') AND DATE_FORMAT(NOW(),'%Y-%m-%d %T');";
		$sql_koreksi.=" WHERE waktu BETWEEN '$time1' AND '$time2';";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$sql_koreksi="SELECT kode_gudang,kode_produk,qty,hpj,`user_id`,DATE_FORMAT(waktu,'%Y-%m-%d %T') FROM temp_koreksi_distribusi";
		$sql_koreksi.=" WHERE waktu BETWEEN DATE_FORMAT(DATE_ADD(NOW(),INTERVAL $interval SECOND),'%Y-%m-%d %T') AND DATE_FORMAT(NOW(),'%Y-%m-%d %T');";
	}else{
		
	}
	// print_r($data_tgl);
	 //die($sql_koreksi);
   $hasil='';
   $res=mysql_query($sql_koreksi) or die($sql_koreksi.' # '.mysql_error()); 
	 while(list($gudang,$barcode,$qty,$hpj,$user,$waktu,$tgl)=mysql_fetch_array($res)){
		  $hasil.="$gudang#$barcode#$qty#$hpj#$user#$waktu#$tgl^";
	 }
	 mysql_free_result($res);
	 echo $hasil;       
                    
      
		 
	 
	 
 }elseif($jenis=='im_g_periode'){// masukan data stok perperiode ke table temp gudang Perperiode pagi
     //die($tgl);
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     $tgl_k=$data_tgl[2] * 1;
	 
	 $today=date('Y-m-d');
	 if($today==$tgl){
		 
		die('ok#[data stok perpagi sudah terinput perpagi ini]') ; 
	 }
	 
	 //cek apakah hari ini dengan tgl dan gudang yang sama telah isi data stok perpagi
	 //jika belum maka isi ulang dulu
	 $sql="SELECT count(*) FROM history_inputstokpagi WHERE  kode_gudang='$gudang' AND tgl_stok_pagi='$tgl' AND
 updatedate LIKE CONCAT(DATE_FORMAT(NOW(),'%Y-%m-%d'),'%');";
	 $hasil=mysql_query($sql) or die($sql.' # '.mysql_error());   
	 list($sudahinput)=mysql_fetch_array($hasil);
	 if($sudahinput>0){
		  die('ok#[data stok perpagi sudah terinput perpagi ini]') ;
	 }
	 
	 
	 $periode=$thn.'-'.$bln.'-01';
	 
	 $fields=' stok_awal  ';
	if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
	   
	}else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		  
		} 	
	}
	
	
	
	if($tp_gudang=='o'){
		$sql_delete="DELETE FROM temp_stok_awal_outlet_pertgl where tgl not like '$tgl%' and kode_outlet='$gudang'";
		
		$query_stok="REPLACE INTO temp_stok_awal_outlet_pertgl(tgl, kode_outlet, kode_produk, stok_awal)";
		$query_stok.=" select '$tgl',kode_outlet,barcode_15,$fields from rekap_stok_outlet where  kode_outlet='$gudang' AND periode like '$periode%'";
	}elseif($tp_gudang=='m'){
		$query_stok="select $fields,barcode_15,kode_outlet,periode from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%'";
		$sql_delete="DELETE FROM temp_stok_awal_markas_pertgl where tgl not like '$tgl%' and kode_markas='$gudang'";
		
		$query_stok="REPLACE INTO temp_stok_awal_markas_pertgl(tgl, kode_markas, kode_produk, stok_awal)";
		$query_stok.=" select '$tgl',kode_markas,barcode_15,$fields from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%'";
		
		//die($query_stok);
	}elseif($tp_gudang=='g'){
		/*
		$sql="select tgl FROM temp_stok_awal_distribusi_pertgl where tgl like '$tgl%' and kode_distribusi='$gudang' limit 1";
		$res=mysql_query($sql) or die("die ".$sql);
		list($tgl)=mysql_fetch_array();
		if(!empty($tgl)){return "ok#$sql";}
		*/
		$gudang=str_replace("-",".",$gudang);
		$sql_delete="DELETE FROM temp_stok_awal_distribusi_pertgl where tgl not like '$tgl%' and kode_distribusi='$gudang'";
		$query_stok="REPLACE INTO temp_stok_awal_distribusi_pertgl(tgl, kode_distribusi, kode_produk, stok_awal)";
		$query_stok.=" select '$tgl',kode_distribusi,barcode_15,$fields from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%'";
	}else{
		
	}
	// print_r($data_tgl);
	//==========Reset Stok dirubah tanggal 1jan 2013
	//$hasil=mysql_query($sql_delete) or die($sql_delete.' # '.mysql_error());          
                    
	
	 //die($periode.' '.$query_stok);
    $hasil=mysql_query($query_stok) or die($query_stok.' # '.mysql_error());    
	
	//untuk kepentingan history
	$kode=$gudang.'_'.$tgl;
	$sql="REPLACE INTO history_inputstokpagi (kode_gudang,tgl_stok_pagi,update_by,updatedate)
VALUES ('$gudang','$tgl','$username',now());"; 
    $hasil=mysql_query($sql) or die($sql.' # '.mysql_error());        
                    
     die("ok#[data stok perpagi sudah terisi]");
	 
	 //echo "ok#$query_stok#".$sql_delete;
 }elseif($jenis=='sg_g_periode'){// gudang Perperiode pagi di grouop dilangsungkan tanpa ada filter barcode
     //die($tgl);
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     $tgl_k=$data_tgl[2] * 1;
	 
	 $periode=$thn.'-'.$bln.'-01';
	 
	 $fields=' stok_awal  ';
	if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
	   
	}else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		  
		} 	
	}
	
	
	if($tp_gudang=='o'){
		$query_stok="select $fields,barcode_15 from rekap_stok_outlet where  kode_outlet='$gudang' AND periode like '$periode%' ";
	}elseif($tp_gudang=='m'){
		$query_stok=" select $fields,barcode_15 from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%' ";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$query_stok="select $fields,barcode_15 from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%'  ; ";
	}else{
		
	}
	// print_r($data_tgl);
	 //die($periode.' '.$query_stok);
	 
	$hasil='';
	$count=0;
    $res=mysql_query($query_stok) or die($query_stok.' # '.mysql_error());          
    while(list($stk,$barcode)=mysql_fetch_array($res)){
		 $count++;
		 if(empty($stk)){
           $stk=0;
         }
		 $hasil.=$barcode.'#'.$stk.'#'.$count.'^';
		
	}
    
      
		 
	 
	 echo $hasil;
 }elseif($jenis=='sg_g_periode_withlimit'){// gudang Perperiode pagi di grouop dilangsungkan tanpa ada filter barcode
     //die($tgl);
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     $tgl_k=$data_tgl[2] * 1;
	 
	 $periode=$thn.'-'.$bln.'-01';
	 
	 $fields=' stok_awal  ';
	if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
	   
	}else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		  
		} 	
	}
	
	
	if($tp_gudang=='o'){
		$query_stok="select $fields,barcode_15 from rekap_stok_outlet where  kode_outlet='$gudang' AND periode like '$periode%' limit $bts1,$bts2; ";/* datanya berat 
		kalau ambil ke table ini */
		$query_stok="SELECT kode_produk,stok_awal FROM temp_stok_awal_outlet_pertgl WHERE kode_outlet='$gudang' AND tgl='$tgl'  limit $bts1,$bts2; ";// pake cara ini lama
		$query_stok="SELECT  trim(sa.kode_produk),sa.stok_awal FROM   temp_stok_awal_outlet_pertgl AS sa  INNER JOIN temp_koreksi_outlet AS tk 
        			ON (sa.tgl = tk.tgl_koreksi) AND (sa.kode_outlet = tk.kode_gudang) AND (sa.kode_produk = tk.kode_produk)
        			AND sa.kode_outlet='$gudang' and tk.tgl_koreksi ='$tgl' limit $bts1,$bts2;";
		
	}elseif($tp_gudang=='m'){
		$query_stok=" select $fields,barcode_15 from rekap_stok_markas where  kode_markas='$gudang' AND periode like '$periode%'  limit $bts1,$bts2;  ";
		$query_stok="SELECT kode_produk,stok_awal FROM temp_stok_awal_markas_pertgl WHERE kode_markas='$gudang' AND tgl='$tgl'  limit $bts1,$bts2; ";
		$query_stok="SELECT  trim(sa.kode_produk),sa.stok_awal FROM   temp_stok_awal_markas_pertgl AS sa  INNER JOIN temp_koreksi_markas AS tk 
        			ON (sa.tgl = tk.tgl_koreksi) AND (sa.kode_markas = tk.kode_gudang) AND (sa.kode_produk = tk.kode_produk)
        			AND sa.kode_markas='$gudang' and tk.tgl_koreksi ='$tgl' limit $bts1,$bts2;";
		//die(" Markas Koreksi jenis sg_g_periode_withlimit ".$query_stok);
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$query_stok="select $fields,barcode_15 from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%'  limit $bts1,$bts2; ";
		$query_stok="SELECT kode_produk,stok_awal FROM temp_stok_awal_distribusi_pertgl WHERE kode_distribusi='$gudang' AND tgl='$tgl'  limit $bts1,$bts2; ";
		$query_stok="SELECT  trim(sa.kode_produk),sa.stok_awal FROM   temp_stok_awal_distribusi_pertgl AS sa  INNER JOIN temp_koreksi_distribusi AS tk 
        			ON (sa.tgl = tk.tgl_koreksi) AND (sa.kode_distribusi = tk.kode_gudang) AND (sa.kode_produk = tk.kode_produk)
        			AND sa.kode_distribusi='$gudang' and tk.tgl_koreksi ='$tgl' limit $bts1,$bts2;";
		
	}else{
		
	}
	// print_r($data_tgl);
	 //die($periode.' '.$query_stok);
	 
	$hasil='';
	$count=0;
    $res=mysql_query($query_stok) or die($query_stok.' # '.mysql_error());          
    while(list($barcode,$stk)=mysql_fetch_array($res)){
		 $count++;
		 if(empty($stk)){
           $stk=0;
         }
		 $hasil.=$barcode.'#'.$stk.'^';
		
	}
    
      
		 
	 //die($query_stok);  
	 echo $hasil;
 }elseif($jenis=='jsa_g'){// jumlah stok awal gudang pertanggal
     //die($tgl);
		
	if($tp_gudang=='o'){
		$sql="SELECT count(stok_awal) FROM temp_stok_awal_outlet_pertgl where tgl like '$tgl%' and kode_outlet='$gudang'";
		
	}elseif($tp_gudang=='m'){
		$sql="SELECT count(stok_awal) FROM temp_stok_awal_markas_pertgl where tgl like '$tgl%' and kode_markas='$gudang'";
	}elseif($tp_gudang=='g'){
		$gudang=str_replace("-",".",$gudang);
		$sql="SELECT count(stok_awal) FROM temp_stok_awal_distribusi_pertgl where tgl like '$tgl%' and kode_distribusi='$gudang'";
	}else{
		
	}
	
	$hasil=mysql_query($sql) or die($sql.' # '.mysql_error());          
                    
	list($jml_data)=mysql_fetch_array($hasil);        
                    
     
	 
	 echo "$jml_data#$sql";
 }elseif($jenis=='ups'){
	
	if($tp_gudang=='o'){
		$table='outlet';
	}elseif($tp_gudang=='m'){
		$table='markas';
	}elseif($tp_gudang=='g'){
		$table='distribusi';
	}
	 
	 //ambil barang perkode diatas
	 $sql="SELECT kode_gudang,date_format(tgl_koreksi,'%Y-%m-%d'),barcode_15,barcode_13,qty FROM temp_koreksi_".$table."_v2 Where kode_temp='$nama_file'";
	 //die($sql);
	 $today=date('Y-m-d');
	 $res=mysql_query($sql)or die('16error#'.mysql_error().'#'.$sql);
	 while(list($gudang,$tgl,$barcode_15,$barcode_13,$qty)=mysql_fetch_array($res)){
		  
		   $sql="SELECT sum(qty) FROM temp_koreksi_".$table."_v2 
		         Where kode_gudang='$gudang' and barcode_15='$barcode_15' and tgl_koreksi like '$tgl%'";
		   $res2=mysql_query($sql)or die('15error#'.mysql_error().'#'.$sql);	
		   list($koreksi_awal)=mysql_fetch_array($res2);
		   
		   if(empty($koreksi_awal)){
			    $koreksi_awal=0;   
		   }else{
			   $qty=$koreksi_awal;
		   }
				 
		   if($today==$tgl){//di edit tanggal 24 des 2013
			    $sql_stok="SELECT stok FROM outlet_stok_perpagi WHERE kode_produk='$barcode_15' AND kode_outlet='$gudang';";
			}else{
				 $sql_stok="select stok_awal from temp_stok_awal_".$table."_pertgl where tgl='$tgl' and kode_".$table."='$gudang' and kode_produk='$barcode_15'";	
			}
		   
		  
		   $res2=mysql_query($sql_stok) or die('14error#'.mysql_error().'#'.$sql_stok);
		   list($stok_awal)=mysql_fetch_array($res2);
		   //insert nilai koreksi
		   if(empty($stok_awal)){
			   $stok_awal=0;   
		   }
		   
		   $koreksi=$qty-$stok_awal;
		   
		   $d=explode("-",$tgl);		
			$table_koreksi_stok='';
			if($d[0].'-'.$d[1]!=date('Y-m')){
				 $table_koreksi_stok='koreksi_'.$table.'_stok_'.$d[1].$d[0];  			 
			}else{
				 $table_koreksi_stok='koreksi_'.$table.'_stok';			
			}
		   
		  $sql_koreksi="REPLACE INTO ".$table_koreksi_stok."(tanggal,kd_".$table.", kode_15, kode_13, stok_awal, qty,stok_akhir,update_by, update_date) ";
		  $sql_koreksi.="VALUES ('$tgl','$gudang','$barcode_15','$barcode_13','$stok_awal','$koreksi','$qty','$username',NOW());";
		  $res2=mysql_query($sql_koreksi) or die('13error#'.mysql_error().'#'.$sql_koreksi);
		  // insert jika duplcate tambah qty lama dengan baru
	 }
	 
	 //update dulu kode yang di temp
	 $sql="UPDATE temp_koreksi_".$table."_v2 SET approveddate=NOW(),approvedby='$username' WHERE kode_temp='$nama_file'";
	 $res2=mysql_query($sql) or die('12error#'.mysql_error().'#'.$sql);
	 die("ok#");
	 
 }elseif($jenis=='bsoo'){
	 $sql="DELETE FROM temp_koreksi_outlet_v2 WHERE kode_temp='$nama_file' AND (approvedby IS NULL OR approvedby='')";
	 $res2=mysql_query($sql) or die('error#'.mysql_error().'#'.$sql);
	 die("ok#$sql");
 }elseif($jenis=='bsom'){
	 $sql="DELETE FROM temp_koreksi_outlet_v2 WHERE kode_temp='$nama_file' AND (approvedby IS NULL OR approvedby='')";
	 $res2=mysql_query($sql) or die('error#'.mysql_error().'#'.$sql);
	 die("ok#$sql");
 }elseif($jenis=='bso_g_periode'){// masukan data stok perperiode ke table temp gudang Perperiode pagi
     //die($tgl);
	 $tgl=date('Y-m-d');
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     $hr=$data_tgl[2] * 1;// buat jadi integer
     
	 
	 $periode=$thn.'-'.$bln.'-01';
	
	 
	 $sql="SELECT count(*) FROM history_produk_blum_so WHERE  kode_gudang='$gudang' AND updatedate LIKE CONCAT(DATE_FORMAT(NOW(),'%Y-%m-%d'),'%');";
	 $hasil=mysql_query($sql) or die($sql.' # '.mysql_error());   
	 list($sudahinput)=mysql_fetch_array($hasil);
	 if($sudahinput>0){
		  die('ok#[data stok perpagi sudah terinput perpagi ini]') ;
	 }
	 
	 
	 
	
	
	
	if($tp_gudang=='o'){
		$sql_delete="DELETE FROM temp_produk_blm_stok_opname where  kode_outlet='$gudang'";
		
		$query_stok="REPLACE INTO temp_produk_blm_stok_opname(periode,kode_outlet, barcode_15,barcode_13, hpp, hpj, stok_akhir, last_update)
 SELECT  k.periode,k.kode_outlet,k.barcode_15,k.barcode_13,k.hpp,k.hpj,k.stok_akhir,NOW() AS last_show FROM (SELECT   r.periode,r.kode_outlet,r.barcode_15,r.barcode_13,r.hpp,r.hpj,r.stok_akhir FROM
  rekap_stok_outlet AS r  WHERE periode ='$periode' AND r.kode_outlet='$gudang')  AS k LEFT JOIN (SELECT kode_15 FROM koreksi_outlet_stok
  WHERE tanggal LIKE '".$thn.'-'.$bln."%' AND kd_outlet='$gudang' )AS b ON b.kode_15 =k.barcode_15 WHERE b.kode_15 IS NULL;";
	}elseif($tp_gudang=='m'){
		$sql_delete="DELETE FROM temp_produk_blm_stok_opname where  kode_outlet='$gudang'";
		
		$query_stok="REPLACE INTO temp_produk_blm_stok_opname(periode,kode_outlet, barcode_15,barcode_13, hpp, hpj, stok_akhir, last_update)
 SELECT  k.periode,k.kode_outlet,k.barcode_15,k.barcode_13,k.hpp,k.hpj,k.stok_akhir,NOW() AS last_show FROM (SELECT   r.periode,r.kode_outlet,r.barcode_15,r.barcode_13,r.hpp,r.hpj,r.stok_akhir FROM
  rekap_stok_outlet AS r  WHERE periode ='$periode' AND r.kode_outlet='$gudang')  AS k LEFT JOIN (SELECT kode_15 FROM koreksi_outlet_stok
  WHERE tanggal LIKE '".$thn.'-'.$bln."%' AND kd_outlet='$gudang' )AS b ON b.kode_15 =k.barcode_15 WHERE b.kode_15 IS NULL;";
		
		//die($query_stok);
	}elseif($tp_gudang=='g'){
		/*
		$sql="select tgl FROM temp_stok_awal_distribusi_pertgl where tgl like '$tgl%' and kode_distribusi='$gudang' limit 1";
		$res=mysql_query($sql) or die("die ".$sql);
		list($tgl)=mysql_fetch_array();
		if(!empty($tgl)){return "ok#$sql";}
		*/
		$gudang=str_replace("-",".",$gudang);
		$sql_delete="DELETE FROM temp_stok_awal_distribusi_pertgl where tgl not like '$tgl%' and kode_distribusi='$gudang'";
		$query_stok="REPLACE INTO temp_stok_awal_distribusi_pertgl(tgl, kode_distribusi, kode_produk, stok_awal)";
		$query_stok.=" select '$tgl',kode_distribusi,barcode_15,$fields from rekap_stok_distribusi where  kode_distribusi='$gudang' AND periode like '$periode%'";
	}else{
		
	}
	// print_r($data_tgl);
	//==========Reset Stok
	$hasil=mysql_query($sql_delete) or die($sql_delete.' # '.mysql_error());          
                    
	
	 //die($periode.' '.$query_stok);
    $hasil=mysql_query($query_stok) or die($query_stok.' # '.mysql_error());    
	
	//untuk kepentingan history
	
    $sql="REPLACE INTO history_produk_blum_so(kode_gudang, periode, update_by, updatedate)
VALUES ('$gudang', '$tgl', '$username',now());";
    $hasil=mysql_query($sql) or die($sql.' # '.mysql_error());        
                    
     die("ok#baru");// maksud baru diisi
	 
	 //echo "ok#$query_stok#".$sql_delete;
 }elseif($jenis=='tbso'){//total belum stok opname
     $tgl=date('Y-m-d');
	 $data_tgl=explode('-',$tgl);
	 $thn=$data_tgl[0];
	 $bln=$data_tgl[1];
     
	 $periode=$thn.'-'.$bln.'-01';
	 
	 if($tp_gudang=='o'){
		  $sql="SELECT SUM(stok_akhir),SUM(hpj * stok_akhir) FROM temp_produk_blm_stok_opname 
		  WHERE periode LIKE '$periode%' AND kode_outlet LIKE '$gudang'";
	 }elseif($tp_gudang=='m'){
	
	 }elseif($tp_gudang=='g'){
		 
	 }
	 
	 $hasil=mysql_query($sql) or die($sql.' # '.mysql_error());
	 list($totalQty,$totalRp)=mysql_fetch_array($hasil);
	 if(empty($totalQty)){$totalQty=0;}
	 if(empty($totalRp)){$totalRp=0;}
	 echo $totalQty.'#'.$totalRp;


 }elseif($jenis=='sagt'){//Stok Awal seluruh Gudang Pertanggal

	
	 if($tp_gudang=='o'){
		$filter_pabrik = " AND pb.id_group=4 ";
	 }elseif($tp_gudang=='m'){
		$filter_pabrik = " AND pb.id_group=2 ";
	 }elseif($tp_gudang=='g'){
		$filter_pabrik = " AND pb.id_group=3 ";
	 }

	 $table="finishing";

	 $sql_inner_group = ' LEFT JOIN pabrik AS pb on sp.pabrik = pb.id ';


	  if($isShowHpp==1){//untuk kemudahan koding
	   $script_hpp='p.hargadasar';  	
	 }else{
		$script_hpp='0'; 
	 }
	 
	 $sql_tbhn='';
	 
	 if(!empty($barcode)){
		  $sql_tbhn.=" AND p.kode like '$barcode%'";
     }
	 if(!empty($nama)){
		  $sql_tbhn.=" AND p.nama like '$nama%'";
     }
	 
	 
	 
	//  $sql=" SELECT   upper(sp.kode_".$table.") as id,sum(sp.stok_awal) as stokawal,SUM(sp.stok_awal * $script_hpp) as hpp, SUM(sp.stok_awal * p.hargajual) as hpj FROM produk AS p ";
	//  $sql.=" INNER JOIN temp_stok_awal_".$table."_pertgl AS sp ON (p.kode = sp.kode_produk)";
	//  $sql.=" WHERE sp.tgl LIKE '$tgl' AND sp.stok_awal>0 $sql_tbhn ";
	//  $sql.=" GROUP BY sp.tgl, sp.kode_".$table.";";
	//  die($sql);
	//  AND sp.stok_awal>0 itu dihilangkan


	 $d=explode("-",$tgl);		
	$table_koreksi_stok='';
	if($d[0].'-'.$d[1]!=date('Y-m')){
		 $table_koreksi_stok='koreksi_'.$table.'_stok_'.$d[1].$d[0];  			 
	}else{
		 $table_koreksi_stok='koreksi_'.$table.'_stok';			
	}
	
	if($jumlah_koreksi!==''){
		if($jumlah_koreksi=='+'){
			$sql_tbhn.=" AND sp.qty > 0";
		}elseif($jumlah_koreksi=='-'){
			$sql_tbhn.=" AND sp.qty < 0";
		}
	}

	
	 
	 $sql="SELECT  SQL_CACHE sp.pabrik as id, SUM(sp.stok_awal) as stokawal , SUM(sp.stok_awal * $script_hpp) as hpp , SUM(sp.stok_awal  * p.hargajual ) as hpj, pb.id_group
	  FROM ".$table_koreksi_stok." AS sp  LEFT JOIN  produk p ON (p.kode = sp.kode_15) $sql_inner_group
	 WHERE sp.tanggal like '$tgl%' $sql_tbhn $filter_pabrik  GROUP BY sp.tanggal,sp.pabrik";
	
	 #die($sql);
	 
	 if($username=='budi-it'){
		$sql_debug=mysql_escape_string($sql);
		$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis ' , '$sql_debug');";
		mysql_query($sql_insert);
	 }

	 
	 $res=mysql_query($sql);// or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
	die(json_encode($result));


 }elseif($jenis=='ssd'){// Stok awal berdasarkan tgl 
    	 
	if($tp_gudang=='o'){
		$filter_pabrik = " AND pb.id_group=4 ";
	 }elseif($tp_gudang=='m'){
		$filter_pabrik = " AND pb.id_group=2 ";
	 }elseif($tp_gudang=='g'){
		$filter_pabrik = " AND pb.id_group=3 ";
	 }

	 $table="finishing";

	 $sql_inner_group = ' LEFT JOIN pabrik AS pb ON k.pabrik = pb.id ';

	  if($isShowHpp==1){//untuk kemudahan koding
	   $script_hpp='p.hargadasar';  	
	 }else{
		$script_hpp='0'; 
	 }
	 
	 $sql_tbhn='';	 
	 if(!empty($barcode)){
		  $sql_tbhn.=" AND p.kode like '$barcode%'";
     }
	 if(!empty($nama)){
		  $sql_tbhn.=" AND p.nama like '$nama%'";
     }
	 if(!empty($md)){
		  $sql_tbhn.=" AND p.wil_md = '$md'";
	 }
	 
	if(!empty($lstbarcode)){
	   $d=explode(",",$lstbarcode);
	   $dt='';	
	   foreach($d as $val){
		   if(trim($val)!=''){
			   $dt.="p.kode like '$val%' OR ";
		  }
	   }
	   $dt=substr($dt,0,strlen($dt)-3);
	   $sql_tbhn.=" AND (  $dt  ) ";	
	}
	 
	 
	  $d=explode("-",$tgl);		
	$table_koreksi_stok='';
	if($d[0].'-'.$d[1]!=date('Y-m')){
		 $table_koreksi_stok='koreksi_'.$table.'_stok_'.$d[1].$d[0];  			 
	}else{
		 $table_koreksi_stok='koreksi_'.$table.'_stok';			
	}
	
	
	if($jumlah_koreksi!==''){
		if($jumlah_koreksi=='+'){
			$sql_tbhn.=" AND k.qty > 0";
		}elseif($jumlah_koreksi=='-'){
			$sql_tbhn.=" AND k.qty < 0";
		}
	}
	 $sql=" SELECT  UPPER(k.pabrik) AS id,SUM(k.stok_akhir) AS so,SUM(k.stok_akhir * ".$script_hpp.") AS hpp , SUM(k.stok_akhir * p.hargajual) AS hpj, pb.id_group FROM produk AS p
      RIGHT JOIN ".$table_koreksi_stok." AS k ON (p.kode = k.kode_15)
	  $sql_inner_group 
      WHERE k.tanggal LIKE '$tgl%'  $sql_tbhn $filter_pabrik GROUP BY k.pabrik ;";
	 // die($sql);
	 if($username=='budi-it'){
			$sql_debug=mysql_escape_string($sql);
			$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis -tg $tp_gudang' , '$sql_debug');";
			mysql_query($sql_insert);
	}
	 
	 
	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
	die(json_encode($result));

 }elseif($jenis=='tsot'){//total stok opname pertanggal

		  $table="finishing";
		  
	 $filter='';
	 if(!empty($barcode)){
		$filter.=" AND k.kode_15 like '$barcode%'";			
	}
	
	if(!empty($txt_nama)){
		$filter.=" AND p.nama like '%$txt_nama%'";	
	}
	if($isShowHpp){
	   $field_hpp=' p.hargadasar ';	
	}else{
		$field_hpp='0';
	}
	$d=explode("-",$tgl);		
	$table_koreksi_stok='';
	if($d[0].'-'.$d[1]!=date('Y-m')){
		 $table_koreksi_stok='koreksi_'.$table.'_stok_'.$d[1].$d[0];  			 
	}else{
		 $table_koreksi_stok='koreksi_'.$table.'_stok';			
	}
	 $sql="SELECT   SUM(k.stok_awal) as stok_awal,SUM(  $field_hpp * k.stok_awal) stok_awal_hpp, SUM(p.hargajual * k.stok_awal) stok_awal_hpj ,  
	  SUM(k.stok_akhir) stok_akhir,SUM( $field_hpp * k.stok_akhir) stok_akhir_hpp, SUM(p.hargajual * k.stok_akhir)stok_akhir_hpj,
	  SUM(k.qty) koreksi,SUM(  $field_hpp * k.qty) koreksi_hpp, SUM(p.hargajual * k.qty)  koreksi_hpj
     FROM  ".$table_koreksi_stok." AS k LEFT JOIN produk AS p ON (p.kode = k.kode_15)
     WHERE pabrik='$gudang' AND k.tanggal LIKE '$tgl' $filter ;";
	 
	 
	 $hasil=mysql_query($sql);// or die($sql.' # '.mysql_error());
	 list($total_stok_awal,$total_hpp_awal,$total_hpj_awal,$total_so,$total_hpp_so,$total_hpj_so,$total_stok_akhir,$total_hpp_akhir,$total_hpj_akhir)=mysql_fetch_array($hasil);
	 //die($sql);
	 if($isShowHpp==1){//untuk kemudahan koding
	    	
	 }else{
		$total_hpp_awal=0;
		$total_hpp_so=0;
		$total_hpp_akhir=0;
	 }
	 die("$total_stok_awal#$total_hpp_awal#$total_hpj_awal#$total_so#$total_hpp_so#$total_hpj_so#$total_stok_akhir#$total_hpp_akhir#$total_hpj_akhir");


 }elseif($jenis=='buso'){// batal upload so
	 if($tp_gudang=='o'){
		  $table="outlet";
	 }elseif($tp_gudang=='m'){
	      $table="markas";
	 }elseif($tp_gudang=='g'){
	      $table="distribusi";	 
	 }
	
	$sql="SELECT pesan FROM config_system  WHERE kode_config='ltq'";
	$res=mysql_query($sql);
	list($location)=mysql_fetch_array($res);
	
	
	if(substr($nama_file,0,2)=='KR'){
		#SELECT tgl_koreksi,kode_outlet FROM koreksi_outlet_stok_validasi WHERE no_stok_opname
		$sql="SELECT DATE_FORMAT(tgl_koreksi,'%Y-%m-%d'),TRIM(kode_outlet) FROM koreksi_outlet_stok_validasi WHERE no_stok_opname='$nama_file';";
	   
	}else{
		$sql="SELECT DATE_FORMAT(tgl_koreksi,'%Y-%m-%d'),TRIM(kode_gudang) FROM temp_koreksi_".$table."_v2 where kode_temp='$nama_file'";

	}
	
		$res=mysql_query($sql);
	list($tgl,$kode_outlet)=mysql_fetch_array($res);
	
	if(substr($nama_file,0,2)=='KR'){
		 $sql="SELECT trim(kode_outlet),DATE_FORMAT(tgl_koreksi,'%Y-%m-%d') FROM koreksi_outlet_stok_validasi 
		     WHERE no_stok_opname='$nama_file'";
		$hasil=mysql_query($sql) or die('error# '.mysql_error());
		list($kode_outlet,$tgl_koreksi)=mysql_fetch_array($hasil);
		
		$sql="update temp_koreksi_outlet set approved='0' WHERE tgl_koreksi='$tgl_koreksi' AND kode_gudang='$kode_outlet' 
		      and kode_produk IN (SELECT TRIM(kode_15) FROM koreksi_outlet_stok WHERE id='$nama_file');";	 
		$hasil=mysql_query($sql) or die('error# '.mysql_error());
		
		
		 $sql="DELETE  FROM koreksi_outlet_stok_validasi WHERE no_stok_opname='$nama_file'";
		 $hasil=mysql_query($sql) or die('error# '.mysql_error());

         $sql="DELETE FROM koreksi_outlet_stok WHERE id='$nama_file'";
		 $hasil=mysql_query($sql) or die('error# '.mysql_error());
		 $cmd='nohup php '.$location.'/insert_data_rekap_stok_outlet_cli.php '."-a ".$tgl." -l ".$tgl." -k 1 -s 0 -g 0 -w $kode_outlet > /dev/null &";
	     exec($cmd);
	die("ok#$nama_file");
		 
	 }
	$cmd='nohup php '.$location.'/insert_data_rekap_stok_outlet_cli.php '."-a ".$tgl." -l ".$tgl." -k 1 -s 0 -g 0 -w $kode_outlet > /dev/null &";
	exec($cmd);  
	 
	 
	 
	 
	 $sql="DELETE  FROM temp_koreksi_".$table."_v2 where kode_temp='$nama_file'";
	 //die('tes#'.$sql);
	 $hasil=mysql_query($sql) or die('error# '.mysql_error());
	 
	 
	 die("ok#$nama_file");
	 
 }elseif($jenis=='nv'){//no validasi outlet
      $d=explode('-',$tgl);
	  $periode=$d[0].'-'.$d[1];
	  if($tp_gudang=='o'){
		$sql="SELECT COUNT(*) FROM koreksi_outlet_stok_validasi WHERE kode_outlet='$gudang' AND tgl_koreksi LIKE '$periode%' ";  
		$res=mysql_query($sql);
		list($counter)=mysql_fetch_array($res);
		
		
		  
	  }
	  $counter++;
	  switch($counter){
		case $counter <10 :
		  $counter='0000'.$counter;
		  break;
		case $counter <100 :
		  $counter='000'.$counter;
		  break;
		case $counter <1000 :
		  $counter='00'.$counter;
		  break;
		case $counter <10000 :
		  $counter='0'.$counter;
		  break;
		default :
		  $counter=$counter;
	  }
	  
	  $id="KR/$counter/$gudang/$d[1]/$d[0]";
	  die($id);
	  
 }elseif($jenis=='iso'){//Insert Stok Opname   
  
	$sql="REPLACE INTO `koreksi_outlet_stok_validasi` (`no_stok_opname`,`kode_outlet`,`nip_sm`,`nama_sm`, `input_by`,`tgl_koreksi`,`update_date`,user_input)
VALUES ('$id_koreksi', '$gudang','$nipSM','$nama_sm','$nipInputer','$tgl',NOW(),'$username');";  
	$res=mysql_query($sql)or die("ERROR".mysql_error());
	die("ok");
	
	  
 }elseif($jenis=='s_otl'){// search data koreksi outlet
	 $sql="SELECT trim(kode_produk),qty,hpj,trim(nama_produk),approved FROM temp_koreksi_outlet WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die($sql);
	 //die($sql);
	 $hasil='';
	 while(list($barcode,$qty,$hpj,$nama_produk,$approved)=mysql_fetch_array($res)){
		  $hasil.="$barcode#$qty#$hpj#$nama_produk#$approved^";
	 }
	 echo $hasil;
 }elseif($jenis=='ua_otl'){// unapproved Outlet
	 $sql="update temp_koreksi_outlet set approved='0' WHERE kode_gudang='$gudang' AND tgl_koreksi='$tgl'";
	 $res=mysql_query($sql) or die('Error # query #'. $sql);
	 die("ok");
 }elseif($jenis=='csabt'){// Control Stok awal berdasarkan tanggal khusus outlet
    	
	$dt=explode('-',$tgl);
	$date=$d[2];	 
	$periode=$dt[0].'-'.$dt[1];
	$thisMonthActive=date('Y-m');
	if($thisMonthActive==$periode){//jika tanggal akhir terpilih maka penjualan hanya sampai kemarin
		$monthTahun='';
	}else{//jika bulan tidak aktif maka penjualan full
		$monthTahun='_'.$dt[1].$dt[0];
	}
	 if($tp_gudang=='o'){
		  $table="outlet";
	 }elseif($tp_gudang=='m'){
	      $table="markas";
	 }elseif($tp_gudang=='g'){
	      $table="distribusi";	 
	 }
	 if($isShowHpp==1){//untuk kemudahan koding
	   $script_hpp='p.hargadasar';  	
	 }else{
		$script_hpp='0'; 
	 }
	 
	 $sql_tbhn='';	 
	 if(!empty($barcode)){
		  $sql_tbhn.=" AND p.kode like '$barcode%'";
     }
	 if(!empty($nama)){
		  $sql_tbhn.=" AND p.nama like '$nama%'";
     }
	 if(!empty($md)){
		  $sql_tbhn.=" AND p.wil_md = '$md'";
	 }
	 
	
	 if(!empty($lstbarcode)){
	   $d=explode(",",$lstbarcode);
	   $dt='';	
	   foreach($d as $val){
		   if(trim($val)!=''){
			   $dt.="p.kode like '$val%' OR ";
		  }
	   }
	   $dt=substr($dt,0,strlen($dt)-3);
	   $sql_tbhn.=" AND (  $dt  ) ";	
	}
	 
	 if(!empty($gudang)){
		 $sql_tbhn.=" AND o.id ='$gudang'";
	 }
	 if(!empty($markas)){
		  $sql_tbhn.=" AND o.id_markas like  '$markas%'";
	 }
	  $d=explode("-",$tgl);		
	

	
	  if($tgl==date('Y-m-d')){
	   //ambil stok didata perpagi	
	   $sql="SELECT  `s`.`kode_outlet` id ,SUM(s.stok) as stokawal,sum($script_hpp  * s.stok ) hpp, sum(`p`.`hargajual` *s.stok ) hpj  
    FROM `outlet_stok_perpagi` AS `s` INNER JOIN `produk` AS `p`   ON (`s`.`kode_produk` = `p`.`kode`)
    INNER JOIN `outlet` AS `o` ON (`s`.`kode_outlet` = `o`.`id`)
	where `o`.`type`=4 and `o`.`jenis` in (1,2) $sql_tbhn  group by o.id  ";
	} else{
		$field="r.stok_awal";
		if($date==1){
			
		}else{
			for($i=1;$i<=$date;$i++){
				$field.="r.si$i - r.ro$i - r.so$i + r.ri$i + r.k$i ";;
			}
		}
		
	     $sql="SELECT o.id id, sum( $field  ) stokawal,sum( $script_hpp  * ($field) ) hpp, sum(`p`.`hargajual` * ($field) ) hpj  
   
FROM `rekap_stok_outlet$monthTahun` AS `r` INNER JOIN `produk` AS `p`      ON (`r`.`barcode_15` = `p`.`kode`)
    INNER JOIN `outlet` AS `o`  ON (`r`.`kode_outlet` = `o`.`id`) where `o`.`type`=4 and `o`.`jenis` in (1,2) 
	and r.periode like '$periode%' $sql_tbhn group by o.id";
		
	}
	
	 
	 //die($sql);
	 if($username=='budi-it'){
			$sql_debug=mysql_escape_string($sql);
			$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis -tg $tp_gudang' , '$sql_debug');";
			mysql_query($sql_insert);
	}
	 
	 
	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
	die(json_encode($result));
 }elseif($jenis=='dapp'){// unapproved Outlet
	 $sql="select trim(nip_sm) AS nip,trim(nama_sm) as nama,trim(input_by) inputer from koreksi_outlet_stok_validasi WHERE no_stok_opname='$nama_file';";
	 $res=mysql_query($sql) or die('Error # query #'. $sql);
	$result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
	die(json_encode($result));


 }elseif($jenis=='sarkp'){// unapproved Outlet

	if($tp_gudang=='o'){
		$filter_pabrik = " AND pb.id_group=4 ";
	 }elseif($tp_gudang=='m'){
		$filter_pabrik = " AND pb.id_group=2 ";
	 }elseif($tp_gudang=='g'){
		$filter_pabrik = " AND pb.id_group=3 ";
	 }
	  	
	$dt=explode('-',$tgl);
	$hr=$dt[2];	 
	$periode=$dt[0].'-'.$dt[1];
	
	
	 $fields=' stok_awal  ';
	 if($hr==1){// jika naikin datanya tanggal 1 maka tidak perlu di ngurangin hari// stok awal=stok akhir
	   $hr=$hr;
		   
	 }else{
	   $hr=$hr-1;
	   for($i=1;$i<=$hr;$i++){
			$fields.="+ si$i -ro$i -so$i + ri$i + k$i ";
		}     
	 }
	
	 $table="manufaktur";

	 $sql_inner_group = ' LEFT JOIN pabrik AS pb on r.kode_manufaktur = pb.id ';


	 if($isShowHpp==1){//untuk kemudahan koding
	   $script_hpp='p.hargadasar';  	
	 }else{
		$script_hpp='0'; 
	 }
	 
	 $sql_tbhn='';	 
	 if(!empty($barcode)){
		  $sql_tbhn.=" AND p.kode like '$barcode%'";
     }
	 if(!empty($nama)){
		  $sql_tbhn.=" AND p.nama like '$nama%'";
     }
	 if(!empty($md)){
		  $sql_tbhn.=" AND p.wil_md = '$md'";
	 }
	 
	$tbhn=''; 
	if(date('Ym')!=$dt[0].$dt[1]){
		$tbhn='_'.$dt[1].$dt[0];//
	}

	$sql=" select SQL_CACHE '$tgl' as bulan,kode_".$table." as id, sum( $fields ) as stokawal, sum( ($fields) * $script_hpp ) as hpp, sum( ($fields) * p.hargajual ) as hpj, pb.id_group  from rekap_stok_".$table."
	       r INNER JOIN produk p ON r.barcode_15=p.kode $sql_inner_group where r.periode like '$periode%' $filter_pabrik group by r.kode_".$table." ";
	#die($sql);
	 if($username=='budi-it' || $username=='iwan-it'){
		$sql_debug=mysql_escape_string($sql);
		$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis ' , '$sql_debug');";
		mysql_query($sql_insert);
	 }	
	 
	 
	 $res=mysql_query($sql) or die('Error # query #'. $sql);
	$result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
	die(json_encode($result));
 }
 

/**/
