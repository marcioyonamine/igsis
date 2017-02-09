﻿<?php include 'include/menu.php'; ?>
<section id="list_items" class="home-section bg-white">
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
						<h2>Comunicação</h2>
						<h4>Todos os eventos</h4>
					</div>
				</div>
			</div> 		
			<div class="table-responsive list_info">
				<table class='table table-condensed'>
					<thead>
						<tr class='list_menu'>
							<td width='10%'>Numero IG</td>
							<td>Nome de Evento</td>
							<td>Enviador por</td>
							<td>Data/Início</td>
							<td>Status</td>
						</tr>
					</thead>		
					<tbody>
				<?php					
					$idInstituicao = $_SESSION['idInstituicao'];
					//verifica a página atual caso seja informada na URL, senão atribui como 1ª página
					$pagina = (isset($_GET['pagina']))? $_GET['pagina'] : 1;
												
					$con = bancoMysqli();
					$sql_busca_dic = "SELECT * FROM ig_comunicacao WHERE idInstituicao = '$idInstituicao' ORDER BY idCom DESC";
					$query_busca_dic = mysqli_query($con,$sql_busca_dic);
					//conta o total de itens
					$total = mysqli_num_rows($query_busca_dic);
					
					//seta a quantidade de itens por página
					$registros = 100;
				   
					//calcula o número de páginas arredondando o resultado para cima
					$numPaginas = ceil($total/$registros);
					
					//variavel para calcular o início da visualização com base na página atual
					$inicio = ($registros*$pagina)-$registros;
					
					//seleciona os itens por página
					$sql_busca_dic = "SELECT * FROM ig_comunicacao WHERE idInstituicao = '$idInstituicao' ORDER BY idCom DESC limit $inicio,$registros ";
					$query_busca_dic = mysqli_query($con,$sql_busca_dic);
					 //conta o total de itens
					$total = mysqli_num_rows($query_busca_dic);
					
					while($evento = mysqli_fetch_array($query_busca_dic))
					{ 
						$event = recuperaDados("ig_evento",$evento['ig_evento_idEvento'],"idEvento");
						$nome = recuperaUsuario($event['idUsuario']);
						$chamado = recuperaAlteracoesEvento($evento['ig_evento_idEvento']);
				?>			
						<tr>
							<td><?php echo $evento['ig_evento_idEvento'] ?></td>
							<td><a href="?perfil=comunicacao&p=editar&idCom=<?php echo $evento['idCom']  ?>"><?php echo $evento['nomeEvento'] ?></a> [<?php 
						if($chamado['numero'] == '0')
						{
							echo "0";
						}
						else
						{
							echo "<a href='?perfil=chamado&p=evento&id=".$evento['ig_evento_idEvento']."' target='_blank'>".$chamado['numero']."</a>";	
						}
							?>]
							</td>
							<td><?php echo $nome['nomeCompleto'] ?></td>
							<td><?php echo retornaPeriodo($evento['ig_evento_idEvento']) ?></td>
							<td><?php 
								if ($evento['editado'] == 1) 
								{ 
									echo "Editado <br/>"; 
								} 
								if ($evento['revisado'] == 1) 
								{
									echo "Revisado <br/>";
								}
								if ($evento['site'] == 1) 
								{
									echo "Site <br/>";
								}	
								if ($evento['publicacao'] == 1) 
								{
									echo "Impresso <br/>";
								}
								if ($evento['foto'] == 1) 
								{
									echo "Foto";
								}
							?></td>
						</tr>
				<?php
					}
				?>
						<tr>
							<td colspan="5" bgcolor="#DEDEDE">
							<?php
								//exibe a paginação
								echo "<strong>Páginas</strong>";
								for($i = 1; $i < $numPaginas + 1; $i++) 
								{
									echo "<a href='?perfil=comunicacao&p=filtro_resultado2?pagina=$i'> [".$i."]</a> ";
								}
							?>
							</td>
						</tr>	
					</tbody>
				</table>
			</div>
		</div>
	</section>