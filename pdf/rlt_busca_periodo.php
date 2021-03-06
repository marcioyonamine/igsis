<?php

require_once("../funcoes/funcoesConecta.php");
require_once("../funcoes/funcoesGerais.php");
require_once("../funcoes/funcoesSiscontrat.php");

// Definimos o nome do arquivo que será exportado
$arquivo = 'relatorio_igsis.xls';
	
function retornaDataInicio($idEvento)
{ //retorna o período
	$con = bancoMysqli();
	$sql_anterior = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataInicio ASC LIMIT 0,1"; //a data inicial mais antecedente
	$query_anterior = mysqli_query($con,$sql_anterior);
	$data = mysqli_fetch_array($query_anterior);
	$data_inicio = $data['dataInicio'];
	$sql_posterior01 = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataFinal DESC LIMIT 0,1"; //quando existe data final
	$sql_posterior02 = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataInicio DESC LIMIT 0,1"; //quando há muitas datas únicas
	$query_anterior01 = mysqli_query($con,$sql_posterior01);
	$data = mysqli_fetch_array($query_anterior01);
	$num = mysqli_num_rows($query_anterior01);
	
	if(($data['dataFinal'] != '0000-00-00') OR ($data['dataFinal'] != NULL))
	{  //se existe uma data final e que é diferente de NULO
		$dataFinal01 = $data['dataFinal'];	
	}
	
	$query_anterior02 = mysqli_query($con,$sql_posterior02); //recupera a data única mais tarde
	$data = mysqli_fetch_array($query_anterior02);
	$dataFinal02 = $data['dataInicio'];
			
	if(isset($dataFinal01))
	{ //se existe uma temporada, compara com a última data única
		if($dataFinal01 > $dataFinal02)
		{
			$dataFinal = $dataFinal01;
		}
		else
		{
			$dataFinal = $dataFinal02;
		}
	}
	else
	{
		$dataFinal = $dataFinal02;		
	}
	
	if($data_inicio == $dataFinal)
	{ 
		return $data_inicio;
	}
	else
	{
		return $data_inicio;
	}	
}
if(isset($_POST['local']) AND trim($_POST['local']))
{
	$idLocal = trim($_POST['local']);
	$local = " AND idLocal = '$idLocal' ";	
}
else
{
	$local = "";	
}
if(isset($_POST['instituicao']) AND trim($_POST['instituicao']))
{
	$idInstituicao = $_POST['instituicao'];
	$instituicao = " AND idInstituicao = '$idInstituicao' ";	
}
else
{
	$instituicao = "";	
}


	$inicio = exibirDataMysql($_POST['inicio']);
	$final = exibirDataMysql($_POST['final']);	
	$con = bancoMysqli();
	$sql_evento = "SELECT DISTINCT idEvento FROM igsis_agenda WHERE data BETWEEN '$inicio' AND '$final' $instituicao $local  ORDER BY data ASC ";
	$query_evento = mysqli_query($con,$sql_evento);
	$num = mysqli_num_rows($query_evento);
	$i = 0;
	while($evento = mysqli_fetch_array($query_evento))
	{
		$idEvento = $evento['idEvento'];
		$dataInicio = strtotime(retornaDataInicio($idEvento));
		if($dataInicio >= strtotime($inicio) AND $dataInicio <= strtotime($final))
		{
			$event = recuperaDados("ig_evento",$idEvento,"idEvento");
			if($event['dataEnvio'] != NULL AND $event['publicado'])
			{ // se o evento estiver publicado e tiver sido enviado 
				$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado ='1' 
				ORDER BY idPedidoContratacao DESC";
				$query_pedido = mysqli_query($con,$sql_pedido);
				while($pedido = mysqli_fetch_array($query_pedido))
				{
					$usuario = recuperaDados("ig_usuario",$event['idUsuario'],"idUsuario");
					$instituicao = recuperaDados("ig_instituicao",$event['idInstituicao'],"idInstituicao");
					$local = listaLocais($pedido['idEvento']);
					$periodo = retornaPeriodo($pedido['idEvento']);
					$operador = recuperaUsuario($pedido['idContratos']);
					if($pedido['parcelas'] > 1)
					{
						$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
						$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
					}
					else
					{
						$valorTotal = $pedido['valor'];
						$formaPagamento = $pedido['formaPagamento'];
					}

					if ( $pedido ['estado'] == 1 OR $pedido ['estado'] == 2 OR 	$pedido ['estado'] == 3 OR $pedido ['estado'] == 4 OR $pedido ['estado'] == 5 OR $pedido ['estado'] == 6 OR $pedido ['estado'] == 7 OR $pedido ['estado'] == 8 OR $pedido ['estado'] == 9 OR $pedido ['estado'] == 10 OR $pedido ['estado'] == 11 OR $pedido ['estado'] == 13 OR $pedido ['estado'] == 14 OR $pedido ['estado'] == 15) 
					{
						$x[$i]['id']= $pedido['idPedidoContratacao'];
						$x[$i]['objeto'] = retornaTipo($event['ig_tipo_evento_idTipoEvento'])." - ".$event['nomeEvento'];
						if($pedido['tipoPessoa'] == 1)
						{
							$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
							$x[$i]['proponente'] = $pessoa['Nome'];
							$x[$i]['tipo'] = "Física";
						}
						else
						{
							$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
							$x[$i]['proponente'] = $pessoa['RazaoSocial'];
							$x[$i]['tipo'] = "Jurídica";
						}
					$x[$i]['local'] = substr($local,1);
					$x[$i]['instituicao'] = $instituicao['sigla'];
					$x[$i]['periodo'] = $periodo;
					$x[$i]['status'] = $pedido['estado'];	
					$x[$i]['operador'] = $operador['nomeCompleto'];		
					$i++;
					}
				}
			}
		}
	}
	$x['num'] = $i;
	if($num > 0)
	{ 
		$html = '';
		$html .= '<table>';
		$html .= '<tr>';
		$html .= '<td>Codigo do Pedido</td>';
		$html .= '<td>Proponente</td>';
		$html .= '<td>Tipo</td>';
		$html .= '<td>Objeto</td>';
		$html .= '<td width="20%">Local</td>';
		$html .= '<td>Instituição</td>';
		$html .= '<td>Periodo</td>';
		$html .= '<td>Status</td>';
		$html .= '<td>Operador</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		
		
			$data=date('Y');
			for($h = 0; $h < $x['num']; $h++)
			{
				$status = recuperaDados("sis_estado",$x[$h]['status'],"idEstado");
				
				$html .= '<tr><td class="list_description">'.$x[$h]['id'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['proponente'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['tipo'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['objeto'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['local'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['instituicao'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['periodo'].'</td> ';
				$html .= '<td class="list_description">'.$status['estado'].'</td> ';
				$html .= '<td class="list_description">'.$x[$h]['operador'].'</td> </tr>';
			}		
		$html .= '</table>';
	}
		
// Configurações header para forçar o download
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );
// Envia o conteúdo do arquivo
echo $html;
exit;
?>