<?php


  require_once dirname(__FILE__).'/../../../SEI.php';
  
  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  //PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

  switch($_GET['acao']){

    case 'sei_numeros':
      $strTitulo = 'SEI! em N&uacute;meros';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }
  
  $arrComandos = array();    
  $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirDiv(\'divEstatisticas\');" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar').'\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';  


//coleta de indicadores
include "MdNumerosIndicadores.php";


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
#lblHelp {position:absolute;left:0%;top:0%;width:95%;}

#lblStaPeriodicidadeExecucao {position:absolute;left:0%;top:0%;width:25%;}
#selStaPeriodicidadeExecucao {position:absolute;left:0%;top:40%;width:25%;}

tr.trVermelha{
background-color:#f59f9f; 
}

.infraTh { height:30px; }

table {margin-top:25px;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
    document.getElementById('btnFechar').focus();
    infraEfeitoTabelas();
}

function pesquisar(){
    document.getElementById('frmEouvRelatorioImportacaoLista').action='<?=$strLinkPesquisar?>';
    document.getElementById('frmEouvRelatorioImportacaoLista').submit();
}

<?
PaginaSEI::getInstance()->fecharJavaScript();

echo '<link href="modulos/mod-numeros-sei/css/main.css?'.PaginaSEI::getInstance()->getNumVersao() . '" rel="stylesheet" type="text/css" media="all" />';

echo '<script src="modulos/mod-numeros-sei/js/highcharts.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script src="modulos/mod-numeros-sei/js/modules/exporting.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script src="modulos/mod-numeros-sei/js/modules/export-data.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
?>

<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('auto');
?>

<?php

$coletor = new MdNumerosColetaRN();
$indicadores = $coletor->coletarIndicadores();

$dataColeta =	date("d/m/Y H:i:s");
$versao = $indicadores['seiVersao'];
$quantUnidades = $indicadores['quantidadeUnidades'];
$quantProcedimentos = $indicadores['quantidadeProcedimentos'];
$quantProcedimentosAbertos = $indicadores['quantidadeProcedimentosAbertos'];
$quantProcedimentosUnidadesMes = $indicadores['quantidadeProcedimentosUnidadesMes'];
$quantUsuarios = $indicadores['quantidadeUsuarios'];
$quantUsuariosExternos = $indicadores['quantidadeUsuariosExternos'];
$docInternos = $indicadores['quantidadeDocumentosInternos'];
$docExternos = $indicadores['quantidadeDocumentosExternos'];
$acessosInternos = $indicadores['quantidadeAcessosInternos'];
$acessosExternos = $indicadores['quantidadeAcessosExternos'];
//$extensoes = $indicadores['extensoes'];

//$tamanhoDB = $indicadores['tamanhoDatabase'];
$extensoes = $indicadores['extensoes'];
$cpu = $indicadores['porcentagemCPU'];
$memoria = $indicadores['quantidadeMemoria'];
$discoUsado = $indicadores['espacoDiscoUsado'];
$acessosMes = $indicadores['acessosUsuariosMes'];
$acessosNavegadores = $indicadores['acessosNavegadores'];
$anexos = $indicadores['anexosTamanhos'];
$acessosMes = $indicadores['acessosUsuariosMes'];
//$soUsuarios = $indicadores['soUsuarios'];
//$navegadores = $indicadores['navegadores'];
//$velocidades = $indicadores['velocidadeCidade'];

// acesso por navegadores 
$arrNome = array();
$arrAcessoNav = array();

foreach ($acessosNavegadores as $fila) {	
  	$arrNome[] = $fila['nome'];
   $arrAcessoNav[] = $fila['quantidade'];
}

$nomeNav = implode("','",$arrNome);
$acessosNav = implode(",",$arrAcessoNav);


// extensoes
$arrExt = array();
$arrExtQtd = array();

foreach ($extensoes as $fila) {	
  	$arrExt[] = $fila['extensao'];
   $arrExtQtd[] = $fila['quantidade'];
}

$nomeExt = implode("','",$arrExt);
$quantExt = implode(",",$arrExtQtd);


// anexos
$arrAnexos = array();
$arrAnexosQuant = array();

foreach ($anexos as $fila) {	
  	$arrAnexos[] = $fila['tamanho'];
   $arrAnexosQuant[] = $fila['quantidade'];
}

$nomeAnexos = implode("','",$arrAnexos);
$quantAnexos = implode(",",$arrAnexosQuant);


//criar arrays de datas e processos
$arrDataProc = array();
$arrQuantProc = array();

foreach ($quantProcedimentosUnidadesMes as $fila) {	
  	$arrDataProc[] = $fila['data'];
   $arrQuantProc[] = $fila['quantidade'];
} 

$datasProc = implode("','",$arrDataProc);
$quantProc = implode(",",$arrQuantProc);

$dataIniProc = $arrDataProc[0];
$anoIniProc = substr($dataIniProc,0,4);
$mesIniProc = substr($dataIniProc,5,7);
$dataInicialProc = $anoIniProc.",".($mesIniProc - 1).", 1";


//criar arrays de acessos por data
$arrData = array();
$arrAcesso = array();

foreach ($acessosMes as $fila) {	
  	$arrData[] = $fila['data'];
   $arrAcesso[] = $fila['quantidade'];
} 

$datasAcesso = implode("','",$arrData);
$quantAcessos = implode(",",$arrAcesso);

$dataIniAcessos = $arrData[0];
$anoIniAcessos = substr($dataIniAcessos,0,4);
$mesIniAcessos = substr($dataIniAcessos,5,7);
$dataInicialAcessos = $anoIniAcessos.",".($mesIniAcessos - 1).", 1";


echo "<p>\n";
?>
<div id="divEstatisticas">

<div id="container">
	<div id="infosei" >
		<span style="font-size: 12pt;">Vers&atilde;o do SEI &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $versao; ?></span>
	</div>
	
	<div id="infosei" >
		<span style="font-size: 12pt;">Unidades &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantUnidades; ?></span>	
	</div>
	
	<div id="infosei" >
		<span style="font-size: 12pt;">Processos &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantProcedimentos; ?></span>	
	</div>
	
	<div id="infosei" >
		<span style="font-size: 12pt;">Documentos &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $docInternos + $docExternos; ?></span>	
	</div>
	
	<div id="infosei" >
		<span style="font-size: 12pt;">Usu&aacute;rios &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantUsuarios + $quantUsuariosExternos; ?></span>	
	</div>	

</div>

<!-- graficos -->
<div id="graficos">
	<div id="pie" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="processos" width="150" height="100"></div>
	</div>
	
	<div id="pie1" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="tipoDocumento" width="150" height="100"></div>
	</div>
		
	<div id="pie2" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="acessos" width="150" height="100"></div>
	</div>
</div>

<div id="graficos2" style="margin-top: 35px;">
	<div id="pie" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="extensoes" width="150" height="100"></div>
	</div>
			
	<div id="pie1" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="anexosTamanho" width="150" height="100"></div>
	</div>
	
	<div id="pie2" style="width: 33%; float: left; margin-bottom: 10px;">
		<div id="navegadores" width="150" height="100"></div>
	</div>

</div>

<div id="processos_mes" style="floatx: left;  margin-bottom: 10px; margin-top: 40px;">
	<div id="processosMes" style="width: 100%; height: 400px"></div>
</div>

<div id="acessos_mes" style="widthx: 96%; floatx: left; margin-bottom: 10px; margin-top: 40px;">
	<div id="acessosData" style="width: 100%; height: 400px" ></div>
</div>

<script type="text/javascript" >

//highcharts processos

$(function () {		
 	   		
// Build the chart
  $('#processos').highcharts({
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
      },
      credits: {
     		enabled: false
 		},
      title: {
          text: 'Processos'
      },
      tooltip: {
  	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              size: '85%',
 					dataLabels: {
						format: '{point.y} - ( {point.percentage:.1f}% )',
             		style: {
                  	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                  		},
                  connectorColor: 'black'
              },
          showInLegend: true
          }
      },
      series: [{
          type: 'pie',
          name: 'Processos',
          data: [{
					name: 'Abertos',
					y: <?php echo $quantProcedimentosAbertos; ?>
          	},
          	{
          		name: 'Encerrados',
          		y: <?php echo $quantProcedimentos - $quantProcedimentosAbertos; ?>	
          	}						                                                         
		    ],
      }]
  });
 });


//tipos documentos
$(function () {		
 	   		
// Build the chart
  $('#tipoDocumento').highcharts({
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
      },
		 credits: {
		     enabled: false
		 },      
      title: {
          text: 'Documentos'
      },
      tooltip: {
  	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              size: '85%',
 					dataLabels: {
						format: '{point.y} - ( {point.percentage:.1f}% )',
             		style: {
                  	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                  		},
                  connectorColor: 'black'
              },
          showInLegend: true
          }
      },
      series: [{
          type: 'pie',
          name: 'Documentos',
          data: [{
					name: 'Internos',
					y: <?php echo $docInternos; ?>
          	},
          	{
          		name: 'Externos',
          		y: <?php echo $docExternos; ?>	
          	}						                                                         
		    ],
      }]
  });
 });
 

// tipos acessos
$(function () {		
 	   		
// Build the chart
  $('#acessos').highcharts({
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
      },
      title: {
          text: 'Acessos'
      },
      tooltip: {
  	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              size: '85%',
 					dataLabels: {
						format: '{point.y} - ( {point.percentage:.1f}% )',
             		style: {
                  	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                  		},
                  connectorColor: 'black'
              },
          showInLegend: true
          }
      },
      series: [{
          type: 'pie',
          name: 'Acessos',
          data: [{
					name: 'Internos',
					y: <?php echo $acessosInternos; ?>
          	},
          	{
          		name: 'Externos',
          		y: <?php echo $acessosExternos; ?>	
          	}						                                                         
		    ],
      }]
  });
 }); 
 
 
//graficos2

//extensoes
$(function () {
  $('#extensoes').highcharts({
      chart: {
          type: 'bar',
			 height: 450,
          plotBorderColor: '#ffffff',
      	 plotBorderWidth: 0 
      },
	 credits: {
	     enabled: false
	 },            
      title: {
          text: 'Arquivos por Tipo - Top 10'
      },
     
      xAxis: {
          categories: [ <?php  echo "'".$nomeExt."'"; ?> ], 
          labels: { 
          	  text: '',	                    
              align: 'center',
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }, 
              overflow: 'justify'                
               },
               title: {
				  text: '',
              align: 'middle'
        		  		}
              },
      yAxis: {
          min: 0,
          title: {
				  text: '',
              align: 'middle'
          },
          labels: {
              overflow: 'justify'
          }
      },
   tooltip: {
          valueSuffix: ' '
      },
      plotOptions: {
          column: {
              dataLabels: {
                  enabled: true                                                
              },
               borderWidth: 2,
          		borderColor: '#fff',
          		shadow:true,           
          		showInLegend: false,
          }
      },
      series: [{
			 colorByPoint: true, 
          name: ' ',
          data: [ <?php  echo $quantExt ; ?> ],
          dataLabels: {
              enabled: true,                                        
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }    
      }]
  });
 });


//anexos tamanho
$(function () {		    	   		
// Build the chart
  $('#anexosTamanho').highcharts({
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
      },
		 credits: {
		     enabled: false
		 },            
      title: {
          text: 'Anexos por Tamanho'
      },
      tooltip: {
  	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              size: '80%',
 					dataLabels: {
						format: '{point.y} - ( {point.percentage:.1f}% )',
             		style: {
                  	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                  		},
                  connectorColor: 'black'
              },
          showInLegend: true
          }
      },
      series: [{
          type: 'pie',
          name: 'Anexos por Tamanho',
          data: [
                               
			<?php
				  $conta = count($arrAnexos); 
				  for($i = 0; $i < $conta; $i++) {    
			     echo '[ "' . $arrAnexos[$i] . '", '.$arrAnexosQuant[$i].'],';
			        }
			 ?>                           
			    ],
      }]
  });
 });


// navegadores
$(function () {
  $('#navegadores').highcharts({
      chart: {
          type: 'bar',
			 height: 450,
          plotBorderColor: '#ffffff',
      	 plotBorderWidth: 0 
      },
      title: {
          text: 'Acessos por Navegador'
      },
	 credits: {
	     enabled: false
	 },
     
      xAxis: {
          categories: [ <?php  echo "'".$nomeNav."'"; ?> ], 
          labels: { 
          	  text: '',	                    
              align: 'center',
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }, 
              overflow: 'justify'                
               },
               title: {
				  text: '',
              align: 'middle'
        		  		}
              },
      yAxis: {
          min: 0,
          title: {
				  text: '',
              align: 'middle'
          },
          labels: {
              overflow: 'justify'
          }
      },
   tooltip: {
          valueSuffix: ' '
      },
      plotOptions: {
          column: {
              dataLabels: {
                  enabled: true                                                
              },
               borderWidth: 2,
          		borderColor: '#fff',
          		shadow:true,           
          		showInLegend: false,
          }
      },
      series: [{
			 colorByPoint: true, 
          name: ' ',
          data: [ <?php  echo $acessosNav ; ?> ],
          dataLabels: {
              enabled: true,                                        
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }    
      }]
  });
 });


//processos por data

 Highcharts.chart('processosMes', {
 
chart: {
       type: 'areaspline'
		 //height: 450,
       //plotBorderColor: '#ffffff',
   	// plotBorderWidth: 0 
      },  	

 title: {
     text: 'Processos por Meses'
 },
 
 credits: {
     enabled: false
 },

 xAxis: {
     type: 'datetime',
     title: {
        text: ''
     }
 },
   yAxis: {
       title: {
           text: ''
       }
   },    

 plotOptions: {
     series: {
         pointStart: Date.UTC(<?php  echo $dataInicialProc; ?>),            
         pointIntervalUnit: 'month'
     }
 },

 series: [{
 	  name: "Processos",
     data: [<?php  echo $quantProc; ?>]
 }],
  responsive: {
     rules: [{
         condition: {
             maxWidth: 400
         },
         chartOptions: {
             legend: {
                 align: 'center',
                 verticalAlign: 'bottom',
                 layout: 'horizontal'
             },
             yAxis: {
                 labels: {
                     align: 'left',
                     x: 0,
                     y: -5
                 },
                 title: {
                     text: null
                 }
             },
             subtitle: {
                 text: null
             },
             credits: {
                 enabled: false
             }
         }
     }]
 }
});


//acessos por mes
 Highcharts.chart('acessosData', {
 	
chart: {
       type: 'areaspline' 
}, 	

 title: {
     text: 'Acessos por Meses'
 },
 
 credits: {
     enabled: false
 }, 

 xAxis: {
     type: 'datetime',
     title: {
        text: ''
     }
 },
   yAxis: {
       title: {
           text: ''
       }
   },    

 plotOptions: {
     series: {
         pointStart: Date.UTC(<?php  echo $dataInicialAcessos; ?>),            
         pointIntervalUnit: 'month',
         color: '#339966'
     }
 },

 series: [{
 	  name: "Acessos",
     data: [<?php  echo $quantAcessos; ?>]
 }],
  responsive: {
     rules: [{
         condition: {
             maxWidth: 400
         },
         chartOptions: {
             legend: {
                 align: 'center',
                 verticalAlign: 'bottom',
                 layout: 'horizontal'
             },
             yAxis: {
                 labels: {
                     align: 'left',
                     x: 0,
                     y: -5
                 },
                 title: {
                     text: null
                 }
             },
             subtitle: {
                 text: null
             },
             credits: {
                 enabled: false
             }
         }
     }]
 }
});
</script>

<?php
$captionTabela = '';
$th1 = "";
$th2 = "";
$linkTodos = "";

gerarTabela($indicadores['quantidadeProcedimentosUnidades'], 'Processos por Unidade', 97, 'Unidades', 'Quantidade de Processos', 'MdNumerosProcedimentosUnidades');

gerarTabela($indicadores['quantidadeProcedimentosTipo'], 'Processos por Tipo', 97, 'Tipo de Processos', 'Quantidade', 'MdNumerosProcedimentos');

gerarTabela($indicadores['quantidadeDocumentosUnidades'], 'Documentos por Unidade', 97, 'Unidade', 'Documentos', 'MdNumerosDocumentosUnidades');

gerarTabela($indicadores['quantidadeAcessosUnidades'], 'Acessos por Unidade', 97, 'Unidade', 'Acessos', 'MdNumerosAcessosUnidades');

gerarTabela($indicadores['quantidadeAcessosUsuarios'], 'Acessos por Usu&aacute;rios', 97, 'Usu&aacute;rios', 'Acessos', 'MdNumerosAcessosUsuarios', $dataColeta);

echo "</div>\n";  
?>

<?
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
//PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

?> 
