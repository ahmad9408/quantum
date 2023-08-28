<?php $content_title="APPROVING DO"; include_once "header.php" ?>
<?php
	include_once "do_produk_init.php";
	//echo nomor_manufaktur();exit;
	$no_do=sanitasi($_GET["no_do"]);
	$sql="SELECT approve FROM do_produk WHERE no_do='$no_do'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)<1){ ?> <script language="javascript">history.back();</script> <?php exit();}
	list($approve)=mysql_fetch_array($hsl);
	if(!$approve){
		/*Buar Jurnal by suro*/
		#include_once('acc_jurnal_function.php');
		/*Buar Jurnal by suro*/
		$keterangan=sanitasi($_POST["keterangan"]);
		$approveby=$_SESSION["username"];
		$sql="UPDATE do_produk SET keterangan='$keterangan',approve='1', approveby='$approveby',approvedate=NOW() WHERE no_do='$no_do'";
		// echo "<br>".$sql;
		mysql_query($sql,$db);
		if(mysql_affected_rows($db)>0){
			?>
				<script language="javascript">
					alert("DO PRODUK telah di approve.");
					window.location="do_produk_listv4.php?no_do=<?php echo $no_do; ?>";
				</script>
			<?php
		}else{
			?>
				<script language="javascript">
					alert("DO PRODUK gagal di approve, Silakan hubungi Technical Support Anda!");
					window.location="do_produk_listv4.php?no_do=<?php echo $no_do; ?>";
				</script>
			<?php
		}
	}else{
		?>
			<script language="javascript">
				alert("DO PRODUK sudah di approve.");
				window.location="do_produk_listv4.php?no_do=<?php echo $no_do; ?>";
			</script>
		<?php
	}
?>
<?php include_once "footer.php" ?>