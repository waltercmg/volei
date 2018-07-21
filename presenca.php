
<?php
include "util.php";
?>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<?php

$torneios = getEstatisticasDadosObj();
$jogadores = getJogadoresObj();

echo"<table>";
echo "<tr><td></td>";
foreach($jogadores as $jog){
    echo "<td>".$jog->abreviatura."</td>";
}
echo "</tr>";        
for($i=0;$i<count($torneios);$i++){
    $torneio = $torneios[$i];
    echo "<tr><td>".$torneio->data."</td>";
    foreach($jogadores as $jog){
        echo "<td align=center>";
        if(array_key_exists($jog->abreviatura,$torneio->jogadores)){
            echo "<img src='ok.png' height=15>";
            $jog->presente += 1;
        }else{
            echo "<img src='nok.png' height=15>";
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "<tr><td></td>";
foreach($jogadores as $jog){
    echo "<td align=center>".$jog->presente."</td>";
}
echo "</tr>";
echo "</table>";


?>