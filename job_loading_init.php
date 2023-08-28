<?php
	function no_load_pindah ($no_load) {
		global $db;
		$sql="SELECT format,digit FROM format_nomor WHERE id=9";
		$hsl=mysql_query($sql,$db);
		list($format,$digit)=mysql_fetch_array($hsl);
		$arrtemp=explode("-",$format);
		$pos_serial=count($arrtemp);
		$jumlahdashformat=$pos_serial-1;
		$arrtemp=explode("-",$no_load);
		$jumlahdash_nosew=count($arrtemp)-1;
		if($jumlahdashformat==$jumlahdash_nosew){//belum ada serial tambahan
			$no_load=$no_load."-01";
		}
		if($jumlahdashformat+1==$jumlahdash_nosew){//sudah ada serial tambahan
			$serialterakhir=$arrtemp[count($arrtemp)-1];
			$serial=($serialterakhir*1)+1;
			$no_load="";
			for($i=0;$i<count($arrtemp)-1;$i++){
				$no_load.=$arrtemp[$i]."-";
			}
			$no_load.=substr("00",0,2-strlen($serial)).$serial;
		}
		return $no_load;
	}
	function no_sew_pending ($no_sew) {
		global $db;
		$sql="SELECT format,digit FROM format_nomor WHERE id=10";
		$hsl=mysql_query($sql,$db);
		list($format,$digit)=mysql_fetch_array($hsl);
		$arrtemp=explode("-",$format);
		$pos_serial=count($arrtemp);
		$jumlahdashformat=$pos_serial-1;
		$arrtemp=explode("-",$no_sew);
		$jumlahdash_nosew=count($arrtemp)-1;
		if($jumlahdashformat==$jumlahdash_nosew){//belum ada serial tambahan
			$no_sew=$no_sew."-01";
		}
		if($jumlahdashformat+1==$jumlahdash_nosew){//sudah ada serial tambahan
			$serialterakhir=$arrtemp[count($arrtemp)-1];
			$serial=($serialterakhir*1)+1;
			$no_sew="";
			for($i=0;$i<count($arrtemp)-1;$i++){
				$no_sew.=$arrtemp[$i]."-";
			}
			$no_sew.=substr("00",0,2-strlen($serial)).$serial;
		}
		return $no_sew;
	}
	function no_sew () {
		global $db;
		$sql="SELECT format,digit FROM format_nomor WHERE id=10";
		$hsl=mysql_query($sql,$db);
		list($format,$digit)=mysql_fetch_array($hsl);
		$arrformat=explode('$_seq',$format);
		$_tahun=date("Y");
		
		$sql="SELECT end FROM set_periode WHERE id=1";
		$hsl=mysql_query($sql,$db);
		list($periode_end)=mysql_fetch_array($hsl);
		if($periode_end<=date("d")*1){
			$_blnawal=date("M",mktime(0,0,0,date("m")));
			$_blnakhir=date("M",mktime(0,0,0,date("m")+1));
		}else{
			$_blnawal=date("M",mktime(0,0,0,date("m")-1));
			$_blnakhir=date("M",mktime(0,0,0,date("m")));
		}
		$like="";
		$like2="";
		
		foreach($arrformat as $key => $value){
			if($key==0){
				$like=$value;
				$like2=$value;
				$po_no=$value;
			}
			if($key>0){
				$temp="";
				eval("\$temp = \"$value\";");
				$like.="%".$temp;//UNTUK PENCARIAN DI TABLE APAKAH SUDAH ADA
				$like2.=$temp;
				$po_no.='$_seq'.$temp;//UNTUK PROSES EVAL
			}
		}
		$looping=true;
		$nol="";
		for($i=0;$i<$digit;$i++){
			$nol.="0";
		}
		$i=0;
		while ($looping){//MENCARI NO DENGAN MENGURUT DARI 1 S/D TAK TERHINGGA
			$i++;
			$_seq=substr($nol,0,$digit-strlen($i)).$i;
			eval("\$po_no_temp = \"$po_no\";");
			$sql="SELECT no_sew FROM job_sewing WHERE no_sew = '$po_no_temp'";
			$hsl=mysql_query($sql,$db);
			if(mysql_affected_rows($db)<=0){$looping=false;}
		}
		eval("\$po_no_temp = \"$po_no\";");
		$po_no=strtoupper($po_no_temp);
         
		return $po_no;
	}
?>