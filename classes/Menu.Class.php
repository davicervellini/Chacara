<?php

class Menu{
	public $conn;

	private $links = array();

	public function append($nomeLink, $href, $form = "", $ident = false, $target = null){
		$vLinks = array(
			'nomeLink' => $nomeLink,
			'href' => $href,
			'ident' => $ident,
			'target'=> $target );

		if(@$_SESSION["usuario"]["usuAdmin"] == 1){
			
			array_push($this->links, $vLinks);

			if($ident == true){
				$vTitulo = $this->links[count($this->links)-2];

				if(isset($vTitulo["title"])){
					$this->subtitulo = $vTitulo["title"];
				}

				$this->links[count($this->links)-1]["subtitulo"] = $this->subtitulo ;
			}

		}else{
			
			if($nomeLink == "Alterar Senha"){
				array_push($this->links, $vLinks);
			}

			if(@$_SESSION["permissoes"][$form]["acesso"] == 1){
				array_push($this->links, $vLinks);
				if($ident == true){
					$vTitulo = $this->links[count($this->links)-2];
					if(isset($vTitulo["title"])){
						$this->subtitulo = $vTitulo["title"];
					}

					$this->links[count($this->links)-1]["subtitulo"] = $this->subtitulo ;
				}
			}
		}
	}

	public function appendTitle($title){
		array_push($this->links, array(
			"title" => $title
		));
	}

	public function render(){
		foreach ($this->links as $key => $value) {
			$subtituloArray = isset($this->links[$key+1]) ? $this->links[$key+1] : [];
			if(count($value) == 1 && array_key_exists("title", $value ) && !array_key_exists("subtitulo",  $subtituloArray)){
				unset($this->links[$key]);
			}
		}

		if(count($this->links) > 0 ){
			print "
			<li class=\"no-padding\">
				<ul class=\"collapsible\" data-collapsible=\"expandable\">
				  <li>
				    <span class=\"collapsible-header\" style='border:1px solid #FAFAFA;'><i class=\"material-icons\" style=\"color:rgba(0, 0, 0, 0.54);\">".$this->materialIcon."</i>".$this->nomeMenu."<i class=\"material-icons right no-margin\">arrow_drop_down</i></span>
				    <div class=\"collapsible-body no-padding\">
						<ul>";

			foreach ($this->links as $key => $value) {
				if(isset($value["title"])){

					$iCont = 0;
					foreach ($this->links as $chave => $valor) {
						if(isset($valor["subtitulo"]) && $valor["subtitulo"] === $value["title"]){
							$iCont++;
						}
					}
					if($iCont > 0)
						print "<li style='line-height:32px;padding-left:38px;background-color:#FFF;font-weight:bold;'>".$value['title']."</li>";

				}
				else{
					$padding = "padding-left:32px";
					if($value["ident"] == true){
						$padding = "padding-left:56px";
					}

					$target = ($value["target"] !== null)  ? "target=\"".$value["target"]."\" " : "";
					print "<li class=\"tooltipped\" ><a href=\"".$value['href']."\" $target style='$padding'><i class=\"material-icons\" style='margin:0;line-height:35px; height:35px; '>keyboard_arrow_right</i>&nbsp;".$value['nomeLink']."</a></li>";
				}
			}

			print "		</ul>
				    </div>
				  </li>
				</ul>
			</li>";
		}
	}

	public function renderLink($nomeLink, $href, $materialIcon, $from = "", $function = ""){
		if($_SESSION["usuario"]["usuAdmin"] == 1 || $_SESSION["permissoes"][$from]['acesso'] == 1){
			if($function == 1){
				print "<li><a onclick=Route.open(\"$href\") style='cursor: pointer;padding-left:20px' ><i class=\"material-icons\">$materialIcon</i>$nomeLink</a></li>";
			}else{
				print "<li><a href=\"$href\" style='padding-left:20px'><i class=\"material-icons\">$materialIcon</i>$nomeLink</a></li>";
			}
		}
	}

	public function __construct($nomeMenu = null, $materialIcon = 'menu'){
		if($nomeMenu !== null){
			$this->nomeMenu     = $nomeMenu;
			$this->materialIcon = $materialIcon;
		}

		$this->conn = new ConexaoMySQL;
	}
}
