<?php
namespace PesquisaFonetica;

class PesquisaFonetica
{
    public function __construct(){
    }
    
    function tirarAcentos($string){
        $string = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
        return strtoupper($string);
    }

    function LimpaRepeticao($sNome){
        $iNumeral = array('0','1','2','3','4','5','6','7','8','9');

        $i = 0;
        while( $i < strlen($sNome) ){
            $sAux    = mb_strcut($sNome, $i, 1);
            $sAuxPos = mb_strcut($sNome, ($i+1), 1);

            if( $sAux == $sAuxPos){
                if(!in_array($sAux, $iNumeral)){
                $sNome = mb_strcut($sNome, 0, $i) . mb_strcut($sNome, ($i+1), strlen($sNome));
                }
            }
            $i++;
        }
        return $sNome;
    }

    function Fonema($string){
        $sAux = explode(" ",$string );
        $sNome = "";
        for($i = 0; $i < count($sAux); $i++){
            $sAuxFonetico = $this->ExecFonema($this->LimpaRepeticao($this->tirarAcentos($sAux[$i])));
            $sNome.= ($sNome != "") ?  " ".$sAuxFonetico : $sAuxFonetico;
        }
        return $sNome;
    }



    function ExecFonema($sNome){
        $sAuxiliar    = $sNome;
        $sNovo = '';
        $i = 0;
        while( $i <= strlen($sNome) ){

            $sAux    = mb_strcut($sNome, $i, 1);
            $sAuxPos = mb_strcut($sNome, ($i+1), 1);
            $sAuxAnt = mb_strcut($sNome, ($i-1), 1);

//            print $sAux.$sAuxPos."<br/>";
            if(1 == 2 && in_array($sAux,array('A','E','I','O','U'))){                                                                           // Permanece com vogais
                $sNovo.= $sAux;
            }
            else if(in_array($sAux,array('B','D','F','J','K','L','M','N','R','T','V','X', '0','1','2','3','4','5','6','7','8','9'))){ // Permanece com certas consoantes e numerais
                $sNovo.= $sAux;
            }
            else if($sAux == 'C'){
                if(in_array($sAuxPos,array('H'))){
                    $sNovo .= 'X';
                }
                elseif(in_array($sAuxPos,array('I','Y','E'))){
                    $sNovo .= 'S';
                }else{
                    $sNovo .= 'K';
                }
            }
            else if($sAux == 'G'){
                if(in_array($sAuxPos,array('I','Y','E'))){
                    $sNovo .= 'J';
                }else{
                    $sNovo .= 'G';
                }
            }
            else if($sAux == 'P'){
                if(in_array($sAuxPos,array('H'))){
                    $sNovo .= 'F';
                }else{
                    $sNovo .= 'P';
                }
            }
            else if($sAux == 'Q'){
                if(in_array($sAuxPos,array('U'))){
                    $sNovo .= 'K';
                }else{
                    $sNovo .= 'Q';
                }
            }
            else if($sAux == 'S'){
                if(in_array($sAuxPos,array('H'))){
                    $sNovo .= 'X';
                }
                elseif(in_array($sAuxPos,array('A','E','I','O','U'))) {
                    if(in_array($sAuxAnt,array('A','E','I','O','U'))) {
                        $sNovo .= 'Z';
                    }else{
                        $sNovo .= 'S';
                    }
                }
                else{
                    $sNovo .= 'S';
                }
            }
            else if($sAux == 'W'){
                $sNovo .= 'V';
            }
            else if($sAux == 'Z'){
                if($i == strlen($sAuxiliar) || ($sAuxPos == ' ')){
                    $sNovo .= 'S';
                }else{
                    $sNovo .= 'Z';
                }
            }
            else if($i == 0){
                if($sAux == 'Y'){
                    $sNovo .= 'I';
                }else{
                    $sNovo .= $sAux;
                }
            }
            $i++;
        }

        return $sNovo;
    }


    function PalavraFonetica($sNome){
        return $this->Fonema($sNome);
    }

}