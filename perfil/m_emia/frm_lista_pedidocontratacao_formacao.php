<?php
	include 'includes/menu.php';
	$linha_tabela_lista = siscontratLista(4,$_SESSION['idInstituicao'],100,1,"DESC",1); //esse gera uma array com os pedidos
	$link="index.php?perfil=emia&p=frm_edita_pedidocontratacaopf&id_ped=";
?>
<section id="list_items">
	<div class="container">
		<div class="sub-title">PEDIDO DE CONTRATAÇÃO DE PESSOA FÍSICA</div>
		<div class="table-responsive list_info">
			<table class="table table-condensed">
				<thead>
					<tr class="list_menu">
					<td>Codigo do Pedido</td>
					<td>Proponente</td>
					<td>Objeto</td>
					<td>Local</td>
					<td>Periodo</td>
					<td>Status</td>
					</tr>
				</thead>			
				<tbody>
<?php
	$data=date('Y');
	for($i = 0; $i < count($linha_tabela_lista); $i++)
	{
		$linha_tabela_pedido_contratacaopf = recuperaDados("sis_pessoa_fisica",$linha_tabela_lista[$i]['IdProponente'],"Id_PessoaFisica");	 
		echo "<tr><td class='lista'> <a href='".$link.$linha_tabela_lista[$i]['idPedido']."'>".$linha_tabela_lista[$i]['idPedido']."</a></td>";
		echo '<td class="list_description">'.$linha_tabela_pedido_contratacaopf['Nome'].					'</td> ';
		echo '<td class="list_description">'.$linha_tabela_lista[$i]['Objeto'].						'</td> ';
		echo '<td class="list_description">'.$linha_tabela_lista[$i]['Local'].				'</td> ';
		echo '<td class="list_description">'.$linha_tabela_lista[$i]['Periodo'].						'</td> ';
		echo '<td class="list_description">'.$linha_tabela_lista[$i]['Status'].						'</td> </tr>';
	}
?>
				</tbody>
			</table>
		</div>
	</div>
</section>