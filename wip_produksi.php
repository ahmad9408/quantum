<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP PRODUKSI"; 
include('header.php'); ?>
<table width="1200" border="0">
    <tr bgcolor="#99CC00">
    <td width="140" rowspan="2"><strong>Kode Model </strong></td>
    <td width="146" rowspan="2"><strong>Nama Model </strong></td>
    <td width="180" rowspan="2"><strong>No Co </strong></td>
    <td width="126" rowspan="2"><strong>Kode PPIC </strong></td>
    <td width="135" rowspan="2"><strong>Tanggal CO </strong></td>
    <td width="90" rowspan="2"><strong>Qty CO </strong></td>
    <td width="71" rowspan="2"><strong>Pabrik</strong></td>
    <td width="87" rowspan="2"><strong>Loading</strong></td>
    <td colspan="2"><div align="center"><strong>Sewing</strong></div></td>
    <td colspan="2"><div align="center"><strong>QC</strong></div></td>
    <td width="39" rowspan="2"><div align="center"><strong>DO</strong></div></td>
    <td width="55" rowspan="2"><div align="center"><strong>WIP</strong></div></td>
  </tr>
  <tr>
    <td width="68"><div align="center"><strong>Bagus</strong></div></td>
    <td width="44"><div align="center"><strong>Reject</strong></div></td>
    <td width="36"><div align="center"><strong>A</strong></div></td>
    <td width="29"><div align="center"><strong>B</strong></div></td>
  </tr>
    <?php $sql="SELECT
	m.kode_model
    ,`m`.`nama_model`
   
FROM
    `job_gelaran` AS `jg`
    INNER JOIN  `job_gelaran_detail` AS `jgd` 
        ON (`jg`.`no_co` = `jgd`.`no_co`)
    INNER JOIN `mst_model_fix` AS `m`
        ON (SUBSTRING(`jgd`.`kd_produk`,1,7) = `m`.`kode_model`) 
		where jg.approvedate like '%2015%' 
		group by m.kode_model,jg.no_co order by jg.no_co desc 
        limit 100";
	$query=mysql_query($sql)or die($sql);
	while(list($kode_model,$nama_model)=mysql_fetch_array($query)){	 
		 $no++;  
          $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2; 
         
		     $sql="SELECT COUNT(jumlah) FROM (SELECT   count(`jg`.`no_co`) AS jumlah
					, `jg`.`kode_ppic`  AS kode_ppic
				FROM
					`job_gelaran` AS `jg`
					INNER JOIN  `job_gelaran_detail` AS `jgd` 
						ON (`jg`.`no_co` = `jgd`.`no_co`)
					INNER JOIN `mst_model_fix` AS `m`
						ON (SUBSTRING(`jgd`.`kd_produk`,1,7) = `m`.`kode_model`) 
						WHERE  jg.approvedate LIKE '%2015%'  AND m.kode_model='$kode_model' GROUP BY jg.no_co
						) AS tabel"; 
					 
						$res=mysql_query($sql)or die($sql);
						list($jumlah)=mysql_fetch_array($res);
						$ni=0;
						
						  $sql="SELECT   `jg`.`no_co` AS no_co
					, `jg`.`kode_ppic`  AS kode_ppic,sum(jgd.qty_produk)
				FROM
					`job_gelaran` AS `jg`
					INNER JOIN  `job_gelaran_detail` AS `jgd` 
						ON (`jg`.`no_co` = `jgd`.`no_co`)
					INNER JOIN `mst_model_fix` AS `m`
						ON (SUBSTRING(`jgd`.`kd_produk`,1,7) = `m`.`kode_model`) 
						WHERE  jg.approvedate LIKE '%2015%'  AND m.kode_model='$kode_model' GROUP BY jg.no_co
						";
						$res=mysql_query($sql)or die($sql);
						while(list($no_co,$kode_ppic,$qty)=mysql_fetch_array($res)){
						$i++;
						$ni++;  
          ?>
          
          <tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>" class="baris<?php echo $no?>">
		  <?php if($ni==1){?>
    <td  height="25" rowspan="<?php echo $jumlah?>" ><?php echo $kode_model;?>|<?php echo $jumlah?></td>
    <td rowspan="<?php echo $jumlah?>" ><?php echo $nama_model;?></td> <?php }?>
    <td><?php echo $no_co?></td>
    <td><?php echo $kode_ppic?></td>
    <td>&nbsp;</td>
    <td align="center"><?php echo number_format($qty,"0",".",",");?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
	<?php   if($ni==1){?> 
    <td rowspan="<?php echo $jumlah?>">&nbsp;</td>
    <td rowspan="<?php echo $jumlah?>">&nbsp;</td> <?php }?>
  </tr>
  <?php 
  	}
  ?>
    
  <?php }?>
</table>

	
<?php include_once "footer.php" ?>
