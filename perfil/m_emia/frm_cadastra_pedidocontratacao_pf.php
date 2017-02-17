<?php
	if(isset($_GET['id_ped']))
	{
		$idPedidoContratacao = $_GET['id_ped'];
	}
	$con = bancoMysqli();
	if(isset($_POST['action']))
	{
		switch($_POST['action'])
		{
			case "novo": //caso seja um novo pedido
				$idEmia = $_POST['idEmia'];
				$emia = recuperaDados("sis_emia",$_POST['idEmia'],"idEmia");
				$proponente = recuperaDados("sis_pessoa_fisica",$emia['IdPessoaFisica'],"Id_PessoaFisica");
				$proponenteEmia = recuperaDados("sis_pessoa_fisica_emia",$emia['IdPessoaFisica'],"IdPessoaFisica");
				$vigencia = $emia['IdVigencia'];				
				$idPessoa = $emia['IdPessoaFisica'];
				$idVerba = "";
				$instituicao = $_SESSION['idInstituicao'];
				$justificativa = addslashes($cargo['justificativa']);
				$mensagem = "";
				// insere um novo pedido pf com pessoa = 5
				$sql_pedido = "INSERT INTO `igsis_pedido_contratacao` (`idEvento`, `tipoPessoa`, `idPessoa`, `instituicao`, `justificativa`, `publicado`) VALUES ('$idEmia', '5', '$idPessoa',  '$instituicao', '$justificativa', '1')";
				$query_pedido = mysqli_query($con,$sql_pedido);
				if($query_pedido)
				{
					$idPedidoContratacao = mysqli_insert_id($con);
					$mensagem = "Pedido Criado.";
					// atualiza o sis_emia com o idPedidoContratacao
					$sql_atualiza_emia = "UPDATE sis_emia SET idPedidoContratacao = '$idPedidoContratacao' WHERE idEmia = '$idEmia'";
					$query_atualiza_emia = mysqli_query($con,$sql_atualiza_emia);
					if($query_atualiza_emia)
					{
						$mensagem = $mensagem."<br /> Tabela da EMIA atualizado com o número de Pedido de Contratacao";	
					}
					else
					{
						$mensagem = $mensagem."<br /> Erro ao atualizar tabela formação com o número de Pedido de Contratacao";	
					}
					//cria as parcela e atualiza a tabela pedido com os valores
					$sql_cria_parcelas = "SELECT * FROM sis_emia_parcelas WHERE Id_Vigencia = '$vigencia' ORDER BY N_Parcela ASC";
					$query_cria_parcelas = mysqli_query($con,$sql_cria_parcelas);
					$i = 1;
					while($parcela = mysqli_fetch_array($query_cria_parcelas))
					{
						//idPedido, numero, valor, vencimento, vigencia_inicio, vigencia_final, horas
						$numero = $parcela['N_Parcela'];
						$valor = $parcela['Valor'];
						$pagamento = $parcela['pagamento'];
						$vigencia_inicio = $parcela['dataInicio'];
						$vigencia_final = $parcela['dataFinal'];
						$horas = $parcela['horas'];
						$sql_insere_parcelas = "INSERT INTO `igsis_parcelas` (`idParcela`, `idPedido`, `numero`, `valor`, `vencimento`, `publicado`, `descricao`, `vigencia_inicio`, `vigencia_final`, `horas`) VALUES (NULL, '$idPedidoContratacao', '$numero', '$valor', '$pagamento', NULL, NULL, '$vigencia_inicio', '$vigencia_final', '$horas')";
						if($valor != 0)
						{
							$i++;	 
						}		
						$query_insere_parcelas = mysqli_query($con,$sql_insere_parcelas);
						if($query_insere_parcelas)
						{
							$mensagem = $mensagem."<br /> Parcela $numero inserida.";
						}
						else
						{
							$mensagem = $mensagem."<br /> Erro.";	
						}	
					}
					$valor_total = somaParcela($idPedidoContratacao,$i);
					//atualizamos a tabela prinicpal com os valores e o número de parcelas
					$sql_atualiza_parcela = "UPDATE igsis_pedido_contratacao SET parcelas = '$i',
					valor = '$valor_total' WHERE idPedidoContratacao = '$idPedidoContratacao'";
					$query_atualiza_parcela = mysqli_query($con,$sql_atualiza_parcela);
					if($query_atualiza_parcela)
					{
						$mensagem .= "<br />Valor e parcelas atualizados";	
					}
					else
					{
						$mensagem .= "<br />Erro ao atualizar parcelas e valor";	
					}
				}
				else
				{
					$mensagem = "Erro ao criar pedido";	
				}
			break;
			case "atualizar":
				$idPedidoContratacao = $_POST['idPedido'];
				$Observacao = addslashes($_POST['Observacao']);
				$Suplente  = $_POST['Suplente']; 
				$Fiscal  = $_POST['Fiscal'];
				$Parecer  = addslashes($_POST['Parecer']);
				$Justificativa  = addslashes($_POST['Justificativa']);
				$Verba = $_POST['Verba'];
				$sql_atualiza_pedido = "UPDATE igsis_pedido_contratacao SET
					observacao = '$Observacao',
					parecerArtistico = '$Parecer',
					justificativa = '$Justificativa', 
					idVerba = '$Verba'
					WHERE idPedidoContratacao = '$idPedidoContratacao'";
				$query_atualiza_pedido = mysqli_query($con,$sql_atualiza_pedido);
				//verificaMysql($sql_atualiza_pedido);
				if($query_atualiza_pedido)
				{
					$sql_atualiza_emia = "UPDATE sis_emia SET
						fiscal = '$Fiscal',
						suplente = '$Suplente'
						WHERE idPedidoContratacao = '$idPedidoContratacao'";
					$query_atualiza_emia = mysqli_query($con,$sql_atualiza_emia);
					if($query_atualiza_emia)
					{
						$mensagem = "Pedido Atualizado";
					}
					else
					{
						$mensagem = "Erro ao atualizar pedido (I)";	
					}
				}
				else
				{
					$mensagem = "Erro ao atualizar pedido(II)";	
				}				
			break;
		}	
	}
	if(isset($_POST['enviar']))
	{
		$dataEnvio = date('Y-m-d');
		$idPedidoContracao = $_POST['idPedido'];
		$sql_enviar = "UPDATE igsis_pedido_contratacao SET estado = '2'WHERE idPedidoContratacao = '$idPedidoContratacao'";
		$query_enviar = mysqli_query($con,$sql_enviar);
		if($query_enviar)
		{
			$sql_emia = "UPDATE sis_emia SET dataEnvio = '$dataEnvio' WHERE idPedidoContratacao = '$idPedidoContratacao'";
			$query_emia = mysqli_query($con,$sql_emia);
			if($query_emia)
			{
				$mensagem = "Pedido enviado à area de contratos";
			}
			else
			{
				$mensagem = "Erro ao enviar pedido(1)";
			}
		}
		else
		{
			$mensagem = "Erro ao enviar pedido(2)";	
		}
	}
	$ano=date('Y');
	$id_ped = $idPedidoContratacao;
	$pedido = recuperaDados("igsis_pedido_contratacao",$id_ped,"idPedidoContratacao");
	$proponente = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
	$emia = recuperaDados("sis_emia",$pedido['idPedidoContratacao'],"idPedidoContratacao");
	$cargo = recuperaDados("sis_emia_cargo",$emia['IdCargo'],"Id_Cargo");
	$faixaEtaria = retornaFaixaEtaria($emia['idFaixaEtaria']);
	//$verba = recuperaDados("sis_verba",$programa['verba'],"Id_Verba");
	$objeto = "Realizar ".$cargo['Cargo']." da EMIA, da faixa etária de ".$faixaEtaria.".";
	//$local=;	
	$carga = retornaCargaHoraria($pedido['idPedidoContratacao'],$pedido['parcelas']);
	$periodo = retornaPeriodoVigencia($pedido['idPedidoContratacao'],$pedido['parcelas']);
	$justif = $cargo['justificativa'];
	//MENU
	include 'includes/menu.php';
?>
<section id="contact" class="home-section bg-white">
	<div class="container">
		<div class="form-group">
			<h2>PEDIDO DE CONTRATAÇÃO DE PESSOA FÍSICA</h2>
			<p><?php if(isset($mensagem)){ echo $mensagem; } ?></p>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-10">
				<form class="form-horizontal" role="form" action="#" method="post">
					<div class="form-group">
						<div class="col-md-offset-2 col-md-8"><strong>Código de Dados para Contratação:</strong><br/>
							<input  readonly name="idEmia"  type="text" class="form-control" id="idEmia" value="<?php echo $emia['idEmia'] ?>">
						</div>
					</div>
					<div class="form-group"> 
						<div class="col-md-offset-2 col-md-8"><strong>Proponente:</strong><br/>
							<input type='text' readonly class='form-control' name='nome' id='nome' value="<?php echo $proponente['Nome']." (".$proponente['CPF'].")"; ?>">                    	
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-offset-2 col-md-8"><strong>Objeto:</strong><br/>
							<textarea readonly="readonly"  class="form-control" rows="5"><?php echo $objeto; ?> </textarea>
						</div>
					</div>
                  	<div class="form-group">
						<div class="col-md-offset-2 col-md-8"><strong>Local:</strong><br/>
							<textarea readonly="readonly"  class="form-control" rows="5"><?php echo $local; ?> </textarea>
						</div>
					</div>       
					<div class="form-group">
						<div class="col-md-offset-2 col-md-6"><strong>Período:</strong><br/>
							<input type='text' readonly name="Periodo" class='form-control' value="<?php echo $periodo ?>">
						</div>
						<div class="col-md-6"><strong>Carga Horária:</strong><br/>
							<input type='text' readonly name="CargaHoraria" class='form-control' value="<?php echo $carga ?>">
						</div>
					</div>
					<form class="form-horizontal" role="form" action="#" method="post">
						<div class="form-group">
							<div class="col-md-offset-2 col-md-8"><strong>Valor:</strong><br/>
								<input type='text' disabled name="valor_parcela" id='valor' class='form-control' value="<?php echo dinheiroParaBr($pedido['valor']) ?>" >
							</div>	
						</div>
						<div class="form-group">
							<div class="col-md-offset-2 col-md-8"><strong>Forma de Pagamento:</strong><br/>
								<textarea  disabled name="FormaPagamento" class="form-control" cols="40" rows="5"><?php echo txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']); ?> 
								</textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-offset-2 col-md-8"><strong>Verba:</strong><br/>
								<input type='text' disabled name="valor_parcela" id='valor' class='form-control' value="<?php echo $verba['Verba'] ?>" >
							</div>
						</div>
						<form class="form-horizontal" role="form" action="?perfil=emia&p=frm_cadastra_pedidocontratacao_pf" method="post">
							<div class="form-group">
								<div class="col-md-offset-2 col-md-8"><strong>Justificativa:</strong><br/>
									<textarea name="Justificativa" cols="40" rows="5"><?php echo $justif ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-6"><strong>Fiscal:</strong>
									<select class="form-control" name="Fiscal" id="Fiscal">
										<?php opcaoUsuario($_SESSION['idInstituicao'],$emia['fiscal']); ?>
									</select>
								</div>
								<div class="col-md-6"><strong>Suplente:</strong>
								   <select class="form-control" name="Suplente" id="Fiscal">
										<?php opcaoUsuario($_SESSION['idInstituicao'],$emia['suplente']); ?>
								   </select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-8"><strong>Observação:</strong><br/>
									<textarea name="Observacao" cols="40" rows="5"><?php echo $pedido['observacao'] ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-8">
									<input type="hidden" name="action" value="atualizar"  />
									<input type="hidden" name="idPedido" value="<?php echo $pedido['idPedidoContratacao']; ?>"  />
									<input type="submit" class="btn btn-theme btn-lg btn-block" value="Gravar">
								</div>
							</div>
						</form>	
<?php
	if($pedido['estado'] == NULL OR $pedido['estado'] == "" )
	{
?>
						<form class="form-horizontal" role="form" action="?perfil=emia&p=frm_cadastra_pedidocontratacao_pf&id_ped=<?php echo $idPedidoContratacao; ?>" method="post">
							<div class="form-group">
								<div class="col-md-offset-2 col-md-8">
									<input type="hidden" name="enviar"  />
									<input type="submit" class="btn btn-theme btn-lg btn-block" value="Enviar pedido para contratos">
								</div>
							</div>
						</form>
<?php
	}
	else
	{
?>			                                
	<div class="form-group">
		<div class="col-md-offset-2 col-md-8">
			<a href="../pdf/rlt_proposta_emia.php?id=<?php echo $pedido['idPedidoContratacao']; ?>&penal=20" class="btn btn-theme btn-lg btn-block" target="_blank">Gerar proposta</a>
		</div>
	</div>
<?php
	}
?>
					
				
			</div>
		</div>
	</div>
</section>