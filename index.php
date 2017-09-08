<?php
include "util.php";
?>

<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<table align=center width="90%" valign=middle>
<tr>
<td align=center>
<input type="button" class="botaoMenu" onclick="location.href = 'jogadores.php'" value="JOGADORES">
<br><br>
</td>
</tr>
<tr>
<td align=center>
<input type="button" class="botaoMenu" onclick="location.href = 'checkin.php'" value="CHECK IN">
<br><br>
</td>
</tr>
<?php
if(isPartidaEmAndamento()){?>
    <tr>
    <td align=center>
    <form name="frm" id="frm" action="partida.php" method="post">
    <input type="hidden" name="acao" id="acao" value="ATUAL">
    <input type="button" class="botaoMenu" onclick="javascript:document.forms.frm.submit();" value="PARTIDA EM ANDAMENTO">
    </form>
    <br><br>
    </td>
    </tr>
<?php
}else{?>
    <tr>
    <td align=center>
    <input type="button" class="botaoMenu" onclick="location.href = 'candidatos.php'" value="NOVA PARTIDA">
    <br><br>
    </td>
    </tr>    
<?php
}
?>
</tr>
<tr>
<td align=center>
<input type="button" class="botaoMenu" onclick="location.href = 'dados.php'" value="DADOS DO DIA">
<br><br>
</td>
</tr>
<tr>
<td align=center>
<br><br><br><br><br><br>
<!--<input type="button" class="botaoMenu" onclick="location.href = 'zerar.php'" value="ZERAR">-->
</td>
</tr>
</table>
