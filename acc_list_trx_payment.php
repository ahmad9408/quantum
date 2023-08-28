<?php
	 
	include_once "header.php";
	$sql="SELECT Administrator FROM t_groupmenu WHERE GroupID=$_SESSION[GroupID] AND  GroupName='Administrator'";
	$hsladmin=mysql_query($sql,$db);
	list($admin)=mysql_affected_rows($db);
	$administrator=false;
	$administrator=true;
	if ($admin>0){$administrator=true;}
	$bgcolor1="#f7f7f7";$bgcolor2="#ebebeb";$bgcolor=$bgcolor2;
?>
<!--html>
	<head>
		<title>TRANSAKSI PEMBAYARAN</title>
	</head>
	<body>
		<br><br><br-->
<script language="JavaScript">
	var detailsWindow;
	function showCalendar(textid){
	   detailsWindow = window.open("calendar.php?textid="+textid+"","calendar","width=250,height=250");
	   detailsWindow.focus();
	}
	function ShowRelasiList(textid){
		tipe=document.getElementById('tipe1').checked;
		if (tipe){tipe=1}else{tipe=2;}
		detailsWindow = window.open("acc_relasi_list.php?textid="+textid+"&tipe="+tipe,"Relasi","width=250,height=250");
		detailsWindow.focus();
	}
	function cektipe(){
		//alert('test');
		try{
		document.getElementById('relasihidden').value='';
		document.getElementById('relasi').value='';
		}catch (err){
			//alert(err.description);
		}
	}
	
</script>
<br><br><br>
<fieldset>
	<legend><b>Filter</b></legend>
	<form name="formfilter" method="post" action="acc_list_trx_payment.php">
		<table>
		<tr><td>Relasi</td><td>:</td><td>
			<?php
				$checked=(isset($_POST['tipe'])?$_POST['tipe']:1);
			?>
			<label for="tipe1">Suplier</label>
				<input type="radio" id="tipe1" name="tipe" <?php echo ($checked==1)?'checked="checked"':''; ?> value="1" onClick="cektipe();" />
			<label for="tipe2">Customer</label>						
				<input type="radio" id="tipe2" name="tipe" <?php echo ($checked==2)?'checked="checked"':''; ?> value="2" onClick="cektipe();" />
			<input type="text" id="relasi" name="relasi" value="<?php echo $_POST['relasi']; ?>" readonly onClick="return ShowRelasiList(this.id)"/>
			<input type="hidden" id="relasihidden" name="relasiid" value="<?php echo $_POST['relasiid']; ?>"/>
		</td></tr>
		<tr><td>Tgl Transaksi</td><td>:</td><td>
			<?php
				$tanggal="";
				if($_POST[tgltrx]!=""){$tanggal=$_POST[tgltrx];}else{$tanggal=date("Y-m-d");}
			?>
			antara <input id="tgltrx" type="text" name="tgltrx" value="<?php echo $tanggal; ?>" size="13" readonly onClick="return showCalendar(this.id)">
			<?php
				$tanggal="";
				if($_POST[tgltrxe]!=""){$tanggal=$_POST[tgltrxe];}else{$tanggal=date("Y-m-d");}
			?>
			dan <input id="tgltrxe" type="text" name="tgltrxe" value="<?php echo $tanggal; ?>" size="13" readonly onClick="return showCalendar(this.id)">
			
		</td></tr>				
		<tr><td colspan="3">
		<input type="submit" name="filtereddata" value="Show Data">
		<? #echo '<pre>';print_r($_POST);echo '</pre>'; ?>
		</td></tr>
		</table>
	</form>
</fieldset>

<fieldset>
	<legend><b>LIST TRANSAKSI PEMBAYARAN</b></legend>
	<br>
	<table>
		<form method="post" action="acc_new_trx_payment.php">
			<tr>
				<td>
					<select name="idtrx">
						<option value=''>-- Pilih --</option>
						<?php
							$sql="SELECT * FROM acc_template_trx ORDER BY nama";
							$hsltemp=mysql_query($sql,$db);
							while($rstemp=mysql_fetch_array($hsltemp)){
						?>
							<option value='<?php echo $rstemp[id]; ?>'><?php echo $rstemp[nama]."[".$rstemp[id]."]"; ?></option>										
						<?php
							}
						?>
					</select>
				</td>
				<!--td>
					Slip: <input type="text" name="slip">
				</td-->
				<td>
					<input type="submit" name="newtrx" value="New Transaction">
				</td>
			</tr>
		</form>
	</table>
	<?php
		if (isset($_POST['filtereddata'])){
	?>
	<table border="1">
		<tr>
			<td bgcolor=<?php echo $bgcolor; ?>>No</td>
			<td bgcolor=<?php echo $bgcolor; ?>>Slip</td>
			<td bgcolor=<?php echo $bgcolor; ?>>Nama Transaksi</td>
			<td bgcolor=<?php echo $bgcolor; ?>>Tgl Transaksi</td>
			<td bgcolor=<?php echo $bgcolor; ?>>Tanggal Entry</td>
			<?php
				if ($administrator){
			?>
				<td bgcolor=<?php echo $bgcolor; ?>>Username</td>						
			<?php
				}
			?>
		</tr>
		<?php
			#$arrtglentry=explode("/",$_POST[tgltrx]);
			$tgltrx=$_POST[tgltrx];#$arrtglentry[2]."-".$arrtglentry[1]."-".$arrtglentry[0];
			#$arrtglentry=explode("/",$_POST[tgltrxe]);
			$tgltrxe=$_POST[tgltrxe];#$arrtglentry[2]."-".$arrtglentry[1]."-".$arrtglentry[0];
					
			if ($administrator){
				$sql="SELECT a.*, date_format(b.trxdate,'%d/%m/%Y') trxdate  
					FROM acc_transaksi a inner join acc_transaksi_payment b on a.id=b.id and a.slip=b.slip and a.tipetrx='4'
					". ( ($_POST['relasiid']!='')?('and b.relasi_id="'.$_POST['relasiid'].'"'):'' ) . 
					" and b.trxdate between '". $tgltrx ."' and '". $tgltrxe ."'
					ORDER BY a.tglentry DESC";
			}else{
				$sql="SELECT a.*, date_format(b.trxdate,'%d/%m/%Y') trxdate 
					FROM acc_transaksi a inner join acc_transaksi_payment b on a.id=b.id and a.slip=b.slip and a.tipetrx='4' 
					". ( ($_POST['relasiid']!='')?('and b.relasi_id="'.$_POST['relasiid'].'"'):'' ) . 
					" and b.trxdate between '". $tgltrx ."' and '". $tgltrxe ."'
					WHERE uid='$_SESSION[IDKaryawan]' ORDER BY a.tglentry DESC";
			}
			$hslreport=mysql_query($sql,$db);
			$no=0;
			if ($bgcolor==$bgcolor1){$bgcolor=$bgcolor2;}else{$bgcolor=$bgcolor1;}
			while ($rsreport=mysql_fetch_array($hslreport)){
				$no++;
		?>
				<tr>
					<td bgcolor=<?php echo $bgcolor; ?>><b><?php echo $no; ?></b></td>
					<td bgcolor=<?php echo $bgcolor; ?>><a href="acc_trx_show_payment.php?id=<?php echo $rsreport[id]; ?>&slip=<?php echo $rsreport[slip]; ?>"><?php echo $rsreport[slip]; ?></a></td>
					<td bgcolor=<?php echo $bgcolor; ?>><a href="acc_trx_show_payment.php?id=<?php echo $rsreport[id]; ?>&slip=<?php echo $rsreport[slip]; ?>"><?php echo $rsreport[nama]; ?></a></td>
					<td bgcolor=<?php echo $bgcolor; ?>><?php echo $rsreport[trxdate]; ?></td>
					<td bgcolor=<?php echo $bgcolor; ?>><?php echo $rsreport[tglentry]; ?></td>
					<?php
						if ($administrator){
							$sql="SELECT UserName FROM mst_userbo WHERE idkaryawan='$_SESSION[IDKaryawan]'";
							$hsluser=mysql_query($sql,$db);
							list($username)=mysql_fetch_array($hsluser);
					?>
					<td bgcolor=<?php echo $bgcolor; ?>><?php echo "[".$rsreport[uid]."] ".$username; ?></td>
					<?php
						}
					?>
				</tr>
		<?php
				if ($bgcolor==$bgcolor1){$bgcolor=$bgcolor2;}else{$bgcolor=$bgcolor1;}
			}
		?>
	</table>
	<?php
		}#end filtered data
	?>
</fieldset>
	<!--/body>
</html--> 
<?php include_once "footer.php";
#echo '<pre>';print_r($_POST);echo '</pre>';
?>