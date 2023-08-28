function loadAllToKoreksi() {
	var ans = confirm('Anda yakin akan meload ulang data hasil upload ke koreksi ?');
	var tipe = 'o';
	var gudang = $('#pabrik').val();
	var tgl = $('#tgl1').val();
	if (ans == true) {
		$("[id='proc']").text("Process loading  data to koreksi....");
		$.ajax({
			url: "monitoring_koreksi_data.php",
			type: "POST",
			cache: false,
			dataType: 'text',
			data: { t: tgl, j: 'sok', tg: tipe, g: gudang },// jenis distribusi
			success: function (data) { //alert(data);
				//alert(data);
				var d = data.split('#');
				if (d[0] == 'ok') {

				} else {
					alert(d[1]);
				}
				$("[id='proc']").text("loading  data to koreksi finish");
			}	//end succes			
		});
	}

}

function showDetail(kodefile) {
	$('#kode_temp').val(kodefile);
	$('#formDetail').submit();

}

function deleteDataSo(kodefile) {
	var ans = confirm('Anda yakin akan menghapus data so dengan nama file ' + kodefile);
	if (ans == true) {
		$('#procReshare').text("Process get Data....");
		$.ajax({
			url: "laporan_so_suho_rekap_data.php",
			type: "POST",
			cache: false,
			dataType: 'text',
			data: { kf: kodefile, j: 'buso', tg: 'o' },// jenis distribusi
			success: function (data) { //alert(data);
				//alert(data);
				var d = data.split('#');
				if (d[0] == 'ok') {
					//alert('Data dengan nama file ' + d[1] +' Berhasil dibatalkan');
					$('[id="' + d[1] + '"]').remove();
				}


			}	//end succes			
		});

	}

}
$(document).ready(function () {
	hitungTotal();
	$('#pabrik').chosen({});
	$('.hpp').hide();
	$('.header').attr('colspan', 2);

	if (isShowHpp == 1) {
		$('.hpp').show();
		$('.header').attr('colspan', 3);
	}

	$('#showSql').toggle(function () {
		$('#dbgSQL').show();
		$('#dataOmset').show();

	}, function () {
		$('#dbgSQL').hide();
		$('#dataOmset').hide();

	})

	//formupload
	$('#fupload').change(function () {
		//alert('test');
		$('#form-upload').submit();
		console.log('try submit #form-upload');
		//$('#form-test').submit();	
	});
	$('#form-upload').iframePostForm({
		post: function () {
			//$('#uploading').modal();
			console.log('#form-upload  upload...');
			var gdg = $('#pabrik').val();
			//$('#pr_'+gdg).text('Uploading ...');
			$("[id='pr_" + gdg + "']").text('Uploading ...');

		},

		complete: function (result) {
			//$.modal.close();
			console.log('#complete...');
			var d = result.split('#');
			var gdg = $('#pabrik').val();
			if (d[0] == 'ok') {
				//$('#pr_'+gdg).text(d[1]);
				//$('#pr_'+gdg).html('<a href="bukti_koreksi/'+d[1]+'" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a>');
				$("[id='pr_" + gdg + "']").html('<a href="bukti_koreksi/' + d[1] + '" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a>');
			} else {
				/*$('#r_'+d[2]).html(d[1]+'<br/>'+ d[4]);*/
				//$('#pr_'+gdg).html('ERROR '+ d[4]);
				$("[id='pr_" + gdg + "']").html('ERROR ' + d[4]);
				//alert(d[4]);
				//$('#r_'+count).text('');
			}
			console.log('result  ' + result);
			//					$("#up-result_"+ count ).html(result);
		}
	});

	$('#cb_hpp').change(function () {

		if ($('#cb_hpp').is(':checked')) {
			$('.body_hpp').show();
			$('.head_hpp').show();
			$('.head_dt').attr('colspan', 3);
		} else {
			$('.body_hpp').hide();
			$('.head_hpp').hide();
			$('.head_dt').attr('colspan', 2);
		}

	})
	$('.head_dt').attr('colspan', 2);
	$('.body_hpp').hide();
	$('.head_hpp').hide();

})
function hitungTotal() {
	var tgl = $('#tgl1').val();
	var gudang = $('#pabrik').val();
	var barcode = $('#barcode').val();
	var nama = $.trim($('#txt_nama').val());
	//alert(gudang);
	$('#procReshare').text("Process get Data....");
	$.ajax({
		url: "laporan_so_suho_rekap_data.php",
		type: "POST",
		cache: false,
		dataType: 'text',
		data: { t: tgl, j: 'tsot', tg: 'o', b: barcode, g: gudang, n: nama },// jenis distribusi
		success: function (data) { //alert(data);
			//alert(data);
			var d = data.split('#');
			$('#totalSa').text(format('#,##0.#', d[0]));
			$('#totalHppSa').text(format('#,##0.#', d[1]));
			$('#totalHpjSa').text(format('#,##0.#', d[2]));
			$('#totalSo').text(format('#,##0.#', d[3]));
			$('#totalHppSo').text(format('#,##0.#', d[4]));
			$('#totalHpjSo').text(format('#,##0.#', d[5]));
			$('#totalSk').text(format('#,##0.#', d[6]));
			$('#totalHppSk').text(format('#,##0.#', d[7]));
			$('#totalHpjSk').text(format('#,##0.#', d[8]));



		}	//end succes			
	});
}
function uploadPernyataan(id_gudang, tanggal, jenis) {
	$('#v_gudang').val(id_gudang);
	$('#v_tgl').val(tanggal);
	$('#v_jenis_gudang').val(jenis);
	$('#fupload').click();

}