<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Datas extends GetterSetter{
	public $conn;
	public $sys;
	public $ws;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;
		$this->ws   = new ArcaTDPJ_WS;
		if($codigo != ""){

		}
	}

	public function insertData($tabela, $ocoCodigo, $options = ''){
        
        $dtAtual = date('Y-m-d');
        $hrAtual = date('H:i:s');

        $vDados = array(
            'OCO_CODIGO'    => $ocoCodigo,
            'DAT_DTBAIXA'   => $dtAtual,
            'DAT_HORA'      => $hrAtual,
            'DAT_USUARIO'   => $_SESSION["usuario"]["usuLogin"]
        );

        if($options != ""){
            $vDados = array_merge($vDados, $options);
        }
        
        $tabelas = array();
        switch ($tabela) {
            case 'datas':
                $prefixo    = "ARG";
                $tabelas[0] = "arc_reg";
                $tabelas[1] = "arc_reg_temp";
                $protocolo  = $vDados["ARS_PROTOCOLO"];
                $campo      = "ARS_PROTOCOLO";
            break;
            case 'datas_cer':
                $prefixo    = "CER";
                $tabelas[0] = "arc_cer";
                $protocolo  = $vDados["ACE_PROTOCOLO"];
                $campo      = "ACE_PROTOCOLO";
            break;            
            case 'datas_exa':
                $prefixo    = "ARE";
                $tabelas[0] = "arc_exa";
                $protocolo  = $vDados["ASE_PROTOCOLO"];
                $campo      = "ASE_PROTOCOLO";
            break;
        }

        try{
	        $result = $this->sys->vStrings($vDados);
	        
            $res = $this->ws->inserirRegistro(getToken($this->conn->db()), $tabela, $result['campos'] , $result['dados'] );
            if($res != ""){
              return $res;
            }
            for ($i=0; $i < count($tabelas); $i++) { 
                $vDadosPosicao = [
                    $prefixo."_POSICAO"     => $ocoCodigo,
                    $prefixo."_DATAPOSICAO" => $dtAtual,
                    $prefixo."_HORAPOSICAO" => $hrAtual
                ];
                $result = $this->sys->vStrings($vDadosPosicao);
                $res    = $this->ws->corrigirRegistro(getToken($this->conn->db()), $tabelas[$i], "$campo = $protocolo", $result['campos'] , $result['dados']);
                if($res != ""){
                  return $res;
                }
            }

        }catch(Exception $e){
        	return $e->getMessage();
        }
    }

    public function getCodigoDevolucao($ocoCodigo){
        switch ($ocoCodigo) {
            case '2':     //Prenotado
                return 9; //Devolvido Registrado
            break;
            
            case '4':     //Registrado
                return 9; //Devolvido Registrado
            break;

            case '5':      //Registro Parcial
                return 10; //Devolvido Registrado Parcialmente
            break;

            case '6':      //Irregular
                return 11; //Devolvido Irregular
            break;

            case '7':      //Cancelado
                return 12; //Devolvido Cancelado
            break;

            case '24':     //Não Apto
                return 24; //Não Altera
            break;

            case '25':     //Apto
                return 25; //Não Altera
            break;

            case '26':     //Pronto
                return 29; //Pronto Devolvido
            break;

            default:
                return "";
            break;
        }
    }

    public function getDevolucao($devCodigo){
        switch ($devCodigo) {
            case '9':
                return "DEVOLVIDO REGISTRADO";
            break;
            
            case '10':
                return "DEVOLVIDO REGISTRADO PARCIALMENTE";
            break;

            case '11':
                return "DEVOLVIDO IRREGULAR";
            break;

            case '12':
                return "DEVOLVIDO CANCELADO";
            break;

            case '24': 
                return "DEVOLVIDO NÃO APTO";
            break;

            case '25':
                return "DEVOLVIDO APTO";
            break;

            case '29':
                return "DEVOLVIDO PRONTO";
            break;

            default:
                return "";
            break;
        }
    }
}