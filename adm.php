
<?php
include "util.php";
?>

<style type="text/css">
@font-face {
    font-family: myFirstFont;
    src: courier;
}
    
</style>

<?php

$id_torneio = getTorneioDia();

echo "<br><br>ATUAL<br><br>";
$query = "select * from atual, jogador, lista_presenca where atual.id_jogador=".
        "lista_presenca.id_jogador and lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.id_torneio = atual.id_torneio and atual.id_torneio= ".$id_torneio." order by hora_chegada";
imprimeResultado($query);

echo "<br><br>ESTAO NO BANCO<br><br>";
$query = "select * from jogador, lista_presenca where ".
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "jogador.id_jogador not in ".
        "(select id_jogador from atual) and ".
        "jogador.id_jogador not in ".
        "(select id_jog_revez from atual where id_jog_revez is not null) and ".
        "id_torneio=".$id_torneio." order by hora_chegada;";
imprimeResultado($query);

echo "<br><br>PROXIMOS CANDIDATOS<br><br>";
$query ="(select id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
        "abreviatura, convidante from ".
        "lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ". 
        "lista_presenca.ativo=true and ". 
        "jogador.id_jogador not in ". 
        "(select id_jogador from atual)  and ".
        "jogador.id_jogador not in ". 
        "(select id_jog_revez from atual where id_jog_revez is not null) ".
        "order by beneficios, hora_chegada) ".
        "union all ".
        "(select lista_presenca.id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
        "abreviatura, convidante from ". 
        "atual, lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=atual.id_jogador and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
imprimeResultado($query);


echo "<br><br>PARTIDA<br><br>";
$query = "select * from partida;";
imprimeResultado($query);

echo "<br><br>TIME<br><br>";
$query = "select * from time;";
imprimeResultado($query);

echo "<br><br>LISTA_PRESENCA<br><br>";
$query = "select * from jogador, lista_presenca where jogador.id_jogador=lista_presenca.id_jogador order by hora_chegada;";
imprimeResultado($query);

echo "<br><br>JOGADOR<br><br>";
$query = "select * from jogador;";
imprimeResultado($query);

echo "<br><br>TORNEIO<br><br>";
$query = "select * from torneio;";
imprimeResultado($query);



?>