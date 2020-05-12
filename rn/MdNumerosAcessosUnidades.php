<?php


  require_once dirname(__FILE__).'/../../../SEI.php';
  
  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();

  switch($_GET['acao']){

    case 'MdNumerosAcessosUnidades':
      $strTitulo = 'SEI! em N&uacute;meros - Acessos por Unidades';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }
  
  $arrComandos = array(); 
  $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirDiv(\'divEstatisticas\');" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=sei_numeros&qtde='.$_GET['qtd']).'\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';  


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

echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/moment.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/Chart.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';
echo '<script type="text/javascript" charset="iso-8859-1" src="modulos/mod-numeros-sei/lib/chartjs-plugin-labels.min.js?'.PaginaSEI::getInstance()->getNumVersao() . '"></script>';

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
$quantUsuarios = $indicadores['quantidadeUsuarios'];
$acessosInternos = $indicadores['quantidadeAcessosInternos'];
$acessosExternos = $indicadores['quantidadeAcessosExternos'];
$acessosMes = $indicadores['acessosUsuariosMes'];

//criar arrays de datas e acessos
$arrData = array();
$arrAcesso = array();

foreach ($acessosMes as $fila) {	
   $arrAcesso[] = $fila['quantidade'];
  	$arrData[] = $fila['data'];
} 

$datas = implode("','",$arrData);
$acessos = implode(",",$arrAcesso);

echo "<p>\n";
?>
<div id="divEstatisticas">
<div id="container" style="margin-left: 3%;">
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Acessos Internos&nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $acessosInternos; ?></span>	
	</div>

	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Acessos Externos&nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $acessosExternos; ?></span>	
	</div>

	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Total de Acessos &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $acessosInternos + $acessosExternos; ?></span>	
	</div>
</div>

<script type="text/javascript" >

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


<?php
$captionTabela = '';
$th1 = "";
$th2 = "";

gerarTabela($indicadores['quantidadeAcessosUnidades'], 'Acessos por Unidade', 95, 'Unidade', 'Acessos', '',$dataColeta);

echo "</div>\n"; 
?>

<?
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
//PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

?> 
