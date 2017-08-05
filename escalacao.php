
<?php
include "util.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<?php

$limite = getQtdeJogadoresPorTime()*2;
$id_torneio = intval($_POST["id_torneio"]);

//echo "limite: " . $limite."<br>";
$clausulaIn = "(";
for ($x = 0; $x <$limite; $x++){
    $radio = "jog_".$x;
    $id_jogador = intval($_POST[$radio]);
    $clausulaIn .= $id_jogador . ",";
}

$clausulaIn .= ")";
//echo "<br>CLAUSULA:". $clausulaIn;
$clausulaIn = str_replace(",)",")",$clausulaIn);
$escalacao = escalarTimes($clausulaIn);
$nota_geral_a = 0;
$nota_geral_b = 0;

?>

<table border=0 align=center width="90%">
    <tr>
        <td align=center>
            <font size=30 align=center>TIME A</font>
        </td>
        <td align=center>
            <font size=30 align=center>TIME B</font>
        </td>
    </tr>
    
<form name="frm" id="frm" action="partida.php" method="post">
    <input type="hidden" name="limite" value="<?=$limite?>">
    <input type="hidden" name="id_torneio" value="<?=$id_torneio?>">
    <input type="hidden" name="acao" id="acao" value="NOVA">
<?php

for($x=0;$x<count($escalacao[0]);$x++){
    $nota_a = $escalacao[0][$x]->nota;
    $nota_b = $escalacao[1][$x]->nota;   
    
    echo "<tr><td align=center>".getHTMLJogadorEscalado($escalacao[0][$x],"A",$x).
         "</td><td align=center>".getHTMLJogadorEscalado($escalacao[1][$x],"B",$x).
         "</td></tr>";
    
    $nota_geral_a += $nota_a;
    $nota_geral_b += $nota_b;
}
echo "<tr><td align=center>TOTAL: " . $nota_geral_a."</td>".
    "<td align=center>TOTAL: " . $nota_geral_b."</td></tr>";
?>
</form>
<table border=0 align=center width="90%">
<tr>
    <td colspan=3 align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
        <input type="button" class="botaoMenu" onclick="javascript:document.forms.frm.submit();" value="INICIAR PARTIDA">
        </td>
</tr>





