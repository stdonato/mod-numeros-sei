<?php

  require_once dirname(__FILE__).'/../../../SEI.php';
  
  //session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();
  //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  //PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

class MdNumerosColetaRN extends InfraRN {
	
    public function __construct() {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance();
    }

    public function coletarIndicadores() {
        try {

			$ind = array();
			
			$ind['dataColeta'] = $this->obterDataColeta();
			$ind['orgaoSigla'] = $orgaoSigla;
			$ind['seiVersao'] = $this->obterVersaoSEI();
			$ind['phpVersao'] = $this->obterVersaoPHP();
			//$ind['memcachedVersao'] = $this->obterVersaoMemcached();
         $ind['solrVersao'] = $this->obterVersaoSolr();
         //$ind['protocolo'] = $this->obterProtocolo();
         $ind['quantidadeUnidades'] = $this->obterQuantidadeUnidades();
         $ind['quantidadeProcedimentos'] = $this->obterQuantidadeProcessosAdministrativos();
         $ind['quantidadeProcedimentosAbertos'] = $this->obterQuantidadeProcessosAdministrativosAbertos();
         $ind['quantidadeProcedimentosTipo'] = $this->obterQuantidadeProcessosTipo($_GET['qtde']);
         $ind['quantidadeProcedimentosUnidades'] = $this->obterQuantidadeProcessosAdministrativosUnidades($_GET['qtde']);
         $ind['quantidadeProcedimentosUnidadesMes'] = $this->obterQuantidadeProcessosUnidadesMes();
         $ind['quantidadeUsuarios'] = $this->obterQuantidadeUsuarios();
         $ind['quantidadeUsuariosExternos'] = $this->obterQuantidadeUsuariosExternos();
         $ind['quantidadeDocumentosInternos'] = $this->obterQuantidadeDocumentosInternos();
         $ind['quantidadeDocumentosExternos'] = $this->obterQuantidadeDocumentosExternos();
         $ind['quantidadeDocumentosUnidades'] = $this->obterQuantidadeDocumentosUnidades($_GET['qtde']);
         $ind['quantidadeMemoria'] = $this->obterUsoMemoria();
         $ind['porcentagemCPU'] = $this->obterUsoCPU();
         $ind['espacoDiscoUsado'] = $this->obterEspacoDisco();
         //$ind['estrategiaCessao'] = $this->obterEstrategiaCessao();
         $ind['tamanhoDatabase'] = $this->obterTamanhoDataBase();
         //$ind['bancoSei'] = $this->obterTipoSGBD();
         //$ind['bancoVersao'] = $this->obterBancoVersao();
         //$ind['servidorAplicacao'] = $this->obterServidorAplicacao();
         $ind['sistemaOperacional'] = $this->obterSistemaOperacional();
         $ind['sistemaOperacionalDetalhado'] = $this->obterSistemaOperacionalDetalhado();
         $ind['tamanhoFilesystem'] = $this->obterTamanhoFileSystem();
         //$ind['tabelasTamanhos'] = $this->obterTamanhoTabelas();
         //$ind['modulos'] = $this->obterPlugins();
         $ind['extensoes'] = $this->obterQuantidadeDocumentosExternosPorExtensao();
         $ind['anexosTamanhos'] = $this->obterTamanhoDocumentosExternos();
         //$ind['isMonoOrgao'] = $this->obterSeMonoOrgao();
         $ind['acessosUsuarios'] = $this->obterAcessosUsuarios();
         $ind['acessosUsuariosMes'] = $this->obterAcessosUsuariosMes();
         //$ind['soUsuarios'] = $this->obterSistemasOperacionaisUsuarios();
         $ind['navegadores'] = $this->obterNavegadores();
         $ind['acessosNavegadores'] = $this->obterQuantidadeAcessosNavegadores();
         $ind['velocidadeCidade'] = $this->obterVelocidadePorCidade();
         $ind['quantidadeAcessosExternos'] = $this->obterQuantidadeAcessosExternos();
         $ind['quantidadeAcessosInternos'] = $this->obterQuantidadeAcessosInternos();
         $ind['quantidadeAcessosUnidades'] = $this->obterQuantidadeAcessosUnidades($_GET['qtde']);
         $ind['quantidadeAcessosUsuarios'] = $this->obterQuantidadeAcessosUsuarios($_GET['qtde']);
		
		    return $ind;
    
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro processando estatísticas do sistema.', $e);
        }
    }    


private static function bolArrFindItem($arrNeedle, $strHaystack){
        $r=false;
        foreach ($arrNeedle as $v) {
            $r=strpos($strHaystack, $v);
            if($r === 0 || $r) return $r;
        }
        return $r;
    }

    private $IG = array('sei/temp', 'sei/config/ConfiguracaoSEI.php', 'sei/config/ConfiguracaoSEI.exemplo.php');

    private static function getDirContents($dir, &$results = array(), $ignorar = array('sei/temp', 'sei/config/ConfiguracaoSEI.php', 'sei/config/ConfiguracaoSEI.exemplo.php', '.vagrant', '.git')){

        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!MdNumerosColetarRN::bolArrFindItem($ignorar, $path)){
                if(!is_dir($path)) {
                    $results[] = $path;

                } else if($value != "." && $value != "..") {
                    MdNumerosColetarRN::getDirContents($path, $results);
                }
            }
        }

        return $results;
    }

    public function obterHashs(){

        $a = MdNumerosColetarRN::getDirContents(DIR_SEI_CONFIG . '/../../');
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();

        if ($objConfiguracaoSEI->isSetValor('SEI','Modulos')){

            foreach($objConfiguracaoSEI->getValor('SEI','Modulos') as $strModulo => $strPathModulo){
                $reflectionClass = new ReflectionClass($strModulo);
                $classe = $reflectionClass->newInstance();
                $arrModulos[$strModulo] = array('modulo' => $strModulo, 'path' => $strPathModulo, 'versao' => $classe->getVersao());
            }
        }

        foreach ($a as $key => $value) {
            $m="";
            $version="";

            foreach ($arrModulos as $k => $v) {
                if(strpos($value, 'web/modulos/'.$arrModulos[$k]['path']) !== false){
                    $m = $k;
                    $version = $arrModulos[$k]['versao'];
                    break;
                }
            }

            //vamos retirar a parte inicial do dir que nao interessa
            $novo_valor = $value;
            $pos=MdNumerosColetarRN::bolArrFindItem(array('infra/infra', 'sei/', 'sip/'), $novo_valor);
            if($pos !== false){
                $novo_valor = substr($novo_valor, $pos);
            }

            $b[] = array('file' => $novo_valor,
                         'hash' => hash_file('sha256', $value),
                         'modulo' => $m,
                         'versaoModulo' => $version,
                         'versaoSei' => SEI_VERSAO);
        }

        return $b;

    }

    private function obterVersaoSEI() {
        return SEI_VERSAO;
    }

    private function obterVersaoPHP() {
        return phpversion();
    }

    private function getDirectorySize($path) {
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false) {
            try{
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                    try{
                        $bytestotal += $object->getSize();
                    }catch(Exception $e1){
                        $bytestotal += 0;
                    }
                }
            }catch(Exception $e2){
                $bytestotal = 0;
            }
        }
        return $bytestotal;
    }

    private function obterTamanhoFileSystem() {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        if ($objConfiguracaoSEI->isSetValor('SEI', 'RepositorioArquivos')) {
            $diretorio = $objConfiguracaoSEI->getValor('SEI', 'RepositorioArquivos');
            $tamanho = $this->getDirectorySize($diretorio);
        }
        return $tamanho;
    }

    private function obterPlugins() {
        global $SEI_MODULOS;
        $lista = array();
        foreach ($SEI_MODULOS as $strModulo => $seiModulo) {
            $result = array(
                'nome' => $strModulo,
                'versao' => $seiModulo->getVersao()
            );
            array_push($lista, $result);
        }

        return $lista;
    }

    private function obterQuantidadeUnidades() {
        $objUnidadeRN = new UnidadeRN();
        return $objUnidadeRN->contarRN0128(new UnidadeDTO());
    }

    private function obterTamanhoTotalDocumentosExternos() {
        $query = "select sum(tamanho) as tamanho from anexo where sin_ativo = 'S'";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $tamanho = (count($rs) && isset($rs[0]['tamanho'])) ? $rs[0]['tamanho'] : 0;
        return $tamanho;
    }

    private function obterQuantidadeUsuarios() {
        $query = "SELECT COUNT(*) as quantidade FROM usuario WHERE sin_ativo = 'S'  AND sta_tipo = 0";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }
    

    private function obterQuantidadeUsuariosExternos() {
        $query = "SELECT COUNT(*) as quantidade FROM usuario WHERE sin_ativo = 'S'  AND sta_tipo = 3";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }
        

    private function obterProtocolo() {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        if ($objConfiguracaoSEI->isSetValor('SessaoSEI', 'https')) {
            $temHTTPS = $objConfiguracaoSEI->getValor('SessaoSEI', 'https');
            $protocolo = 'HTTP';
            if ($temHTTPS) {
                $protocolo = 'HTTPS';
            }
            return $protocolo;
        }
    }

    private function obterQuantidadeProcessosAdministrativos() {
        $query = "select count(*) as quantidade from procedimento";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }

    
   private function obterQuantidadeProcessosAdministrativosAbertos() {
        $query = "select count(distinct id_protocolo) as total from atividade where dth_conclusao is null";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['total'])) ? $rs[0]['total'] : 0;
        return $quantidade;
    }
    
/*    private function obterQuantidadeProcessosAdministrativosUnidades() {
        $query = "select count(*) as quantidade from procedimento group by id_unidade";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }*/
    
    private function obterQuantidadeProcessosAdministrativosUnidades($limit = null) {
    	
    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "select u.descricao as unidade, u.sigla as sigla, count(p.id_protocolo) as quantidade from protocolo p, unidade u where p.sta_protocolo = 'P' and u.id_unidade = p.id_unidade_geradora group by p.id_unidade_geradora, u.descricao, u.sigla order by quantidade desc $limite";       
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
        $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'unidade' => $r['unidade']."  (".$r['sigla'].")",
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }
    
    
    private function obterQuantidadeProcessosUnidadesMes($limit = null) {
    	
    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "select to_char(dta_geracao,'YYYY-MM') AS data , count(*) as quantidade  from protocolo  where dta_geracao >= date '1900-01-01'  and sta_protocolo = 'P' group by to_char(dta_geracao,'YYYY-MM') order by data, quantidade";       
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
        $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'data' => $r['data'],
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }
    
    
    private function obterQuantidadeProcessosTipo($limit = null) {
    	
    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "select tp.nome as tipo, count(p.id_procedimento) as quantidade from procedimento p, tipo_procedimento tp  where p.id_tipo_procedimento = tp.id_tipo_procedimento group by p.id_tipo_procedimento, tp.nome order by quantidade desc $limite";        
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
        $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'tipo' => $r['tipo'],
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }

    private function obterTipoSGBD() {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        return $objConfiguracaoSEI->getValor('BancoSEI', 'Tipo', false, '');
    }

    private function obterQuantidadeDocumentosInternos() {
        $query = "SELECT COUNT(*) as quantidade FROM documento WHERE STA_DOCUMENTO = 'I'";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }
    
    private function obterQuantidadeDocumentosExternos() {
        $query = "SELECT COUNT(id_documento) as quantidade FROM documento WHERE STA_DOCUMENTO = 'X'";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }

    private function obterQuantidadeDocumentosExternosPorExtensao($limit = null) {
		
		  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "SELECT nome FROM anexo WHERE sin_ativo = 'S' $limite";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $extensoes = array();
        foreach ($rs as $r) {
            $extensao = $this->extrairExtensao($r['nome']);
            $qtd = $extensoes[$extensao];
            if (! $qtd) {
                $qtd = 0;
            }
            $extensoes[$extensao] = $qtd + 1;
        }
        // Calculando na aplicacao para funcionar independente do banco
        $lista = array();
        
        arsort($extensoes);

        foreach ($extensoes as $key => $value) {
            $result = array(
                'extensao' => $key,
                'quantidade' => $value
            );
            array_push($lista, $result);
        }
        
        //limita aos 10 primeiros
        $lista = array_slice($lista, 0, 10);
       
        return $lista;
    }


    private function extrairExtensao($filename) {
        $listaarq = explode('.', $filename);
        $extensao = end($listaarq);
        return utf8_encode($extensao);
    }


    private function obterQuantidadeDocumentosUnidades($limit = null) {

    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "SELECT u.descricao as unidade, u.sigla as sigla, COUNT(d.id_documento) as quantidade FROM documento d, unidade u WHERE d.STA_DOCUMENTO = 'I'  AND d.id_unidade_responsavel = u.id_unidade GROUP BY d.id_unidade_responsavel, u.descricao, u.sigla ORDER BY quantidade DESC $limite";        
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
       $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'unidade' => $r['unidade']."  (".$r['sigla'].")",
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }       

    
    private function obterQuantidadeAcessosUnidades($limit = null) {

    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "SELECT u.descricao as unidade, u.sigla as sigla, count(a.id_acesso) as quantidade from unidade u, acesso a where u.id_unidade = a.id_unidade group by a.id_unidade, u.descricao, u.sigla order by quantidade desc $limite";        
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
       $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'unidade' => $r['unidade']."  (".$r['sigla'].")",
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }     
    
    
    private function obterQuantidadeAcessosUsuarios($limit = null) {

    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "select u.nome as nome, u.sigla as sigla, count(a.id_acesso) as quantidade from usuario u, acesso a where u.id_usuario = a.id_usuario and u.sin_ativo = 'S' group by a.id_usuario, u.nome, u.sigla order by quantidade desc $limite";        
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
        $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'nome' => $r['nome']."  (".$r['sigla'].")",
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }
    

    private function obterEstrategiaCessao() {
        return ini_get('session.save_handler');
    }

    private function obterVersaoMemcached() {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        $host = $objConfiguracaoSEI->getValor('CacheSEI', 'Servidor', false, '');
        $porta = $objConfiguracaoSEI->getValor('CacheSEI', 'Porta', false, '');

        $memcache = new Memcache();
        $memcache->connect($host, $porta);
        $versao = $memcache->getVersion();

        return $versao;
    }

    private function obterTamanhoDatabase() {
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "SELECT table_schema, SUM(data_length + index_length) as tamanho FROM information_schema.TABLES WHERE table_schema = 'sei' GROUP BY table_schema";
        } elseif ($sgbd == 'SqlServer') {
            $query = "SELECT SUM(Total_Pages * 8 * 1000) As tamanho FROM sys.partitions As P INNER JOIN sys.allocation_units As A ON P.hobt_id = A.container_id  INNER JOIN sys.tables t on t.object_id = p.object_id";
        } elseif ($sgbd == 'Oracle') {
            $query = "select tablespace_name, sum(bytes) as tamanho from dba_data_files where tablespace_name = 'SEI' group by tablespace_name order by sum(bytes)";
        }
        $rs = array();
        if ($query) {
            $rs = BancoSEI::getInstance()->consultarSql($query);
        }
        $tamanho = (count($rs) && isset($rs[0]['tamanho'])) ? $rs[0]['tamanho'] : 0;
        return $tamanho;
    }

    private function obterTamanhoTabelas() {
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "SELECT table_name as tabela, data_length + index_length as tamanho FROM information_schema.TABLES WHERE table_schema = 'sei'";
        } elseif ($sgbd == 'SqlServer') {
            $query = "" . " SELECT t.name as tabela,  SUM(Total_Pages * 8 * 1000) As tamanho " . " FROM sys.partitions As P " . "   INNER JOIN sys.allocation_units As A ON P.hobt_id = A.container_id " . "   INNER JOIN sys.tables t on t.object_id = p.object_id " . " GROUP BY t.name ORDER BY t.name";
        } elseif ($sgbd == 'Oracle') {
            $query = "";
        }
        $tabelas = array();
        if ($query) {
            $tabelas = BancoSEI::getInstance()->consultarSql($query);
        }
        return $tabelas;
    }

    private function obterVersaoSolr() {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        $url = $objConfiguracaoSEI->getValor('Solr', 'Servidor', false, 'http://localhost:8983/solr');
        $url = $url . '/admin/info/system?wt=json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $json = json_decode($output, true);
        return $json['lucene']['lucene-spec-version'];
    }

    private function obterServidorAplicacao() {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    private function obterSistemaOperacional() {
        return PHP_OS;
    }

    private function obterSistemaOperacionalDetalhado() {
        return php_uname();
    }

    private function obterDataColeta() {
        return date(DATE_ATOM);
    }

    private function obterTamanhoDocumentosExternos() {
        $resultado = array();
        // 0MB - !MB
        $query = "SELECT count(*) as quantidade FROM anexo WHERE sin_ativo = 'S' AND tamanho >= 0 AND tamanho < 1000";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $resultado[0] = array(
            'tamanho' => '0MB - 1MB',
            'quantidade' => (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0
        );

        // 1MB - 10MB
        $query = "SELECT count(*) as quantidade FROM anexo WHERE sin_ativo = 'S' AND tamanho >= 1000 AND tamanho < 10000";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $resultado[1] = array(
            'tamanho' => '1MB - 10MB',
            'quantidade' => (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0
        );

        // 10MB - 100MB
        $query = "SELECT count(*) as quantidade FROM anexo WHERE sin_ativo = 'S' AND tamanho >= 10000 AND tamanho < 100000";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $resultado[2] = array(
            'tamanho' => '10MB - 100MB',
            'quantidade' => (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0
        );

        // > 100MB
        $query = "SELECT count(*) as quantidade FROM anexo WHERE sin_ativo = 'S' AND tamanho >= 100000";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $resultado[3] = array(
            'tamanho' => 'Maior que 100MB',
            'quantidade' => (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0
        );
        return $resultado;
    }

    private function obterUsoMemoria() {
        return memory_get_usage();
    }

    private function obterUsoCPU() {
        $load = sys_getloadavg();
        $uso = null;
        if ($load) {
            $uso = $load[0];
        }
        return $uso;
    }

    private function obterEspacoDisco() {
        if (php_uname('s') == 'Windows NT') {
            $unidade = substr($_SERVER['DOCUMENT_ROOT'], 0, 2);
            if (! $unidade) {
                $unidade = 'C:';
            }
        } else {
            $unidade = "/";
        }
        $total = disk_total_space($unidade);
        $free = disk_free_space($unidade);
        return $total - $free;
    }

    private function obterBancoVersao() {
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "SELECT version() as versao";
        } elseif ($sgbd == 'SqlServer') {
            $query = "SELECT SERVERPROPERTY('productversion') as versao";
        } elseif ($sgbd == 'Oracle') {
            $query = "select version AS versao from product_component_version WHERE product LIKE 'Oracle%'";
        }
        $rs = array();
        if ($query) {
            try{
                $rs = BancoSEI::getInstance()->consultarSql($query);
            }catch(Exception $e){
                $rs = array(array('versao' => 'Undefined'));
            }
        }
        $versao = (count($rs) && isset($rs[0]['versao'])) ? $rs[0]['versao'] : null;
        return $versao;
    }

    public function obterVelocidadePorCidade() {
        $query = "
      select d.nome as cidade, e.nome as uf, avg(velocidade) as velocidade
      from velocidade_transferencia a
        join unidade b on b.id_unidade = a.id_unidade
        join contato c on b.id_contato = c.id_contato
        join cidade d on c.id_cidade = d.id_cidade
        join uf e on d.id_uf = e.id_uf
      group by
        d.nome, e.nome
    ";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'cidade' => utf8_encode($r['cidade']),
                'uf' => utf8_encode($r['uf']),
                'velocidade' => $r['velocidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }

    public function obterAcessosUsuarios($ultimadata = null) {
    	
    	  //if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        if ($ultimadata == null) {
            //$ultimadata = "1900-01-01";
            $ultimadata = date('Y-m-d', strtotime('-180 days'));
        }
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "select count(*) as quantidade, date(dth_acesso) as data from infra_navegador where date(dth_acesso) > '%s' group by date(dth_acesso)";
        } elseif ($sgbd == 'SqlServer') {
            $query = "select count(*) as quantidade, CONVERT(date, dth_acesso) as data from infra_navegador where dth_acesso >= '%s' group by CONVERT(date, dth_acesso)";
        } elseif ($sgbd == 'Oracle') {
            $query = "select count(*) as quantidade, to_char(dth_acesso,'YYYY-MM-DD') AS data from infra_navegador where dth_acesso >= date '%s' group by to_char(dth_acesso,'YYYY-MM-DD')";
        }

        $rs = array();
        if ($query) {
            $query = sprintf($query, $ultimadata);
            $rs = BancoSEI::getInstance()->consultarSql($query);
        }
        return $rs;
    }
    
    
    public function obterAcessosUsuariosMes($ultimadata = null) {
    	    	  
        if ($ultimadata == null) {
            $ultimadata = "1900-01-01";
            //$ultimadata = date('Y-m-d', strtotime('-365 days'));
        }
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "select count(*) as quantidade, date(dth_acesso) as data from infra_navegador where date(dth_acesso) > '%s' group by date(dth_acesso)";
        } elseif ($sgbd == 'SqlServer') {
            $query = "select count(*) as quantidade, CONVERT(date, dth_acesso) as data from infra_navegador where dth_acesso >= '%s' group by CONVERT(date, dth_acesso)";
        } elseif ($sgbd == 'Oracle') {
            $query = "select count(*) as quantidade, to_char(dth_acesso,'YYYY-MM') AS data from infra_navegador where dth_acesso >= date '%s' group by to_char(dth_acesso,'YYYY-MM')  order by data, quantidade";
        }

        $rs = array();
        if ($query) {
            $query = sprintf($query, $ultimadata);
            $rs = BancoSEI::getInstance()->consultarSql($query);
        }
        return $rs;
    }
    

    public function obterSistemasOperacionaisUsuarios() {
        $sgbd = $this->obterTipoSGBD();
        if ($sgbd == 'Oracle') {
            $query = "select distinct to_char(user_agent) as nome from infra_auditoria where user_agent is not null";
        } else {
            $query = "select distinct user_agent as nome from infra_auditoria where user_agent is not null";
        }
        try{
            $sistemas = BancoSEI::getInstance()->consultarSql($query);
        } catch (Exception $e) {
            $sistemas = array(array('nome'=>'(XX)'));
        }

        $lista = array();
        foreach ($sistemas as $r) {
            $texto = $r['nome'];
            $inicio = strpos($texto, '(');
            if ($inicio !== false) {
                $fim = strpos($texto, ')', $inicio);
                $nome = substr($texto, $inicio + 1, $fim - $inicio - 1);
                array_push($lista, $nome);
            }
        }
        $lista = array_unique($lista);

        $sistemas = array();
        foreach ($lista as $n) {
            $result = array(
                'nome' => $n
            );
            array_push($sistemas, $result);
        }
        return $sistemas;
    }

 	public function obterNavegadores($ultimadata = null) {
        if ($ultimadata == null) {
          $ultimadata = "1900-01-01";
        }
        $current_month = date("Y-m-01");
        $sgbd = $this->obterTipoSGBD();
        $query = '';
        if ($sgbd == 'MySql') {
            $query = "SELECT year(dth_acesso) as ano, month(dth_acesso) as mes, identificacao as nome, versao, count(*) as quantidade from infra_navegador where date(dth_acesso) > '%s' and date(dth_acesso) < '%s' group by 1, 2, 3, 4 order by 1,2,3,4";
        } elseif ($sgbd == 'SqlServer') {
            $query = "SELECT year(dth_acesso) as ano, month(dth_acesso) as mes, identificacao as nome, versao, count(*) as quantidade from infra_navegador where dth_acesso > '%s' and dth_acesso < '%s' group by year(dth_acesso), month(dth_acesso), identificacao, versao order by 1,2,3,4";
        } elseif ($sgbd == 'Oracle'){
            $query = "SELECT to_char(dth_acesso, 'YYYY') AS ano, to_char(dth_acesso, 'MM') AS mes, identificacao as nome, versao, count(*) as quantidade from infra_navegador WHERE dth_acesso > date '%s'  AND dth_acesso < date '%s' group by to_char(dth_acesso, 'YYYY'), to_char(dth_acesso, 'MM'), identificacao, versao order by to_char(dth_acesso, 'YYYY'), to_char(dth_acesso, 'MM'), identificacao, versao";
        }
        $lista = array();
        if ($query) {
          $query = sprintf($query, $ultimadata, $current_month);
         // echo $query;
          $rs = BancoSEI::getInstance()->consultarSql($query);
          foreach ($rs as $r) {
              $result = array(
                  'nome' => utf8_encode($r['nome']),
                  'quantidade' => $r['quantidade'],
                  'versao' => $r['versao'],
                  'ano' => $r['ano'],
                  'mes' => $r['mes'],
              );
              array_push($lista, $result);
          }

        }
        return $lista;
    }
    

    private function obterQuantidadeAcessosNavegadores($limit = null) {

    	  if($limit != null) { $limite = 'FETCH FIRST '.$limit.' ROWS ONLY' ;} else { $limite = '';}
        $query = "select identificacao as nome, count(*) as quantidade from infra_navegador group by identificacao order by quantidade desc";        
        // Calculando na aplicacao para funcionar independente do banco
		  $rs = BancoSEI::getInstance()->consultarSql($query);
       $lista = array();
        foreach ($rs as $r) {
            $result = array(
                'nome' => $r['nome'],
                'quantidade' => $r['quantidade']
            );

            array_push($lista, $result);
        }
        return $lista;
    }     
           

    public function obterQuantidadeRecursos($dataultimorecurso) {
        if ($dataultimorecurso == null) {
            $dataultimorecurso = "1900-01-01";
        }
        $current_month = date("Y-m-01");
        $sgbd = $this->obterTipoSGBD();
        if ($sgbd == 'MySql') {
            $query = "SELECT year(dth_acesso) as ano, month(dth_acesso) as mes, recurso, count(*) as quantidade FROM infra_auditoria where date(dth_acesso) > '%s' and date(dth_acesso) < '%s' group by 1, 2, 3 order by 1, 2, 3";
        } elseif ($sgbd == 'SqlServer') {
            $query = "SELECT year(dth_acesso) as ano, month(dth_acesso) as mes, recurso, count(*) as quantidade FROM infra_auditoria where dth_acesso > '%s' and dth_acesso < '%s' group by year(dth_acesso), month(dth_acesso), recurso order by 1, 2, 3";
        } elseif ($sgbd == 'Oracle'){
            $query = "SELECT to_char(dth_acesso, 'YYYY') AS ano, to_char(dth_acesso, 'MM') AS mes, recurso, count(*) as quantidade FROM infra_auditoria WHERE dth_acesso > date '%s'  AND dth_acesso < date '%s' GROUP BY to_char(dth_acesso, 'YYYY'), to_char(dth_acesso, 'MM'), recurso";
        }
        if ($query) {
            $query = sprintf($query, $dataultimorecurso, $current_month);
            return BancoSEI::getInstance()->consultarSql($query);
        }
    }

    public function obterQuantidadeLogErro() {
        $sgbd = $this->obterTipoSGBD();
        if ($sgbd == 'MySql') {
            $query = "select year(dth_log) ano, month(dth_log) mes, week(dth_log) + 1 semana, count(*) as quantidade from infra_log where sta_tipo = 'E' group by 1, 2, 3";
        } elseif ($sgbd == 'SqlServer') {
            $query = "select year(dth_log) ano, month(dth_log) mes, datepart(week, dth_log) semana, count(*) as quantidade from infra_log where sta_tipo = 'E' group by year(dth_log), month(dth_log), datepart(week, dth_log)";
        } elseif ($sgbd == 'Oracle'){
            $query = "select to_char(dth_log, 'YYYY') AS ano, to_char(dth_log, 'MM') AS mes, to_char(dth_log, 'WW') AS semana, count(*) as quantidade from infra_log where sta_tipo = 'E' GROUP BY to_char(dth_log, 'YYYY'), to_char(dth_log, 'MM'), to_char(dth_log, 'WW')";
        }
        if ($query) {
            return BancoSEI::getInstance()->consultarSql($query);
        }
    }

    public function obterSeMonoOrgao () {
        $query = "SELECT count(*) as quantidade FROM orgao WHERE sin_ativo = 'S'";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade <= 1;
    }
    
    private function obterQuantidadeAcessosInternos() {
        $query = "SELECT COUNT(*) as quantidade FROM acesso";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }
    
    private function obterQuantidadeAcessosExternos() {
        $query = "SELECT COUNT(*) as quantidade FROM acesso_externo";
        $rs = BancoSEI::getInstance()->consultarSql($query);
        $quantidade = (count($rs) && isset($rs[0]['quantidade'])) ? $rs[0]['quantidade'] : 0;
        return $quantidade;
    }
        
//end class    
}


function gerarTabela($ind, $captionTabela, $larg, $th1, $th2, $linkTodos = null, $data = null) {

	$numRegistros = 0;
  
   $numRegistros = count($ind);   
 
  if ($numRegistros > 0){
    
/*    $bolAcaoImprimir = true;

    if ($bolAcaoImprimir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }*/
    
    $strResultado = '';
    
    $strSumarioTabela = 'Tabela de Processos.';
    $strCaptionTabela = $captionTabela;

    $strResultado .= '<div id="tabelaEstat">'."\n";
    $strResultado .= '<table width="'.$larg.'%" class="infraTable" style="float:left; margin-left:0px;" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    //if ($bolCheck) {
     // $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
   // }
    $strResultado .= '<th class="infraTh" width="50%" align="center">'.$th1.'</th>'."\n";
    $strResultado .= '<th class="infraTh" align="center">'.$th2.'</th>'."\n";  
    
    $strResultado .= '</tr>'."\n";
    
     // Table body      
       foreach ($ind as $fila) {
       	  $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      	  $strResultado .= $strCssTr;
           echo "<tr>";
           foreach ($fila as $elemento) {
                 $strResultado .=  "<td class='infraTrClara'>".$elemento."</td>\n";
           } 
          $strResultado .=  "</tr>";
       } 
    if($linkTodos != null) { 
      $strResultado .=  "<tr style='background:#e5e5e5;'><td>\n";
    	$link = $linkTodos; 
    	$strResultado .= '<span><a href="'.SessaoSEI::getInstance()->assinarLink('/sei/controlador.php?acao='.$link.'&acao_origem=sei_numeros&qtd='.$_GET['qtde']).'"><img src ="modulos/mod-numeros-sei/imagens/mais.png" height="12"/>&nbsp; Ver todos...</a></span>';
      $strResultado .=  "</td><td></td></tr>";
    }	
    
    $strResultado .= "</table>\n";
    if($data != null) { 
    	$strResultado .= "<span style='margin-right:5%; float:right;'>Atualizado em: ". $data ."</span>\n";
 	 }
    $strResultado .= "</div>";

  }

	echo $strResultado;
//end gerarTabela
}



function bytesToHRF($bytes,$unit='') {
	// convert bytes to a human readable format
		
	$units['TB']=1099511627776;
	$units['GB']=1073741824;
	$units['MB']=1048576;
	$units['KB']=1023;
		
	reset($units);
	//while(list($key,$value)=each($units)) {
	foreach($units as $key => $value){
		if($key==$unit) {
			$bytes=sprintf('%01.2f',$bytes/$value);
			return $bytes.$key;
		}
		if($bytes>$value && empty($unit)) {
			$bytes=sprintf('%01.2f',$bytes/$value);
			return $bytes.$key;
		}
	}
	return $bytes;
}


//acesso por navegador
//select identificacao, count(*) as quantidade from infra_navegador group by identificacao order by quantidade desc;

// acessos navegador versões
//SELECT identificacao, versao, COUNT(*) as total_acessos FROM infra_navegador WHERE dth_acesso > date '1900-01-01' AND dth_acesso < date '2050-01-01'  GROUP BY identificacao, versao  ORDER BY total_acessos desc

//processo por data criação
//select to_char(dta_geracao,'YYYY-MM') AS data , count(*) as quantidade  from protocolo  where dta_geracao >= date '1900-01-01'  and sta_protocolo = 'P' group by to_char(dta_geracao,'YYYY-MM') order by data, quantidade ;

?>
 
