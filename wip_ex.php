 <?php $content_title="WIP PEMENUHAN"; include_once "header.php";

 include("css_group.php"); 
   ?> 
<table border="1" class="table table-hover table-striped">
    <tr class="bg-header">
        <td>NO</td>    
        <td>NO Transaksi</td>
        <td>Kode Customer</td> 
        <td>Nama Customer</td> 
        <td>Outlet Asal</td> 
        <td>Tanggal</td> 
        <td>Model</td> 
        <td>Qty</td>  
        <td>Nilai Qty</td>     
        <td>Jual</td>   
        <td>Nilai Jual</td>
        <td>WIP</td>
        <td>Nilai WIP</td>
        <td>Prosentase</td>        
    </tr>  
    <?php $sql 	= "SELECT SQL_CALC_FOUND_ROWS SQL_CACHE
				 TRIM(substring(p.kode,12,20)), 
				  TRIM(p.customer),
				  c.nama,
				 DATE_FORMAT(p.tanggal,'%Y-%m-%d %T') AS tgl,
				 pr.nama,
				 SUM(topd.qty) AS qty , 
				 total_amount,o.nama 
				 FROM trx_order_pos p  
				 INNER JOIN outlet o ON o.id=p.outlet  
				 INNER JOIN trx_order_pos_detail AS topd ON 
				 (topd.kode=p.kode)
				 INNER JOIN produk AS pr ON 
				 (pr.kode=topd.barcode)
				 INNER JOIN customer AS c ON 
				 (c.id=TRIM(p.customer))
				 left join trx_pos_dan_order as tpdo on 
				 (tpdo.kode_order=p.kode)
				 WHERE p.tanggal 
				 BETWEEN '2018-12-01 00:00:00' AND '2018-12-31 23:59:59' 
				 AND p.kode NOT LIKE '%K%' 
				 AND p.kode NOT LIKE 'BTL%'
				  AND ( o.id LIKE '%' ) 
				AND o.id LIKE '%%'  GROUP BY p.kode
				ORDER BY p.kode ASC LIMIT 0,200 ";
 	
		$query 	= mysql_query($sql)or die($sql);
		while(list($kode_transaksi,$kode_customer,$nama_customer,$tanggal,$model,$qty,$nilai_po,$outlet_asal) = mysql_fetch_array($query)){
			$no++;
			?>
			<tr>
				<td><?php echo $no ?></td>
				<td><?php echo $kode_transaksi ?></td>		
				<td><?php echo $kode_customer ?></td>
				<td><?php echo $nama_customer ?></td>
				<td><?php echo $outlet_asal ?></td>
				<td><?php echo $tanggal ?></td>
				<td><?php echo $model ?></td>
				<td class="text-center" id="po<?php echo $kode_transaksi?>"><?php echo $qty ?></td>
				<td class="text-right" id="nilaipo<?php echo $kode_transaksi?>"><?php echo number_format($nilai_po,"0",".",","); ?></td>
				<td class="bg-danger" id="qty<?php echo $kode_transaksi?>">0</td>
				<td class="bg-danger" id="nilai<?php echo $kode_transaksi?>">0</td> 
				<td class="text-center" id="selisih<?php echo $kode_transaksi?>"><?php echo $qty ?></td>
				<td class="text-right" id="nilaiselisih<?php echo $kode_transaksi?>"><?php echo number_format($nilai_po,"0",".",",")?></td>
				<td class="text-center" id="prosentase<?php echo $kode_transaksi?>">0%</td>
			</tr>
			<?php

		}

?>
</table>
 
  
    <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };


 function getCekPenjualan(){
 	var proses 		= "ambil_penjualan";
	try{
			  $.ajax({
			  type: 'POST',
			  url: 'wip_ex_proses.php',
			  data: {proses:proses},
			  dataType: 'json',
			  success: function(data){  
				 
				$.each(data, function(key, val) 
             	{
             		$("#qty"+val.kode).html(Number(val.qty).formatMoney(0, '.', ','));
             		$("#nilai"+val.kode).html(Number(val.nilai).formatMoney(0, '.', ','));

             		var vqty_po 	= Number($("#po"+val.kode).text().replace(/,/g, ''), 10);
             		var vnilai_po 	= Number($("#nilaipo"+val.kode).text().replace(/,/g, ''), 10);
             		var vsisa 		= vqty_po-val.qty;
             		var vnilaisisa 	= vnilai_po-val.nilai;

             		$("#selisih"+val.kode).html(Number(vsisa).formatMoney(0, '.', ','));
             		$("#nilaiselisih"+val.kode).html(Number(vnilaisisa).formatMoney(0, '.', ','));

             		vprosentase 	= (vqty_po/val.qty)*100;
             		alert(vprosentase);
             		 
             	 	 
            	});	
				 
			  }
			 	
			});	
			   
	
			}catch(err){alert(err.message);}
}

getCekPenjualan();

</script>
  
 
  
<?php 
//include("js_group.php");
include_once "footer.php" ?> 
</script> 