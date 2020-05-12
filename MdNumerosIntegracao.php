<?php
/**
 * MPRO
 *
 * 05/05/2020 - criado por stevenes.donato@mpro.mp.br
 *
 */

class MdNumerosIntegracao extends SeiIntegracao
{

//    public function __construct()
//    {
//    }

    public function getNome()
    {
        return 'Modulo de Estatisticas do SEI';
    }

    public function getVersao()
    {
        return '0.0.1';
    }

    public function getInstituicao()
    {
        return 'Ministerio Publico do Estado de Rondonia';
    }

//    public function inicializar($strVersaoSEI)
//    {
//        /*
//        if (substr($strVersaoSEI, 0, 2) != '3.'){
//          die('Módulo "'.$this->getNome().'" ('.$this->getVersao().') não é compatível com esta versão do SEI ('.$strVersaoSEI.').');
//        }
//        */
//    }

    public function processarControlador($strAcao)
    {

        switch($strAcao) {

		case 'sei_numeros':
        require_once dirname(__FILE__).'/rn/MdNumerosExibir.php';
        return true;

      case 'MdNumerosProcedimentos':
        require_once dirname(__FILE__).'/rn/MdNumerosProcedimentos.php';
        return true;
        
      case 'MdNumerosProcedimentosUnidades':
        require_once dirname(__FILE__).'/rn/MdNumerosProcedimentosUnidades.php';
        return true;

      case 'MdNumerosDocumentosUnidades':
        require_once dirname(__FILE__).'/rn/MdNumerosDocumentosUnidades.php';
        return true; 

      case 'MdNumerosAcessosUnidades':
        require_once dirname(__FILE__).'/rn/MdNumerosAcessosUnidades.php';
        return true; 

      case 'MdNumerosAcessosUsuarios':
        require_once dirname(__FILE__).'/rn/MdNumerosAcessosUsuarios.php';
        return true; 

      case 'MdNumerosArquivosTipo':
        require_once dirname(__FILE__).'/rn/MdNumerosArquivosTipo.php';
        return true; 
        
      case 'MdNumerosSistema':
        require_once dirname(__FILE__).'/rn/MdNumerosSistema.php';
        return true;                         
                                
    }

    return false;

    }
}

?>