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

    case 'MdNumerosProcedimentos':
      $strTitulo = 'SEI! em N&uacute;meros - Processos por Tipo';
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
$quantProcedimentos = $indicadores['quantidadeProcedimentos'];
$quantProcedimentosAbertos = $indicadores['quantidadeProcedimentosAbertos'];

echo "<p>\n";
?>
<div id="divEstatisticas">

<div id="container" style="margin-left: 3%;">
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Processos Conclu&iacute;dos&nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantProcedimentos - $quantProcedimentosAbertos; ?></span>	
	</div>
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Processos em tramita&ccedil;&atilde;o&nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantProcedimentosAbertos; ?></span>	
	</div>
	<div id="infosei" style="padding: 5px 0px 5px 0px; margin-bottom: 20px; margin-right:5px; width: 30%; float: left; text-align: center;border: 2px solid #cecece;">
		<span style="font-size: 12pt;">Total de Processos &nbsp;</span><br><span style="font-size: 14pt; font-weight: bold;"><?php echo $quantProcedimentos; ?></span>	
	</div>
</div>


<?php
$captionTabela = '';
$th1 = "";
$th2 = "";

gerarTabela($indicadores['quantidadeProcedimentosTipo'], 'Processos por Tipo', 95, 'Tipo de Processos', 'Quantidade', '',$dataColeta);

echo "</div>\n";

?>

<?
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

?> 
