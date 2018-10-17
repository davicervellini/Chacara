<?php
	session_start();

	require_once "../../config.php";
	require_once "../../conexao/ConexaoMySQL.Class.php";
	require_once "../../classes/autoload.php";

	$sys = new Sistema;
	$usu = new Usuario;

	foreach ($_POST as $key => $value) {
		${$key} = ($value != "") ? $value : NULL;
	}

	$resp = [];
	switch($processo){
		case "inclusao":
			try{
					$usuCod = $sys->gera_codigo("usuarios");
					die($usuCod);
					$vDados = [
						"USU_CODIGO"         => $usuCod,
						"USU_NOME"           => $usuNome,
						"USU_EMAIL"          => strtolower($usuEmail),
						"USU_CARGO"          => $cargo,
						"USU_LOGIN"          => $usuLogin,
						"USU_SENHA"          => $usuLogin,
						"USU_ADMIN"          => $usuAdmin,
						"USU_PRIMEIROACESSO" => "0",
						"USU_APPS"           => "1"
					];


					$sql = $sys->getInsert("usuarios", $vDados);
					$qry = $conn->prepare($sql);
					$res = $qry->execute();

					$usu->inserirPermissoes($usuCod);
					if($_SESSION["usuario"]["usuAdmin"] != 1) $usu->inserirPermissoesCopia($_SESSION["usuario"]["usuCodigo"],$usuCod);

					$sys->historico('CADASTRO DE USUÁRIOS', 'INCLUIU O USUÁRIO - NOME: '.$usuNome.'; CÓDIGO: '.$usuCod.' ');

					$resp["resposta"] = "Usuário cadastrado com sucesso!";
					$resp["usuCod"] = $usuCod;

					$sSQL = "SELECT RECNO FROM usuarios WHERE USU_CODIGO = ". $usuCod;
					$qryRecno = $connMYSQL->prepare($sSQL);
					$qryRecno->execute();
					$resQuery = $qryRecno->fetch();
					$resp["usuRecno"] = $resQuery["RECNO"];
					$resp["usuAdmin"] = $usuAdmin;


 				print json_encode($resp);

			}catch(Exception $e){
				print $e->getMessage();
				die;
			}
		break;

		case "correcao":
			try{
				$vDados = [
					"USU_NOME"  => $usuNome,
					"USU_EMAIL" => $usuEmail,
					"USU_CARGO" => $cargo,
					"USU_LOGIN" => $usuLogin,
					"USU_ADMIN" => $usuAdmin,
				];

				$result = $sys->vStrings($vDados);
				$camposCorrecao = $sys->select('usuarios', $vDados, array('USU_CODIGO'=>$usuCodigo), false); // Retorna o resultado antes do UPDATE

				$sql = $sys->getUpdate("usuarios", "USU_CODIGO = ".$usuCodigo."" ,  $vDados);
				$qry = $conn->prepare($sql);
				$res = $qry->execute();

				$resultado = $sys->identificarCorrecao($camposCorrecao, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual

				$selectPermissoes = $sys->select('permissao', [], array('USU_CODIGO'=>$usuCodigo), false);
				if($selectPermissoes == ""){
					$usu->inserirPermissoes($usuCodigo);
					if($_SESSION["usuario"]["usuAdmin"] != 1) $usu->inserirPermissoesCopia($_SESSION["usuario"]["usuCodigo"],$usuCodigo);
				}

				foreach ($_SESSION["permissoesNovo"] as $key => $value){
					$pDados = $_SESSION["permissoesNovo"][$key];

					$vDados = [
						"ACESSO"  	=> $pDados['ACESSO'],
						"INCLUIR" 	=> $pDados['INCLUSAO'],
						"CORRIGIR" 	=> $pDados['CORRECAO'],
						"EXCLUIR" 	=> $pDados['EXCLUSAO'],
					];

					$result = $sys->vStrings($vDados);
					$camposCorrecao = $sys->select('permissao', $vDados, array('USU_CODIGO'=>$usuCodigo,'FORM'=>$key), false); // Retorna o resultado antes do UPDATE
					$sql = $sys->getUpdate("permissao", "USU_CODIGO = ".$usuCodigo." AND FORM = '".$key."'" , $vDados);
					$qry = $conn->prepare($sql);
					$res = $qry->execute();

					$resultado .= $sys->identificarCorrecao($camposCorrecao, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual
				}

				$sys->historico('CADASTRO DE USUÁRIOS', 'CORRIGIU O USUARIO - CÓDIGO:'.$usuCodigo.'; '.$resultado); // Informa no histórico os campos alterados

				$resp["resposta"] = "Usuário corrigido com sucesso!";
 				print json_encode($resp);

			}catch(Exception $e){
				print $e->getMessage();
				die;
			}
		break;

		case "exclusao":
			try{

				$sql = $sys->getDelete("usuarios", "USU_CODIGO = ".$usuCodigo." ");
				$qry = $conn->prepare($sql);
				$res = $qry->execute();

				$sys->historico('CADASTRO DE USUÁRIOS', 'EXCLUIU O USUÁRIO - NOME: '.$usuNome.'; CÓDIGO: '.$usuCodigo.' ');
				$resp["resposta"] = "Usuário excluído com sucesso!";
 				print json_encode($resp);

			}catch(Exception $e){
				print $e->getMessage();
				die;
			}
		break;

		case "atualizarTabelaUsuarios":
			try{

				$qry = $usu->listUsuarios();
				$cont = 0;
				$selected = 0;
				$ativo = "";
				if($qry){
					$resp['grid'] = " <thead>
							<th>Nome</th>
							<th>Login</th>
							<th>Email</th>
						</thead>
						<tbody>";
					foreach ($qry as $ln) {
			            $cont++;
			            if($ln["RECNO"] == $recno){
			                $ativo = "tr-active";
			                $selected = $cont;
			            }else{
			                $ativo = "";
			            }
						$resp['grid'] .= "<tr class='trHighlight ".$ativo."' onclick=\"preencheCampos('".$ln["USU_CODIGO"]."',this)\" style=\"cursor:pointer\">
							<td>".$ln["USU_NOME"]."</td>
							<td>".$ln["USU_LOGIN"]."</td>
							<td>".$ln["USU_EMAIL"]."</td>
						</tr>";
					}

					$resp['grid'] .= " </tbody>";
        			$resp['pagina'] = (10 * floor(($selected/10)));
				}else{
					$resp['grid'] = "<thead>
							<th>&nbsp;</th>
						   </head>
						   <tbody>
						   	<tr><td>Nenhum registro encontrado</td></tr>
						   </tbody>";
				}
				print json_encode($resp);
			}catch(Exception $e){
				print $e->getMessage();
			}
		break;

		case "verificarLogin":
			if($habilita == 0){
				$valid = $usu->verificaLogin($usuLogin);
				print $valid;
			}else{
				if($usuLogin == $usuAntigo){
					print "1";
				}else{
					$valid = $usu->verificaLogin($usuLogin);
					print $valid;
				}
			}
		break;

		case "resetarSenha":
			try{
				$vDados = [
					"USU_SENHA"          => $usuLogin,
					"USU_PRIMEIROACESSO" => 0,
				];
				$sql = $sys->getUpdate("usuarios", "RECNO = ".$recno."" , $vDados);
				$qry = $conn->prepare($sql);
				$res = $qry->execute();

				if($res != ""){
					print $res;
				}

				$sys->historico('CADASTRO DE USUÁRIOS', 'RESETOU A SENHA DO USUARIO - LOGIN:'.$usuLogin); // Informa no histórico os campos alterados

				$resp["message"] = "Senha do usuário resetada com sucesso!";
 				print json_encode($resp);

			}catch(Exception $e){
				print $e->getMessage();
				die;
			}
		break;

		case "verificaPermissoes":
			if($usuCodigo != ""){
				$conn = new ConexaoMySQL();
				if($chave == 0){
					$_SESSION["permissoesNovo"] = [];
				}
				$sql="SELECT men.MEN_GRUPO
					  FROM menus men
					  WHERE MEN_GRUPO NOT IN ('Títulos e Documentos - Recibos','Pessoas Jurídicas - Recibos','Certidão - Recibos')
					  GROUP BY men.MEN_GRUPO, men.MEN_GRUPO_ORDEM
					  ORDER BY men.MEN_GRUPO_ORDEM, men.MEN_GRUPO";
				$qryMenus = $conn->prepare($sql);
				$qryMenus->execute();
				$html = "";
				if($qryMenus->rowCount() > 0){
					$menus = $qryMenus->fetchAll(PDO::FETCH_ASSOC);
					foreach ($menus as $lnMenus) {
						$sqlPermissoes = "SELECT men.MEN_MENU, men.MEN_GRUPO, men.MEN_FORM, men.MEN_DESCRICAO, men.MEN_SUBMENU, per.ACESSO, per.INCLUIR,
												 per.CORRIGIR, per.EXCLUIR
										FROM menus men
										LEFT JOIN permissao per ON (per.FORM = men.MEN_FORM AND USU_CODIGO = :USU_CODIGO)
										WHERE MEN_GRUPO = :MEN_GRUPO AND MEN_FORM NOT IN ('F_CadConfigMenus')
										ORDER BY men.MEN_SUBMENU, men.MEN_GRUPO_ORDEM, men.MEN_GRUPO, men.MEN_DESCRICAO;";
						$qry = $conn->prepare($sqlPermissoes);
						$qry->bindParam(":USU_CODIGO", $usuCodigo);
						$qry->bindParam(":MEN_GRUPO", $lnMenus['MEN_GRUPO']);
						$qry->execute();
						if($qry->rowCount() > 0){
							$result = $qry->fetchAll(PDO::FETCH_ASSOC);
							$inicio = true;
							$cont = 0;
							$sMenu = "";
							$fromAntes = "";
		 					// <ul class="collapsible" data-collapsible="accordion">

							foreach ($result as $ln) {
								$cont++;

								if ($ln["MEN_GRUPO"] == "" || $ln["MEN_GRUPO"] == null) {
									continue;
								}
								if($ln['MEN_SUBMENU'] == ""){
									if($sMenu != $ln["MEN_GRUPO"]){
										if($inicio == false){
											$html .= " 		</ul>
														</div>
													</li>
												</ul>";
										}
										$html .= "
											<ul class=\"collapsible z-depth-2\" data-collapsible=\"accordion\" style=\"border-radius:5px;margin-top: 0px;margin-bottom: 5px;\">
												<li style=\"border-radius:5px\">
													<div class='collapsible-header' style='border-radius:5px'>
														<span style='font-size:18px'>".$ln["MEN_GRUPO"]."</span>
													</div>
													<div class=\"collapsible-body\" style='padding: 5px 4rem 2rem 20px;'>
														<ul>
															<li>
																<div class='row'>
																	<div class='col l4 input-field'>
																		<span>Selecionar todos</span>
																	</div>
																	<div class='col l2'>
																		<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('acesso','".$ln["MEN_GRUPO"]."',this)\" id='acesso".$ln["MEN_GRUPO"]."' >
																		<label for='acesso".$ln["MEN_GRUPO"]."'>Acesso</label>
																	</div>
																	<div class='col l2'>
																		<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('inclusao','".$ln["MEN_GRUPO"]."',this)\" id='inclusao".$ln["MEN_GRUPO"]."' >
																		<label for='inclusao".$ln["MEN_GRUPO"]."'>Inclusão</label>
																	</div>
																	<div class='col l2'>
																		<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('correcao','".$ln["MEN_GRUPO"]."',this)\" id='correcao".$ln["MEN_GRUPO"]."' >
																		<label for='correcao".$ln["MEN_GRUPO"]."'>Correção</label>
																	</div>
																	<div class='col l2'>
																		<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('exclusao','".$ln["MEN_GRUPO"]."',this)\" id='exclusao".$ln["MEN_GRUPO"]."' >
																		<label for='exclusao".$ln["MEN_GRUPO"]."'>Exclusão</label>
																	</div>
																</div>
																<div class='row no-bottom'>
																	<div class='col l12 divider'></div>
																</div>
															</li>
										";
									}
									$inicio = false;

									$acesso   = ($ln["ACESSO"]   == 1)? "checked" : "";
									$inclusao = ($ln["INCLUIR"]  == 1)? "checked" : "";
									$correcao = ($ln["CORRIGIR"] == 1)? "checked" : "";
									$exclusao = ($ln["EXCLUIR"]  == 1)? "checked" : "";
									if($ln["MEN_FORM"] != $fromAntes){
										$html .= "
											<li>
												<div class= 'row no-bottom'>
													<div class='col l4 input-field'>
														<span>".$ln["MEN_DESCRICAO"]."</span>
													</div>
													<div class='col l2'>
														<input type='checkbox' class='filled-in checkModal' ".$acesso." id='acesso".$ln["MEN_FORM"]."' onclick=\"alterarPermissoes('ACESSO','".$ln["MEN_FORM"]."')\" data-menu='acesso".$ln["MEN_GRUPO"]."' ><label for='acesso".$ln["MEN_FORM"]."'>Acesso</label>
													</div>
													<div class='col l2'>
														<input type='checkbox' class='filled-in checkModal' ".$inclusao." id='inclusao".$ln["MEN_FORM"]."' onclick=\"alterarPermissoes('INCLUIR','".$ln["MEN_FORM"]."')\" data-menu='inclusao".$ln["MEN_GRUPO"]."' ><label for='inclusao".$ln["MEN_FORM"]."'>Inclusão</label>
													</div>
													<div class='col l2'>
														<input type='checkbox' class='filled-in checkModal' ".$correcao." id='correcao".$ln["MEN_FORM"]."' onclick=\"alterarPermissoes('CORRIGIR','".$ln["MEN_FORM"]."')\" data-menu='correcao".$ln["MEN_GRUPO"]."' ><label for='correcao".$ln["MEN_FORM"]."'>Correção</label>
													</div>
													<div class='col l2'>
														<input type='checkbox' class='filled-in checkModal' ".$exclusao." id='exclusao".$ln["MEN_FORM"]."' onclick=\"alterarPermissoes('EXCLUIR','".$ln["MEN_FORM"]."')\" data-menu='exclusao".$ln["MEN_GRUPO"]."' ><label for='exclusao".$ln["MEN_FORM"]."'>Exclusão</label>
													</div>
												</div>
											</li>
										";
									}
									$fromAntes = $ln["MEN_FORM"];
								}else{
									$sqlPermissoesSub = "
										SELECT men.MEN_MENU, men.MEN_GRUPO, men.MEN_FORM, men.MEN_DESCRICAO, men.MEN_SUBMENU, per.ACESSO, per.INCLUIR,
											   per.CORRIGIR, per.EXCLUIR
										FROM menus men
										LEFT JOIN permissao per ON (per.FORM = men.MEN_FORM AND USU_CODIGO = :USU_CODIGO)
										WHERE MEN_GRUPO = :MEN_GRUPO
										ORDER BY men.MEN_GRUPO_ORDEM, men.MEN_DESCRICAO, men.MEN_GRUPO";
									$qrySub = $conn->prepare($sqlPermissoesSub);
									$qrySub->bindParam(":USU_CODIGO", $usuCodigo);
									$qrySub->bindParam(":MEN_GRUPO", $ln['MEN_SUBMENU']);
									$qrySub->execute();
									if($qrySub->rowCount() > 0){
										$resultSub = $qrySub->fetchAll(PDO::FETCH_ASSOC);
										$inicioSub = true;
										$sMenuSub = "";

										foreach ($resultSub as $lnSub) {
											$cont++;
											if($sMenuSub != $lnSub["MEN_GRUPO"]){
												if($inicioSub == false){
													$html .= " 		</ul>
																</div>
															</li>
														</ul>";
												}
												$html .= "
													<ul class=\"collapsible z-depth-2\" data-collapsible=\"accordion\" style=\"border-radius:5px;margin-top: 15px;margin-bottom: 5px;\">
														<li style=\"border-radius:5px\">
															<div class='collapsible-header' style='border-radius:5px'>
																<span style='font-size:18px'>".$ln["MEN_DESCRICAO"]."</span>
															</div>
															<div class=\"collapsible-body\" style='padding: 5px 4rem 2rem 20px;'>
																<ul>
																	<li>
																		<div class='row'>
																			<div class='col l4 input-field'>
																				<span>Selecionar todos</span>
																			</div>
																			<div class='col l2'>
																				<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('acesso','".$lnSub["MEN_GRUPO"]."',this)\" id='acesso".$lnSub["MEN_GRUPO"]."' >
																				<label for='acesso".$lnSub["MEN_GRUPO"]."'>Acesso</label>
																			</div>
																			<div class='col l2'>
																				<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('inclusao','".$lnSub["MEN_GRUPO"]."',this)\" id='inclusao".$lnSub["MEN_GRUPO"]."' >
																				<label for='inclusao".$lnSub["MEN_GRUPO"]."'>Inclusão</label>
																			</div>
																			<div class='col l2'>
																				<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('correcao','".$lnSub["MEN_GRUPO"]."',this)\" id='correcao".$lnSub["MEN_GRUPO"]."' >
																				<label for='correcao".$lnSub["MEN_GRUPO"]."'>Correção</label>
																			</div>
																			<div class='col l2'>
																				<input type='checkbox' class='filled-in checkModal' onclick=\"selectAllPermissoes('exclusao','".$lnSub["MEN_GRUPO"]."',this)\" id='exclusao".$lnSub["MEN_GRUPO"]."' >
																				<label for='exclusao".$lnSub["MEN_GRUPO"]."'>Exclusão</label>
																			</div>
																		</div>
																		<div class='row no-bottom'>
																			<div class='col l12 divider'></div>
																		</div>
																	</li>
												";
											}
											$inicioSub = false;

											$acesso   = ($lnSub["ACESSO"]   == 1)? "checked" : "";
											$inclusao = ($lnSub["INCLUIR"]  == 1)? "checked" : "";
											$correcao = ($lnSub["CORRIGIR"] == 1)? "checked" : "";
											$exclusao = ($lnSub["EXCLUIR"]  == 1)? "checked" : "";
											if($lnSub["MEN_FORM"] != $fromAntes){
												$html .= "
													<li>
														<div class= 'row no-bottom'>
															<div class='col l4 input-field'>
																<span>".$lnSub["MEN_DESCRICAO"]."</span>
															</div>
															<div class='col l2'>
																<input type='checkbox' class='filled-in checkModal' ".$acesso." id='acesso".$lnSub["MEN_FORM"]."' onclick=\"alterarPermissoes('ACESSO','".$lnSub["MEN_FORM"]."')\" data-menu='acesso".$lnSub["MEN_GRUPO"]."' ><label for='acesso".$lnSub["MEN_FORM"]."'>Acesso</label>
															</div>
															<div class='col l2'>
																<input type='checkbox' class='filled-in checkModal' ".$inclusao." id='inclusao".$lnSub["MEN_FORM"]."' onclick=\"alterarPermissoes('INCLUIR','".$lnSub["MEN_FORM"]."')\" data-menu='inclusao".$lnSub["MEN_GRUPO"]."' ><label for='inclusao".$lnSub["MEN_FORM"]."'>Inclusão</label>
															</div>
															<div class='col l2'>
																<input type='checkbox' class='filled-in checkModal' ".$correcao." id='correcao".$lnSub["MEN_FORM"]."' onclick=\"alterarPermissoes('CORRIGIR','".$lnSub["MEN_FORM"]."')\" data-menu='correcao".$lnSub["MEN_GRUPO"]."' ><label for='correcao".$lnSub["MEN_FORM"]."'>Correção</label>
															</div>
															<div class='col l2'>
																<input type='checkbox' class='filled-in checkModal' ".$exclusao." id='exclusao".$lnSub["MEN_FORM"]."' onclick=\"alterarPermissoes('EXCLUIR','".$lnSub["MEN_FORM"]."')\" data-menu='exclusao".$lnSub["MEN_GRUPO"]."' ><label for='exclusao".$lnSub["MEN_FORM"]."'>Exclusão</label>
															</div>
														</div>
													</li>
												";
											}
											$fromAntes = $ln["MEN_FORM"];
											$sMenuSub  = $lnSub["MEN_GRUPO"];
										}
										$html .= " 		</ul>
														</div>
													</li>
												</ul>";
									}
								}

								$sMenu = $ln["MEN_GRUPO"];
							}
							$html .= " 		</ul>
										</div>
									</li>
								</ul>";
						}
					}
				}else{
					$html = "<center class='red-text' style='font-weight:bold'>Nenhum usuário selecionado.</center>";
				}
			}else{
				$html = "<center class='red-text' style='font-weight:bold'>Nenhum usuário selecionado.</center>";
			}
			print $html;
		break;

	    case 'preencheCampos':
	        try{
				$sqlUsuario = "SELECT RECNO, USU_NOME, USU_LOGIN, USU_EMAIL, USU_CARGO, USU_ADMIN FROM usuarios WHERE USU_CODIGO = :USU_CODIGO";
				$qryUsuario = $connMYSQL->prepare($sqlUsuario);
				$qryUsuario->bindParam(':USU_CODIGO', $id, PDO::PARAM_STR);
				$qryUsuario->execute();
				$row = $qryUsuario->fetch(PDO::FETCH_ASSOC);

				$resp["usuNome"]  = $row["USU_NOME"]   ?? '';
				$resp["usuLogin"] = $row["USU_LOGIN"]  ?? '';
				$resp["usuEmail"] = $row["USU_EMAIL"]  ?? '';
				$resp["cargo"]    = $row["USU_CARGO"]  ?? '';
				$resp["usuAdmin"] = $row["USU_ADMIN"]  ?? '';
				$resp["usuRecno"] = $row["RECNO"]      ?? '';
				$_SESSION["permissoesNovo"] = [];
	            print json_encode($resp);

	        }catch(Exception $e){
	            $resp['error'] = $e->getMessage();
	            print json_encode($resp);
	        }

	    break;
	}
?>
