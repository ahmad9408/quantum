<?php 
$content_title="DATA DETAIL"; 
include_once "header.php";

$tgl1=sanitasi($_GET['t1']);
$tgl2=sanitasi($_GET['t2']);
$model=sanitasi($_GET['m']);
$nama=sanitasi($_GET['nm']);
$jenis=sanitasi($_GET['j']);

if($jenis=='p'){ //Po
   $sql="SELECT p.no_manufaktur,date_format(p.tanggal,'%d %M %Y'),SUM(pd.qty),pd.kd_produk  FROM po_manufaktur AS p 
		INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur) 
		LEFT JOIN produk AS i ON (i.kode = pd.kd_produk) 
		WHERE p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND pd.kd_produk LIKE '$model%' 
		AND p.closeco IS NULL group by date_format(p.tanggal,'%d %M %Y'),i.kode_basic_item,i.kode_kategori,i.kode_kelas,i.kode_style,i.kode_model";
   $judul_field='PO';
   $judul_set="Model [$model] $nama";
}elseif($jenis=='d'){ //DO
   $sql="SELECT    d.no_do,DATE_FORMAT(d.tanggal,'%d %M %Y'),sum(dd.qty) FROM do_produk_detail AS dd 
		INNER JOIN do_produk AS d ON (dd.no_do = d.no_do) 
		LEFT JOIN produk AS i ON (i.kode = dd.kd_produk)
		WHERE d.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND dd.kd_produk LIKE '$model%' AND d.no_do NOT LIKE 'BTL%' 
		group by date_format(d.tanggal,'%d %M %Y'),i.kode_basic_item,i.kode_kategori,i.kode_kelas,i.kode_style,i.kode_model order by d.tanggal;";
		
		
   /*dirubah tanggal 9 april 2012*/
   $sql="SELECT k.no_do,DATE_FORMAT(k.tanggal,'%d %M %Y'),SUM(IFNULL(k.jumlah_kirim,0)) AS jumlah_kirim FROM 
    (SELECT pd.kd_produk, SUM(pd.qty) AS 	jumlah FROM po_manufaktur_detail AS pd
    INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur)      
    WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND p.closeco IS NULL AND pd.kd_produk LIKE '$model%'
    GROUP BY pd.kd_produk) AS a LEFT JOIN (SELECT dd.kd_produk, SUM(dd.qty) AS jumlah_kirim,d.tanggal,d.no_do FROM
    do_produk_detail AS dd
    INNER JOIN do_produk AS d 
        ON (dd.no_do = d.no_do) WHERE  d.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND dd.kd_produk LIKE '$model%'
        AND  d.no_do NOT LIKE 'BTL%' AND dd.kd_produk LIKE '%' GROUP BY dd.kd_produk) AS k  ON k.kd_produk=a.kd_produk
		where k.jumlah_kirim>0
        GROUP BY k.no_do ";
		
   $judul_field='DO';
   $judul_set="Model [$model] $nama";
}

 ?>
 <script type="text/javascript" src="sortable.js"></script> 
<fieldset>
 <legend><?=$judul_set?></legend>
 
<table border="1" width="97%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" class="sortable">
    <tr>
        <td align="center" width="80" bgcolor="#99CC00" height="24"><b>No</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="598"><b>No <?=$judul_field?></b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="203"><b>TGL</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="292"><b>Qty</b></td>
    </tr>
		<?php
			
			
			
			//echo $sql;
			
			
			$hsl=mysql_query($sql);
			$total_qty=0;
			
			$no=0;
			while(list($no_trans,$tgl,$qty,$kd_produk)=mysql_fetch_array($hsl)){
				$no++;
          		$bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
				$total_qty +=$qty;
          		// $bgclr1 = ""; $bgclr2 = "";
          
          		$bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;      	
		?>
        <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
        <td align="center" width="80" height="20"><?php echo $no; ?></td>
        <td align="left" height="20" width="598">&nbsp;<?php echo $no_trans; ?></td>
        <td align="left" height="20" width="203">&nbsp;<?php echo $tgl; ?></td>
        <td align="right" height="20" width="292">&nbsp;<?php echo number_format($qty); ?></td>
    </tr>
       
		<?php
			}
		?>
        <tfoot>
<tr  bgcolor="#99CC00">
          <td align="center" height="20">&nbsp;</td>
          <td align="left" height="20">&nbsp;</td>
          <td align="left" height="20">Total</td>
          <td align="right" height="20"><?php echo number_format($total_qty); ?></td>
        </tr>
        </tfoot>
   </table>
</fieldset>
 <br /><br />
<?php include_once "footer.php" ?>
