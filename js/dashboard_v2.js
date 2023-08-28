 var optionsChar;
 var optionsCharKelas;
 $(document).ready(function() {
	 
	       
		   //return;
	 $('#container_detail').html("");
            optionsChar = {
                chart: {
                    renderTo: 'container',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: 'Penjualan Produk Basic Item di Outlet Rabbani' 
                },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.point.name +' Nilai '+ this.point.y +' from ' + this.total
						  +'</b>: '+ format("#,##0.#0",this.percentage) +' %' ;
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
						size:250,
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>'+ this.point.name +'</b>: '+ format("#,##0.#0",this.percentage) +' %';
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [],
					 point: {
						events: {
							click: function(e) {
								//this.slice();
								//console.log(e);
								//location.href = e.point.url; redirece
								//e.preventDefault();//redirec
								//alert(' poin name '+e.point.name + ', nilai y ' + e.point.y);//alert disini
								try{
									var kodeBasic=e.point.name.substr(1,1);
									//alert('kode Basic ' + kodeBasic);
								     loadDetail(kodeBasic,e.point.name);
								}catch(e){
									alert('error ' + e.message);
								}
							}
						}//end events
					}//end point
				  
                }]
				
				
            }
           
		    /* 
            $.getJSON("dashboard_model_data.php", function(json) {
                optionsChar.series[0].data = json;
                chart = new Highcharts.Chart(optionsChar);
            });
           */
		  getDataJualPermodel();
            generatePiePerkelas();
		   loadDetail('K');
           
        });
		
function generatePiePerkelas(nama){
	 
	optionsCharKelas = {
                chart: {
                    renderTo: 'container_detail',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: 'Penjualan Produk Kelas '+ nama +' Outlet'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.point.name +' Nilai '+ this.point.y +' from ' + this.total 
						 +'</b>: '+ format("#,##0.#0",this.percentage) +' %' ;
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
						size: 250,
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
							width: '50px',
                            formatter: function() {
                                return '<b>'+ this.point.name +'</b>: '+ format("#,##0.#0",this.percentage) +' %';
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [],
					 point: {
						events: {
							click: function(e) {
								//this.slice();
								//console.log(e);
								//location.href = e.point.url; redirece
								//e.preventDefault();//redirec
								//alert(e.point.name + ' ' + e.point.y);//alert disini
								
								
								
							}
						}//end events
					}//end point
				  
                }]
				
				
            }
	
}

  
function getDataJualPermodel(){
	//jual
	
	//$('#container').text("Loading......");
	$('#btnSearch').val("Loading");
	$('#btnSearch').attr("disabled",true);
	$('#container').html('<div align="center" style="margin-top:80;"><img name="load" src="ajax-loader.gif" width="100" height="100" alt="" /><br><h2>GET DATA ....</h2></div>');
	$('#container_detail').html("");
	 var tgl1=$('#tgl1').val();
	 var tgl2=$('#tgl2').val();
	 var tipe=$('#tipe').val();
	 var outlet=$('#outlet').val();
	  var markas=$('#markas').val();
	  $.ajax({
			url:"dashboard_model_data.php", 
			type:"POST",
			cache: false,
			dataType:'json',
			data:{j:'gsm',t1:tgl1,t2:tgl2,o:outlet,t:tipe,m:markas},
			success: function(data) {
				 
				//$('#container').text(data);return;
				try{
					 optionsChar.series[0].data = data;
					  
                     chart = new Highcharts.Chart(optionsChar);
					 
				}catch(e){
					alert(e.message);
				}
				
				//$('#debug').text("");
				var counter=0;
				$("#myTable").html("");
				$.each(data, function(key,val) {
					//$('#debug').append(JSON.stringify($(this)) + "<br>");//print object
					counter++;
					//$('#debug').append(val.name +' ' + val.y + "<br>");
					try{
						$("#myTable").last().append('<tr>' +
					      				'<td>'+counter+'</td>' +
										'<td>'+val.name+'</td>' +
										'<td>'+val.y+'</td>' +
								'</tr>');
					}catch(e){
					  alert(e.message);	
					}
					
				});//end each
				
				$('#btnSearch').val("Search");
				$('#btnSearch').attr("disabled",false);
			}				
		});
}

function loadDetail(kodeBasic,nama){
	generatePiePerkelas(nama);
	$('#container_detail').show();
	$('#container_detail').html('<div align="center" style="margin-top:100;"><img name="load" src="ajax-loader.gif" width="100" height="100" alt="" /><br><h2>GET DATA ....</h2></div>');
	 var tgl1=$('#tgl1').val();
	 var tgl2=$('#tgl2').val();
	 var tipe=$('#tipe').val();
	 var outlet=$('#outlet').val();
	  var markas=$('#markas').val();
	  $.ajax({
			url:"dashboard_model_data.php", 
			type:"POST",
			cache: false,
			dataType:'json', 
			data:{j:'gsrk',t1:tgl1,t2:tgl2,o:outlet,t:tipe,b:kodeBasic,m:markas},
			success: function(data) {
				//$('#container_detail').text(data);return;
				 
				try{
					 optionsCharKelas.series[0].data = data;
                     chart = new Highcharts.Chart(optionsCharKelas);
				}catch(e){
					alert(e.message);
				}
				
				//$('#debug').text("");
				var counter=0;
				$.each(data, function(key,val) {
					//$('#debug').append(JSON.stringify($(this)) + "<br>");//print object
					counter++;
					//$('#debug').append(val.name +' ' + val.y + "<br>");
					try{
						/*$("#myTable").last().append('<tr>' +
					      				'<td>'+counter+'</td>' +
										'<td>'+val.name+'</td>' +
										'<td>'+val.y+'</td>' +
								'</tr>');*/
					}catch(e){
					  alert(e.message);	
					}
					
				});//end each
				
				
			}				
		});
}