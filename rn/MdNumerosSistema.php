<?php


  require_once dirname(__FILE__).'/../../../SEI.php';
  
  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();
//  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  //PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

  switch($_GET['acao']){

    case 'MdNumerosSistema':
      $strTitulo = 'SEI! em N&uacute;meros';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }
  
  $arrComandos = array();    
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

#lblStaPeriodicidadeExecucao {position:absolute;left:0%;top:0%;width:23%;}
#selStaPeriodicidadeExecucao {position:absolute;left:0%;top:40%;width:23%;}

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

echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/moment.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/Chart.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/chartjs-plugin-labels.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<link href="modulos/mod-numeros-sei/css/main.css?'.PaginaSEI::getInstance()->getNumVersao() . '" rel="stylesheet" type="text/css" media="all" />';
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

$versao = $indicadores['seiVersao'];
$tamanhoDB = $indicadores['tamanhoDatabase'];
$extensoes = $indicadores['extensoes'];
$cpu = $indicadores['porcentagemCPU'];
$memoria = $indicadores['quantidadeMemoria'];
$discoUsado = $indicadores['espacoDiscoUsado'];
//$soUsuarios = $indicadores['soUsuarios'];
$acessosMes = $indicadores['acessosUsuariosMes'];
//$navegadores = $indicadores['navegadores'];
$acessosNavegadores = $indicadores['acessosNavegadores'];
//$velocidades = $indicadores['velocidadeCidade'];
$anexos = $indicadores['anexosTamanhos'];
$acessosMes = $indicadores['acessosUsuariosMes'];

//criar arrays de datas e acessos
$arrData = array();
$arrAcesso = array();

foreach ($acessosMes as $fila) {	
  	$arrData[] = $fila['data'];
   $arrAcesso[] = $fila['quantidade'];
} 

$datas = implode("','",$arrData);
$acessos = implode(",",$arrAcesso);


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
$arrAnexosQtd = array();

foreach ($anexos as $fila) {	
  	$arrAnexos[] = $fila['tamanho'];
   $arrAnexosQtd[] = $fila['quantidade'];
}

$nomeAnexos = implode("','",$arrAnexos);
$quantAnexos = implode(",",$arrAnexosQtd);

echo "<p>\n";

?>

<div id="container">

	<div id="infoseix" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 15%; float: left; text-align: center; ">
<!--		<span style="font-size: 12pt;">Uso Mem&oacute;ria &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php //echo bytesToHRF($memoria,'GB'); ?></span>	-->
	</div>

	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 23%; float: left; text-align: center; border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Uso CPU &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $cpu; ?> %</span>
	</div>
	
<!--	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 23%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Uso Mem&oacute;ria &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php //echo bytesToHRF($memoria,'GB'); ?></span>	
	</div>-->
	
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 23%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Uso de Disco &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo bytesToHRF($discoUsado,'GB'); ?></span>	
	</div>
	
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 23%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Tamanho do Banco &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo bytesToHRF($tamanhoDB,'GB'); ?></span>	
	</div>
	
</div>

<!-- graficos -->
<div id="graficos" style="margin-top: 35px;">
	<div id="pie" style="width: 33%; float: left; margin-bottom: 10px;">
		<canvas id="navegadores" width="150" height="100"></canvas>
	</div>
			
	<div id="pie1" style="width: 33%; float: left; margin-bottom: 10px;">
		<canvas id="anexos" width="150" height="100"></canvas>
	</div>
	
	<div id="pie2" style="width: 33%; float: right; margin-bottom: 10px;">
		<canvas id="extensoes" width="150" height="100"></canvas>
	</div>

</div>

<div id="acessos_mes" style="width: 96%; float: left; margin-bottom: 10px; margin-top: 40px;">
	<canvas id="myChart" width="150" height="150"></canvas>
</div>

<script type="text/javascript" >


//anexos
var ctx = document.getElementById("anexos");

//Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 14;

var pieData = {
  labels: [<?php  echo "'".$nomeAnexos."'"; ?>],
  datasets: [{
    //label: "",
	 backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#c45850", "#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5",],
    data: [<?php echo $quantAnexos; ?>],
  }]
};

var chartOptions = {
  legend: {
    display: true,
    position: 'bottom',
	 segmentShowStroke : false,
    animateScale : true, 
    labels: {
      boxWidth: 80,
      fontColor: 'black'
    }
  }
};

var lineChart = new Chart(ctx, {
  type: 'pie',
  data: pieData,
  options: {
  	plugins:{
	  labels: [
	   	{
	      	render: 'value',
				fontColor: '#000',
	    	}
	    	,
	    	{
	      	render: 'percentage',
	      	position: 'outside'
	    	}
  		] 	
  	},  	
  		chartOptions,
 		title: {
 			display: true,
 			text: "Anexos por Tamanho"
  		}
  
  }
});


new Chart(document.getElementById("navegadores"), {
    type: 'horizontalBar',
    data: {
      labels: [<?php  echo "'".$nomeNav."'"; ?>],
      datasets: [
        {
          label: "",
          backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#c45850", "#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5", "#64E572", "#FF9655", "#FFF263", "#6AF9C4"],
          data: [<?php  echo $acessosNav ; ?>]
        }
      ]
    },
    options: {
      legend: { display: false },
      title: {
        display: true,
        text: 'Acessos por Navegador'
      }
    }
});


//extensoes
new Chart(document.getElementById("extensoes"), {
    type: 'horizontalBar',
    data: {
      labels: [<?php  echo "'".$nomeExt."'"; ?>],
      datasets: [
        {
          label: "",
          backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#c45850", "#058DC7", "#50B432", "#ED561B", "#DDDF00", "#24CBE5", "#64E572", "#FF9655", "#FFF263", "#6AF9C4"],
          data: [<?php  echo $quantExt ; ?>]
        }
      ]
    },
    options: {
      legend: { display: false },
      title: {
        display: true,
        text: 'Arquivos por Tipo'
      }
    }
});


//acessos por mes
var canvas = document.getElementById('myChart');
canvas.width = 550;
var data = {
    //labels: [moment().format("2020-01"), moment().format("2020-02"), moment().format("2020-03")],
    labels: [<?php  echo "'".$datas."'"; ?>],
    type: "line",
    datasets: [
        {
            data: [ <?php  echo $acessos; ?>],
            label: "Acessos Mensais - Ultimo Ano",
            borderWidth: 2,
            borderColor: "#3e95cd",
            fill: false,
            pointRadius: 2
        }
    ]
};

var myBarChart = Chart.Line(canvas,{
	data:data,
});

</script>

<?

PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
//PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

?> 
