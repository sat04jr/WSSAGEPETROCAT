<?php
function Traza ($funcion, $texto){
	$traza = true;
	if ($traza) {
		$logFileName='e:\LOGS_WSSAGE\POLARlog_';
		$logFileName.=date("Y_m_d"); //."_".date("H").'h';
		$logFileName.='.txt';
		$fiS = fopen($logFileName, "a");
		fwrite($fiS, "\n" .date("Y-m-d H:i:s") .$funcion .": " .$texto);
		fclose($fiS);
	}
}

function TrazaJR ($funcion, $texto){
	$traza = false;
	if ($traza) {
		$logFileName='e:\LOGS_WSSAGE\POLARlog_';
		$logFileName.=date("Y_m_d"); //."_".date("H").'h';
		$logFileName.='.txt';
		$fiS = fopen($logFileName, "a");
		fwrite($fiS, "\n" .date("Y-m-d H:i:s") .$funcion .": " .$texto);
		fclose($fiS);
	}
}
