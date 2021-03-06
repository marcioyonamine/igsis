<?php

//require '../include/';
   require_once("../funcoes/funcoesConecta.php");
   require_once("../funcoes/funcoesGerais.php");
   require_once("../funcoes/funcoesSiscontrat.php");
   require_once("../funcoes/funcoesFormacao.php");

//CONEXÃO COM BANCO DE DADOS 
   $conexao = bancoMysqli();


//CONSULTA  
$id_ped=$_GET['id'];

$ano=date('Y');

$dataAtual = date("d/m/Y");

$pedido = siscontrat($id_ped);
$pessoa = siscontratDocs($pedido['IdProponente'],1);

$setor = $pedido["Setor"];

$id = $pedido['idEvento'];
$Objeto = $pedido["Objeto"];
$Periodo = $pedido["Periodo"];
$Duracao = $pedido["Duracao"];
$CargaHoraria = $pedido["CargaHoraria"];
$Local = $pedido["Local"];
$ValorGlobal = dinheiroParaBr($pedido["ValorGlobal"]);
$ValorPorExtenso = valorPorExtenso($pedido["ValorGlobal"]);
$FormaPagamento = $pedido["FormaPagamento"];
$Justificativa = $pedido["Justificativa"];
$Fiscal = $pedido["Fiscal"];
$rfFiscal = $pedido["RfFiscal"];
$Suplente = $pedido["Suplente"];
$rfSuplente = $pedido["RfSuplente"];
$NumeroProcesso = $pedido["NumeroProcesso"];
$notaempenho = $pedido["NotaEmpenho"];
$data_entrega_empenho = exibirDataBr($pedido['EntregaNE']);
$data_emissao_empenho = exibirDataBr($pedido['EmissaoNE']);
$ingresso = dinheiroParaBr($pedido['ingresso']);
$ingressoExtenso = valorPorExtenso($pedido['ingresso']);

$grupo = grupos($id_ped);
$integrantes = $grupo["texto"];

//PessoFisica

$Nome = $pessoa["Nome"];
$NomeArtistico = $pessoa["NomeArtistico"];
$EstadoCivil = $pessoa["EstadoCivil"];
$Nacionalidade = $pessoa["Nacionalidade"];
$DataNascimento = exibirDataBr($pessoa["DataNascimento"]);
$RG = $pessoa["RG"];
$CPF = $pessoa["CPF"];
$CCM = $pessoa["CCM"];
$OMB = $pessoa["OMB"];
$DRT = $pessoa["DRT"];
$cbo = $pessoa["cbo"];
$Funcao = $pessoa["Funcao"];
$Endereco = $pessoa["Endereco"];
$Telefones = $pessoa["Telefones"];
$Email = $pessoa["Email"];
$INSS = $pessoa["INSS"];
  
 
 
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$NumeroProcesso em $dataAtual.doc");
echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<body>";
  
echo 		
		"<p align='center'><strong>PROCESSO SEI Nº ".$NumeroProcesso."</strong></p>".
		"<p align='center'><strong>TERMO DE DOAÇÃO</strong></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p>A PREFEITURA DO MUNICÍPIO DE SÃO PAULO, por intermédio da SECRETARIA MUNICIPAL DE CULTURA, neste ato representada  André Sturm, Secretário Municipal de Cultura, doravante denominada donatária e ".$Nome.", portador/a da cédula de identidade RG: ".$RG.", inscrito no CPF: ".$CPF.", residente na ".$Endereco.", denominado/a doador/a, com fundamento no artigo 1º do Decreto nº 40.384/2001, resolvem, firmar o presente termo de doação, mediante as seguintes cláusulas e condições:</p>".
		"<p>&nbsp;</p>".
		"<p><strong>CLÁUSULA 1 - OBJETO</strong></p>".
		"<p>Doação de serviços artísticos para o evento ".$Objeto.", no ".$Local.", no período ".$Periodo.", conforme proposta e cronograma constantes no processo eletrônico.</p>".
		"<p>&nbsp;</p>".
		"<p><strong>CLÁUSULA 2 - OBRIGAÇÕES DO DOADOR</strong></p>".
		"<p>O/a doador/a compromete-se a:</p>".
		"<p>2.1. Executar os serviços no período e horário constantes na proposta de doação, garantindo sua qualidade e adequação aos propósitos do evento.</p>".
		"<p>2.2 Fazer menção dos créditos da Prefeitura da Cidade de São Paulo, Secretaria Municipal de Cultura, Centro Cultural São Paulo, em toda divulgação, escrita ou falada, realizada sobre o evento programado.</p>".
		"<p>&nbsp;</p>".
		"<p><strong>CLÁUSULA 3 - DOS DIREITOS E ENCARGOS DA DONATÁRIA</strong></p>".
		"<p>A donatária:</p>".
		"<p>3.1. Compete o fornecimento da sonorização necessária à realização de espetáculos e dos equipamentos de iluminação disponíveis no local do evento, assim como providências quanto à divulgação de praxe (confecção de cartaz a ser afixado no equipamento cultural e encaminhamento de release à mídia impressa e televisiva).</p>".
		"<p>3.2. Exercer a coordenação e comunicações necessárias, bem como dirimir dúvidas, para o bom cumprimento das obrigações descritas neste termo.</p>".
		"<p>&nbsp;</p>".
		"<p><strong>CLÁUSULA 4 - DISPOSIÇÕES GERAIS</strong></p>".
		"<p>4.1. O/a doador/a, nos termos do artigo 8° do Decreto Municipal n° 40.384/01, declara, sob as penas da lei, que não está em débito com a Fazenda Municipal.</p>".
		"<p>4.2. A presente doação não acarretará ônus para a Municipalidade.</p>".
		"<p>4.3. A donatária fica autorizada a reproduzir, por processo fotográfico ou digital, e a utilizar, sem qualquer ônus, as imagens do evento realizado em anúncio, catálogo, exposição, folder e outras publicações, sem fins lucrativos, nos eventos promovidos e/ou produzidos pela Prefeitura do Município de São Paulo. Essa autorização terá validade a partir da presente assinatura e vigorará pelo prazo previsto no artigo 41 da Lei Federal nº 9.610/98.</p>".
		"<p>4.4. Nos termos do art. 6 do Decreto nº. 54.873/2014, fica designado como fiscal do contrato ".$Fiscal.", RF ".$rfFiscal." e como suplente ".$Suplente.", RF ".$rfSuplente.".</p>".
		"<p>4.5. Fica eleito o foro da Comarca da Capital, através de uma de suas varas da Fazenda Pública, para qualquer procedimento judicial oriundo do presente Termo, com a renúncia de qualquer outro, por mais especial ou privilegiado que seja.</p>".
		"<p>E por estarem justas e pactuadas firmam as Partes o presente Termo, em 4 (quatro) vias de igual teor, forma e data para um só efeito na presença das testemunhas abaixo.</p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p><strong>DONATÁRIA</strong></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p><b>André Luiz Pompeia Sturm<br/>".
			"Secretário Municipal de Cultura</b></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".	
		"<p><strong>DOADOR</strong></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p><b>".$Nome."<br/>".
		"CPF: ".$CPF."</b></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p><strong>TESTEMUNHAS</strong></p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>".
		"<p>&nbsp;</p>";
	echo "</body>";
echo "</html>";	
?>