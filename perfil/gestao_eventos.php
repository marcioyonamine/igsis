﻿<?php
	//include para contratos
	if(isset($_GET['p']))
	{
		$p = $_GET['p'];	
	}
	else
	{
		$p = "index";
	}
	include "../funcoes/funcoesSiscontrat.php";	
	include "m_gestao_eventos/".$p.".php";
?>