
<?php
include "util.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>


<?php

$id_torneio = getTorneioDia();
//$id_torneio=65;

echo "<table align=center width=90%>";
echo "<tr><td align=center>";

$query = "select abreviatura,tipo,beneficios from jogador, lista_presenca where ".
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "jogador.id_jogador not in ".
        "(select id_jogador from atual) and ".
        "jogador.id_jogador not in ".
        "(select id_jog_revez from atual where id_jog_revez is not null) and ".
        "id_torneio=".$id_torneio." order by hora_chegada;";
imprimeResultado("ESTAO NO BANCO",$query);

echo "</td></tr><tr><td align=center>";

$query = "(select id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
        "abreviatura, convidante, beneficios from ".
        "lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ". 
        "lista_presenca.ativo=true and ". 
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "jogador.id_jogador not in ". 
        "(select id_jogador from atual)  and ".
        "jogador.id_jogador not in ". 
        "(select id_jog_revez from atual where id_jog_revez is not null) ".
        "order by beneficios, hora_chegada) ".
        "union all ".
        "(select lista_presenca.id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
        "abreviatura, convidante, beneficios from ". 
        "atual, lista_presenca, jogador where ". 
        "(lista_presenca.id_jogador=atual.id_jogador or lista_presenca.id_jogador=atual.id_jog_revez) and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
imprimeResultado("PROXIMOS CANDIDATOS",$query);

echo "</td></tr><tr><td align=center>";

$query = "select id,to_char(inicio, 'HH24:MI:SS') as hora , ".
"time, vitorioso as venceu, jogador1,jogador2,jogador3, ".
"jogador4,jogador5,jogador6 from ".
"( ".
"(select id_partida as id,  ".
"hora_inicio as inicio, 'A' as time, vitorioso,  ".
"joga1.abreviatura as jogador1, ". 
"joga2.abreviatura as jogador2, ". 
"joga3.abreviatura as jogador3, ". 
"joga4.abreviatura as jogador4, ". 
"joga5.abreviatura as jogador5, ". 
"joga6.abreviatura as jogador6 ".
"from partida ".
"inner join time on ".
"partida.id_time_a = time.id_time  ".
"inner join jogador joga1 on ".
"time.id_jogador1=joga1.id_jogador ".
"inner join jogador joga2 on ".
"time.id_jogador2=joga2.id_jogador ".
"inner join jogador joga3 on ".
"time.id_jogador3=joga3.id_jogador ".
"inner join jogador joga4 on ".
"time.id_jogador4=joga4.id_jogador ".
"inner join jogador joga5 on ".
"time.id_jogador5=joga5.id_jogador ".
"inner join jogador joga6 on ".
"time.id_jogador6=joga6.id_jogador ".
"where id_torneio=".$id_torneio.") ".
"union  ".
"(select null as id, hora_inicio as inicio, 'B' as time, vitorioso, ". 
"joga1.abreviatura as jogador1, ". 
"joga2.abreviatura as jogador2, ". 
"joga3.abreviatura as jogador3, ". 
"joga4.abreviatura as jogador4, ".
"joga5.abreviatura as jogador5, ". 
"joga6.abreviatura as jogador6 ".
"from partida ".
"inner join time on ".
"partida.id_time_b = time.id_time ". 
"inner join jogador joga1 on ".
"time.id_jogador1=joga1.id_jogador ".
"inner join jogador joga2 on ".
"time.id_jogador2=joga2.id_jogador ".
"inner join jogador joga3 on ".
"time.id_jogador3=joga3.id_jogador ".
"inner join jogador joga4 on ".
"time.id_jogador4=joga4.id_jogador ".
"inner join jogador joga5 on ".
"time.id_jogador5=joga5.id_jogador ".
"inner join jogador joga6 on ".
"time.id_jogador6=joga6.id_jogador ".
"where id_torneio=".$id_torneio.
")) as time ".
"order by inicio desc,time;";
imprimeResultado("ULTIMAS PARTIDAS",$query);

echo "</td></tr><tr><td align=center>";

$query = "select abreviatura, tipo, hora_chegada from jogador, ".
"lista_presenca where jogador.id_jogador=lista_presenca.id_jogador ".
"and lista_presenca.id_torneio=".$id_torneio." order by hora_chegada;";
imprimeResultado("ORDEM DE CHEGADA",$query);

getEstatisticas();

?>
</td></tr>
<tr>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
    </td>
</tr>
