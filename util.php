<?php

include "classes.php";

//$conn = pg_connect("dbname=postgres");
//sudo service postgresql start
//$conn = pg_connect("host=ec2-107-20-186-238.compute-1.amazonaws.com dbname=dep3sfi55ndpdb user=orwbscrzwbuznv password=ba6d32ffbc1821ce1e9261ed41a79622e54632a746e7b4ce23ab6b213e8d574b");
$conn = pg_connect("host=ec2-3-214-121-14.compute-1.amazonaws.com dbname=ddi9urr0tffnvv user=fkzyeszpvbqplb password=5e0a0bfea6465a72c478432de40802686e4ae04a1aadb9f28ad956798ca8ff3e");
//psql -h ec2-107-20-186-238.compute-1.amazonaws.com -p 5432 -U orwbscrzwbuznv -W ba6d32ffbc1821ce1e9261ed41a79622e54632a746e7b4ce23ab6b213e8d574b -d dep3sfi55ndpdb
$mensalistas = array();

function getTorneioDia(){
    global $conn;
    $retorno = 0;
    setTimeZone();
    $result = pg_query($conn, "SELECT * FROM torneio where data=current_date;");
    if($result){
        while ($row = pg_fetch_array($result)) {
            $retorno = $row['id_torneio'];
            break;
        }
    }
    //echo "TORNEIODIA=".$retorno;
    return $retorno;
}

function getHTMLJogador($jogador, $ordem){
    $texto = $jogador->abreviatura;
    if($jogador->ativo=="t"){
        $texto = $ordem . "-". $jogador->abreviatura;
        $classe = "botao1";
    }elseif($jogador->ativo=="f"){
        $texto = $ordem . "-". $jogador->abreviatura;
        $classe = "botao2";
    }else{
        $classe = "botao3";
    }
    if($jogador->tipo=="C"){
        $texto.="*";
    }
    $retorno = "<input type=\"button\" class=". $classe . " value=\"".
                $texto . "\" onclick=\"javacript:atualizar(".
                $jogador->id_jogador . ",'". $jogador->ativo ."','". $jogador->presente ."');\">";

    if($jogador->ativo=="t"){
        $retorno .= "<input type=\"button\" class=\"botaoExcluir\" value=\"X\"".
                    "onclick=\"javacript:remover(". $jogador->id_jogador .
                    ",'". $jogador->ativo."');\">";
    }
    return $retorno;
}

function setTimeZone(){
    global $conn;
    $query = "set timezone=\"America/Sao_Paulo\";";
    $result = pg_query( $conn,$query);
    
}

function atualizarCheckIn($id_torneio, $id_jogador, $ativo, $presente){
    global $conn;
    echo "ID TORNEIO a: ".$id_torneio;
    setTimeZone();
    if($id_torneio==0){
        echo "<br>INSERIR";
        $result = pg_query( $conn, "INSERT INTO TORNEIO (nome, data) VALUES ('DIA: '|| current_date, current_date) RETURNING id_torneio;");
        //echo "<br>RESULTADO01: ".$result;
        $row = pg_fetch_row($result);
        $id_torneio = intval($row[0]);
        apagarAtual();
    }
    
    echo "<br>ID TORNEIO b: ".$id_torneio;
    $params = array($id_torneio, $id_jogador);
    echo "<br>PRESENTE:".$presente;
    if($ativo != ""){
        if($presente){
            //echo "<br>ATUALIZAR ID JOG: ".$id_jogador;
            $params = array($id_torneio, $id_jogador, $ativo);
            $result = pg_query_params( $conn, 'UPDATE LISTA_PRESENCA SET ATIVO=$3 WHERE ID_TORNEIO=$1 AND ID_JOGADOR=$2;', $params);
        }else{
            //echo "<br>INSERIR JOG ".$id_torneio." - ".$id_jogador;
            $result = pg_query_params( $conn, 'INSERT INTO LISTA_PRESENCA (ID_TORNEIO, ID_JOGADOR, HORA_CHEGADA, ATIVO) VALUES ($1,$2,now(),true);', $params);    
            $row = pg_fetch_row($result);
            $id_torneio = intval($row[0]);
            //echo "<br>RESULTADO INSERT: ".$id_torneio;
        }
    }else{
        $params = array($id_torneio, $id_jogador);
        $result = pg_query_params( $conn, 'DELETE FROM LISTA_PRESENCA WHERE ID_TORNEIO=$1 AND ID_JOGADOR=$2;', $params);
    }
}

function getQtdePresentes($id_torneio){
    global $conn;
    $retorno = array();
    $qtdeMensalistas = 0;
    $qtdeConvidados = 0;
    setTimeZone();
    $consulta = "SELECT tipo, count(tipo) as quant FROM ".
    "lista_presenca, jogador ".
    "where lista_presenca.id_torneio=".$id_torneio." and ".
    "lista_presenca.id_jogador=jogador.id_jogador and ".
    "lista_presenca.ativo=true ".
    "group by tipo;";
    //echo "<br>QTDE_PRRSENTES:". $consulta;
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            if($row['tipo']=="M"){
                $qtdeMensalistas = $row['quant'];
            } elseif ($row['tipo']=="C"){
                $qtdeConvidados = $row['quant'];
            }
        }
    }
    array_push($retorno, $qtdeMensalistas);
    array_push($retorno, $qtdeConvidados);
    return $retorno;
    
}

function getHTMLJogadorEscalado($jogador,$time,$ordem){
    $texto = $jogador->abreviatura."(".$jogador->nota.")";
    if($jogador->tipo=="C"){
        $texto.="*";
    }
    if($time=="A")
        $classe="time_a";
    else
        $classe="time_b";
        
    $retorno = "<input type=\"hidden\" name=\"jog_".$time. $ordem . "\" ".
                "value=" . $jogador->id_jogador . ">".
                "<input type=\"hidden\" name=\"abr_".$time. $ordem . "\" ".
                "value=" . $jogador->abreviatura . ">".
                "<input type=\"hidden\" name=\"tip_".$time. $ordem . "\" ".
                "value=" . $jogador->tipo . ">".
                "<input type=\"hidden\" name=\"not_".$time. $ordem . "\" ".
                "value=" . $jogador->nota . ">".
                "<font class=\"".$classe."\">".$texto."</font>";
    
    return $retorno;
}

function iniciarPartida($id_torneio, $time_a,$time_b){
    global $conn;
    
    $id_time_a = inserirTime($time_a);
    $id_time_b = inserirTime($time_b);
    $array = inserirPartida($id_torneio, $id_time_a, $id_time_b);
    $id_partida = $array[0];
    $consulta = "select * from atual;";
    $result = pg_query($conn, $consulta);
    if($result){
        $row = pg_fetch_array($result);
        if(pg_num_rows($result)>0){
            atualizarBeneficios($id_torneio, array_merge($time_a,$time_b));
            apagarAtual();
            inserirAtual($id_torneio,$id_partida,$time_a,$time_b);
        }else{
            inserirAtual($id_torneio,$id_partida,$time_a,$time_b);
        }
    }
    return $array;
}

function atualizarBeneficios($id_torneio, $jogadores){
    global $conn;
    
    $arrayQtPresentes = getQtdePresentes($id_torneio);
    $qtdeMenPresentes = $arrayQtPresentes[0];
    if($qtdeMenPresentes > 2*getQtdeJogadoresPorTime()){
        $clausulaIn="";
        for($x=0;$x<count($jogadores);$x++){
            if($x>0)
                $clausulaIn.=",";
           $clausulaIn .= $jogadores[$x]."";
        }
        
        $query = "select id_jogador, id_jog_revez, venceu ".
                "from atual where id_jogador in (".$clausulaIn.") ".
                "or id_jog_revez in (".$clausulaIn.");";
        //echo $query;
        $result = pg_query($conn, $query);
        $x=0;
        $clausulaIn = "";
        while ($row = pg_fetch_array($result)) {
            if($row['venceu']!=true || $qtdeMenPresentes > 3*getQtdeJogadoresPorTime()){
                if($x>0)
                    $clausulaIn.=",";
                $clausulaIn.=$row['id_jogador'];
                if($row['id_jog_revez']!=null)
                    $clausulaIn.=",".$row['id_jog_revez'];
                $x++;
            }
        }
        if($clausulaIn != ""){
            $query = "update lista_presenca set beneficios=beneficios+1 ".
                    "where id_jogador in (".$clausulaIn.")";
        }
        $result = pg_query($conn, $query);
        
    }
}


function atualizarBeneficios_OLD($id_torneio, $jogadores){
    global $conn;
    
    $arrayQtPresentes = getQtdePresentes($id_torneio);
    $qtdeMenPresentes = $arrayQtPresentes[0];
    if($qtdeMenPresentes > 2*getQtdeJogadoresPorTime()){
        $clausulaIn="";
        for($x=0;$x<count($jogadores);$x++){
            if($x>0)
                $clausulaIn.=",";
           $clausulaIn .= $jogadores[$x]."";
        }
        
        $query = "select id_jogador, id_jog_revez, venceu from atual where id_jogador in (".$clausulaIn.") or id_jog_revez in (".$clausulaIn.");";
        //echo $query;
        $result = pg_query($conn, $query);
        $qtdeJogPermanecem = pg_num_rows($result);
        //echo "<br>PERMANECEM: ". $qtdeJogPermanecem;
        if($qtdeJogPermanecem>0 && $qtdeJogPermanecem!=6){
            $x=0;
            $clausulaIn = "";
            while ($row = pg_fetch_array($result)) {
                if($qtdeJogPermanecem>6){
                    if($row['venceu']!=true){
                        if($x>0)
                            $clausulaIn.=",";
                        $clausulaIn.=$row['id_jogador'];
                        if($row['id_jog_revez']!=null)
                            $clausulaIn.=",".$row['id_jog_revez'];
                    }
                }else{
                    if($x>0)
                        $clausulaIn.=",";
                    $clausulaIn.=$row['id_jogador'];
                    if($row['id_jog_revez']!=null)
                            $clausulaIn.=",".$row['id_jog_revez'];
                }
                $x++;
            }
            $query = "update lista_presenca set beneficios=beneficios+1 ".
                    "where id_jogador in (".$clausulaIn.")";
            //echo "<br>QUERY: ". $query;
         
            $result = pg_query($conn, $query);
        }
    }
}

function encerrarPartida($id_partida, $vitorioso){
    global $conn;
    atualizarVitoriosoPartida($id_partida, $vitorioso);
    atualizarVitoriosoAtual($vitorioso);
    header("Location: index.php");
}

function apagarAtual(){
    global $conn;
    
    $query = "delete from atual;";
    $result = pg_query($conn, $query);
}

function inserirAtual($id_torneio, $id_partida, $time_a, $time_b){
    global $conn;
    
    $query = "insert into atual (id_torneio, id_partida, id_jogador, time, id_jog_revez) ".
            "values ($1,$2,$3,$4,".
            "(select id_jog_revez from lista_presenca where lista_presenca.id_torneio=$5 and id_jogador=$6));";
    for($x=0;$x<count($time_a);$x++){
        $params = array($id_torneio, $id_partida, $time_a[$x], "A", $id_torneio, $time_a[$x]);
        $result = pg_query_params($conn, $query, $params);
     }
     for($x=0;$x<count($time_b);$x++){
        $params = array($id_torneio, $id_partida, $time_b[$x], "B", $id_torneio, $time_b[$x]);
        $result = pg_query_params($conn, $query, $params);
     }
}

function inserirTime($time){
    global $conn;
    $query = "insert into time (";
    for($x=0;$x<count($time);$x++){
        if($x>0){
            $query.=",";
            $values.=",";
        }
       $query .= "id_jogador".($x+1);
       $values .= "$".($x+1)."";
       //$values .= $time[$x];
    }
    $query.=") values (".$values.") returning id_time;";
    $result = pg_query_params($conn, $query, $time);
    $row = pg_fetch_row($result);
    $id_time = intval($row[0]);
    return $id_time;
}

function atualizarVitoriosoAtual($vitorioso){
    global $conn;
    $query = "update atual set venceu=true where time=$1;";
    $params = array($vitorioso);
    $result = pg_query_params($conn, $query, $params);
    
}

function atualizarVitoriosoPartida($id_partida, $vitorioso){
    global $conn;
    if($vitorioso == "A"){
        $returning = "id_time_a";
    }elseif($vitorioso == "B"){
        $returning = "id_time_b";
    }
    $query = "update partida set vitorioso=$1 where id_partida=$2 returning ".$returning.";";
    //echo $id_partida."-".$vitorioso;
    $params = array($vitorioso, $id_partida);
    $result = pg_query_params($conn, $query, $params);
    $row = pg_fetch_row($result);
    $id_time_vencedor = intval($row[0]);
    return $id_time_vencedor;
    
}

function inserirPartida($id_torneio, $id_time_a, $id_time_b){
    global $conn;
    setTimeZone();
    $query = "insert into partida (id_torneio, id_time_a, id_time_b, hora_inicio) ".
            "values ($1, $2, $3, now()) returning id_partida, hora_inicio;";
    //echo "PARAMS: ".$id_torneio."-".$id_time_a."-".$id_time_b;
    $params = array($id_torneio, $id_time_a, $id_time_b);
    $result = pg_query_params($conn, $query, $params);
    $row = pg_fetch_row($result);
    $id_partida = intval($row[0]);
    $hora_inicio = $row[1];
    return array($id_partida, $hora_inicio);
}

function getHTMLJogadorCandidato($jogador,$ordem,$checked){
    $texto = $jogador->abreviatura;
    if($jogador->tipo=="C"){
        $texto.="*";
    }
    $retorno = "<input type=\"radio\" name=\"jog_". $ordem . "\" ".
                "id=\"el08\" value=" . $jogador->id_jogador . " " . $checked .">".
                "<label for=\"el08\"><font class=\"botao1\">".$texto."</font></label>";
                
                //"<input type=\"text\" disabled=true onclick=\"javascript:alert('');\" class=\"botao1\" value=\"" . $texto . "\">";
    return $retorno;
}

function getJogador($id_jogador){
    global $conn;
    $retorno = null;
    $consulta = "select id_jogador, abreviatura, tipo from ".
                "jogador where id_jogador=$1;";  
    //echo $consulta;s    
    $params = array($id_jogador);
    $result = pg_query_params($conn, $consulta, $params);
    if($result){
        $row = pg_fetch_array($result);
        $jog = new jogador();
        $jog->id_jogador = $row['id_jogador'];
        $jog->abreviatura = $row['abreviatura'];
        $jog->tipo = $row['tipo'];
        $retorno = $jog;
    }
    return $retorno;
}


function getListaPresencaDia($id_torneio){
    global $conn;
    $retorno = array();
    if($id_torneio=="")
        $id_torneio=0;
    /*$consulta = "(select jogador.id_jogador, abreviatura, ativo, tipo, hora_chegada from ".
                "torneio, jogador, lista_presenca where ".
                "lista_presenca.id_torneio=".$id_torneio." and ".
                "lista_presenca.id_jogador=jogador.id_jogador ".
                "order by lista_presenca.hora_chegada, jogador.abreviatura) ".
                "union ALL".
                "(select id_jogador, abreviatura, null, tipo, null from ".
                "jogador where ".
                "id_jogador not in ".
                "(select lista_presenca.id_jogador from ".
                "lista_presenca where ".
                "lista_presenca.id_torneio=".$id_torneio.") order by abreviatura);"; */          
    $consulta = "(select jogador.id_jogador, abreviatura, ativo, tipo, hora_chegada from ".
                "jogador, lista_presenca where ".
                "lista_presenca.id_torneio=".$id_torneio." and ".
                "lista_presenca.id_jogador=jogador.id_jogador ".
                "order by lista_presenca.hora_chegada, jogador.abreviatura) ".
                "union ALL".
                "(select id_jogador, abreviatura, null, tipo, null from ".
                "jogador where ".
                "id_jogador not in ".
                "(select lista_presenca.id_jogador from ".
                "lista_presenca where ".
                "lista_presenca.id_torneio=".$id_torneio.") order by abreviatura);";
    //echo $consulta;
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->ativo = $row['ativo'];
            $jog->tipo = $row['tipo'];
            //echo "<br>".$row['abreviatura'];
            if($row['hora_chegada'] != null)
                $jog->presente = true;
            else
                $jog->presente = false;
            array_push($retorno, $jog);
        }
    }
    return $retorno;
}

function getMaxJogadoresSemRevezar(){
    return 12;
}

function getQtdeJogadoresPorTime(){
    return 6;
}

function zerar(){
    global $conn;
    $query = "DELETE FROM ATUAL;";
    $result = pg_query( $conn, $query);
    $query = "DELETE FROM PARTIDA;";
    $result = pg_query( $conn, $query);
    $query = "DELETE FROM TIME;";
    $result = pg_query( $conn, $query);
    $query = "DELETE FROM LISTA_PRESENCA;";
    $result = pg_query( $conn, $query);
    $query = "DELETE FROM TORNEIO;";
    $result = pg_query( $conn, $query);

}

function apagarRevezamentos($id_torneio){
    global $conn;
    setTimeZone();
    $query = "update lista_presenca set id_jog_revez = null where ".
             "id_torneio = ".$id_torneio.";";
    $result = pg_query($conn, $query);
    
}

function atualizarRevezamentos($id_torneio){
    global $conn;
    $retorno = array();
    
    $arrayQtPresentes = getQtdePresentes($id_torneio);
    $qtdeMenPresentes = $arrayQtPresentes[0];
    $qtdeConPresentes = $arrayQtPresentes[1];
    $qtdeMaxJogadoresSemRevezar = getMaxJogadoresSemRevezar();
    $qtdePresentes = $qtdeMenPresentes + $qtdeConPresentes;
    
    if($qtdePresentes > $qtdeMaxJogadoresSemRevezar){
        if($qtdeMenPresentes >= $qtdeMaxJogadoresSemRevezar){
            $convidadosNaoPrecisamRevezar = 0;
        }else{
            $convidadosNaoPrecisamRevezar = $qtdeMaxJogadoresSemRevezar-$qtdeMenPresentes;
        }
    }else{
        $convidadosNaoPrecisamRevezar = $qtdeConPresentes;
    }
    /*echo "<br>PRE: ".$qtdeMenPresentes;
    echo "<br>CON: ".$qtdeConPresentes;
    echo "<br>DIF: ".$dif;
    echo "<br>MAX: ".$qtdeMaxJogadoresSemRevezar;
    echo "<br>CONR: ".$convidadosNaoPrecisamRevezar;*/
    
    $consulta = "(select id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ".
        "lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ". 
        "lista_presenca.ativo=true and ". 
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "jogador.id_jogador not in ". 
        "(select id_jogador from atual) and ".
        "jogador.id_jogador not in ". 
        "(select id_jog_revez from atual where id_jog_revez is not null) ".
        "order by beneficios, hora_chegada) ".
        "union all ".
        "(select lista_presenca.id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ". 
        "atual, lista_presenca, jogador where ". 
        "(lista_presenca.id_jogador=atual.id_jogador or lista_presenca.id_jogador=atual.id_jog_revez) and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
    //echo "<br>ATU_REVEZ: " .$consulta;
    $result = pg_query($conn, $consulta);
    apagarRevezamentos($id_torneio);
    
    $arrayRevez = array();
    $arrayCandidatos = array();
    if($result){
        while ($row = pg_fetch_array($result)) {
            if($row['tipo']=="C"){
                //echo "<br>P0";
                $arrayRevez[$row['convidante']]=$row['id_jogador'];
            }else{
                $id_convidado=isMensalistaComConvidado($id_torneio, $row['id_jogador']);
                if($id_convidado>0){
                    $arrayRevez[$row['id_jogador']]=$id_convidado;
                }
            }
            array_push($arrayCandidatos, $row['id_jogador']);
        }
    }
    $contCon=0;
    for($x=0;$x<count($arrayCandidatos);$x++){
        $id_convidante=0;
        $id_convidado=0;
        //echo "<br>P1";
        if($arrayRevez[$arrayCandidatos[$x]]!=null){
            $id_convidante=$arrayCandidatos[$x];
            $id_convidado=$arrayRevez[$arrayCandidatos[$x]];
        }elseif(in_array($arrayCandidatos[$x], $arrayRevez)){
            $id_convidante=array_search($arrayCandidatos[$x],$arrayRevez);
            $id_convidado=$arrayCandidatos[$x];            
        }
        if($id_convidado>0){
            $contCon++;
            //echo "<br>P2: ".$contCon;
            if($contCon>$convidadosNaoPrecisamRevezar){
                //echo "<br>P3: ".$convidadosNaoPrecisamRevezar;
                incluiRevezamento($id_torneio,$id_convidante, $id_convidado);
                incluiRevezamento($id_torneio,$id_convidado, $id_convidante);
            }else{
                $arrayRevez[$id_convidante]=null;
            }
        }  
    }
}



function atualizarRevezamentos_OLD_20170807($id_torneio){
    global $conn;
    $retorno = array();
    
    $arrayQtPresentes = getQtdePresentes($id_torneio);
    $qtdeMenPresentes = $arrayQtPresentes[0];
    $qtdeConPresentes = $arrayQtPresentes[1];
    $qtdeMaxJogadoresSemRevezar = getMaxJogadoresSemRevezar();
    $qtdePresentes = $qtdeMenPresentes + $qtdeConPresentes;
    
    if($qtdePresentes > $qtdeMaxJogadoresSemRevezar){
        if($qtdeMenPresentes >= $qtdeMaxJogadoresSemRevezar){
            $convidadosNaoPrecisamRevezar = 0;
        }else{
            $convidadosNaoPrecisamRevezar = $qtdeMaxJogadoresSemRevezar-$qtdeMenPresentes;
        }
    }else{
        $convidadosNaoPrecisamRevezar = $qtdeConPresentes;
    }
    
    echo "<br>PRE: ".$qtdeMenPresentes;
    echo "<br>CON: ".$qtdeConPresentes;
    echo "<br>DIF: ".$dif;
    echo "<br>MAX: ".$qtdeMaxJogadoresSemRevezar;
    echo "<br>CONR: ".$convidadosNaoPrecisamRevezar;
     
    $consulta = "(select id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ".
        "lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ". 
        "lista_presenca.ativo=true and ". 
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "jogador.id_jogador not in ". 
        "(select id_jogador from atual) and ".
        "jogador.id_jogador not in ". 
        "(select id_jog_revez from atual where id_jog_revez is not null) ".
        "order by beneficios, hora_chegada) ".
        "union all ".
        "(select lista_presenca.id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ". 
        "atual, lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=atual.id_jogador and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
    echo $consulta;
    $result = pg_query($conn, $consulta);
    $contCon = 0;
    $arrayRevez = array();
    apagarRevezamentos();
    $contCon=0;

    if($result){
        while ($row = pg_fetch_array($result)) {
            echo "<br>nome=".$row['abreviatura'];
            if($row['tipo'] == "C"){
                echo "<br>PASSO1";
                $contCon++;
                if($contCon > $convidadosNaoPrecisamRevezar){
                    echo "<br>PASSO2";
                    if($arrayRevez[$row['convidante']]==null){
                        incluiRevezamento($row['id_torneio'],$row['convidante'], $row['id_jogador']);
                        incluiRevezamento($row['id_torneio'],$row['id_jogador'], $row['convidante']);
                        $arrayRevez[$row['convidante']]=$row['id_jogador'];
                    }else{
                        $contCon--;
                    }
                }
                
            }else{
                $id_jog_convidado=isMensalistaComConvidado($row['id_torneio'],$row['id_jogador']);
                echo "<br>convidadso: ". $id_jog_convidado."<br>";
                if($id_jog_convidado>0){
                    $contCon++;
                    echo "<br>contCon=".$contCon;
                    if($contCon > $convidadosNaoPrecisamRevezar){
                        echo "PASSO 4";
                        if($arrayRevez[$row['id_jogador']]==null){
                            incluiRevezamento($row['id_torneio'],$row['id_jogador'], $id_jog_convidado);
                            incluiRevezamento($row['id_torneio'],$id_jog_convidado, $row['id_jogador']);
                            $arrayRevez[$row['id_jogador']]=$id_jog_convidado;
                        }else{
                            $contCon--;
                        }
                    }
                }
            }
        }
    }
}


function getCandidatosPartida($id_torneio){
    global $conn;
    $retorno = array();
    
    atualizarRevezamentos($id_torneio);
    
    $consulta = "(select id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
        "abreviatura, convidante from ".
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
        "abreviatura, convidante from ". 
        "atual, lista_presenca, jogador where ". 
        "(lista_presenca.id_jogador=atual.id_jogador or lista_presenca.id_jogador=atual.id_jog_revez) and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.id_torneio=".$id_torneio." and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
    //echo "<br><br>".$consulta;
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->tipo = $row['tipo'];
            $jog->id_jog_revez = $row['id_jog_revez'];
            array_push($retorno, $jog);
        }
    }
    
    
    return $retorno;
}


function getCandidatosPartida_OLD_20170728($id_torneio){
    global $conn;
    $retorno = array();
    
    $arrayQtPresentes = getQtdePresentes($id_torneio);
    $qtdeMenPresentes = $arrayQtPresentes[0];
    $qtdeConPresentes = $arrayQtPresentes[1];
    $qtdeMaxJogadoresSemRevezar = getMaxJogadoresSemRevezar();
    $qtdePresentes = $qtdeMenPresentes + $qtdeConPresentes;
    
    if($qtdePresentes > $qtdeMaxJogadoresSemRevezar){
        if($qtdeMenPresentes >= $qtdeMaxJogadoresSemRevezar){
            $convidadosNaoPrecisamRevezar = 0;
        }else{
            $convidadosNaoPrecisamRevezar = $qtdeMaxJogadoresSemRevezar-$qtdeMenPresentes;
        }
    }else{
        $convidadosNaoPrecisamRevezar = $qtdeConPresentes;
    }
    
    /*echo "<br>PRE: ".$qtdeMenPresentes;
    echo "<br>CON: ".$qtdeConPresentes;
    echo "<br>DIF: ".$dif;
    echo "<br>MAX: ".$qtdeMaxJogadoresSemRevezar;
    echo "<br>CONR: ".$convidadosNaoPrecisamRevezar;*/
     
    $consulta = "(select id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ".
        "lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ". 
        "lista_presenca.ativo=true and ". 
        "jogador.id_jogador not in ". 
        "(select id_jogador from atual) and ".
        "jogador.id_jogador not in ". 
        "(select id_jog_revez from atual where id_jog_revez is not null) ".
        "order by beneficios, hora_chegada) ".
        "union all ".
        "(select lista_presenca.id_torneio, jogador.id_jogador, tipo, ".
        "abreviatura, convidante from ". 
        "atual, lista_presenca, jogador where ". 
        "lista_presenca.id_jogador=atual.id_jogador and ". 
        "lista_presenca.id_jogador=jogador.id_jogador and ".
        "lista_presenca.ativo=true ". 
        "order by venceu, beneficios, hora_chegada);";
    //echo $consulta;
    $result = pg_query($conn, $consulta);
    $contCon = 0;
    $arrayConRevez = array();
    apagarRevezamentos();
    $contCon=0;
    if($result){
        while ($row = pg_fetch_array($result)) {
            if($row['tipo'] == "C"){
                $contCon++;
                if($contCon > $convidadosNaoPrecisamRevezar){
                    incluiRevezamento($row['id_torneio'],$row['convidante'], $row['id_jogador']);
                    incluiRevezamento($row['id_torneio'],$row['id_jogador'], $row['convidante']);
                }
            }else{
                $id_jog_convidado=isMensalistaComConvidado($row['id_torneio'],$row['id_jogador']);
                //echo "<br><br>convidado: ". $id_jog_convidado."<br>";
                if($id_jog_convidado>0){
                    //$contCon++;
                    if($contCon > $convidadosNaoPrecisamRevezar){
                        incluiRevezamento($row['id_torneio'],$row['id_jogador'], $id_jog_convidado);
                        incluiRevezamento($row['id_torneio'],$id_jog_convidado, $row['id_jogador']);
                    }
                }
            }
        }
    }
    
    $consulta = "(select id_torneio, jogador.id_jogador, lista_presenca.id_jog_revez, tipo, ".
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
    //echo "<br><br>".$consulta;
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->tipo = $row['tipo'];
            $jog->id_jog_revez = $row['id_jog_revez'];
            array_push($retorno, $jog);
        }
    }
    
    
    return $retorno;
}

function isMensalistaComConvidado($id_torneio, $id_jogador){
    global $conn;
    $retorno = 0;
    $consulta = "select jogador.id_jogador from jogador, lista_presenca ".
                "where lista_presenca.id_jogador=jogador.id_jogador and ".
                "ativo=true and id_torneio=$1 and convidante=$2;";
    $params = array($id_torneio, $id_jogador);
    $result = pg_query_params( $conn, $consulta, $params);
    if($result){
        $row = pg_fetch_array($result);
        $retorno = $row['id_jogador'];
    }
    return $retorno;
}

function carregarArrayMensalistas(){
    global $conn;
    global $mensalistas;
    $mensalistas = array();
    $consulta = "select id_jogador,nome from jogador where tipo = 'M';";
    $result = pg_query( $conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)){
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->nome = $row['nome'];
            array_push($mensalistas, $jog);
        }
    }
}

function incluiRevezamento($id_torneio, $id_convidante, $id_convidado){
    global $conn;
    $query = "update lista_presenca set id_jog_revez=$1 ".
            "where lista_presenca.id_torneio=$2 and ".
            "id_jogador=$3;";
    $params = array($id_convidante, $id_torneio, $id_convidado);
    $result = pg_query_params( $conn, $query, $params);
}

function getCandidatosPartida_OLD(){
    global $conn;
    $retorno = array();
    
    $consultaPartidaAtual = "select max(id_partida) from ".
    "partida, torneio where ".
    "partida.id_torneio=torneio.id_torneio and ".
    "partida.em_andamento = true";
    
    $result = pg_query($conn, $consulta);
    if($result){
        $row = pg_fetch_array($result);
        $id_partida = $row['id_partida'];
    }
    
    $consultaTimeA = "select vitorioso, time.id_jogador from time, partida where ".
    "partida = " .$id_partida. " and partida.id_time_a = time.id_time;";
    
    $result = pg_query($conn, $consulta);
    $id_jogadores_a = array();
    if($result){
        while ($row = pg_fetch_array($result)) {
            array_push($id_jogadores_a, $row['id_jogador']);
        }
    }
    
    $consultaTimeB = "select time.id_jogador from time, partida where ".
    "partida = " .$id_partida. " and partida.id_time_b = time.id_time;";
    
    $result = pg_query($conn, $consulta);
    $id_jogadores_b = array();
    if($result){
        while ($row = pg_fetch_array($result)) {
            array_push($id_jogadores_b, $row['id_jogador']);
        }
    }
    
    $consultaJogadoresBanco = "select id_jogador from lista_presenca ";
    

    return $retorno;
}

function escalarTimes($clausulaIn){
    global $conn;
    $listaOrdenada = array();
    $indice = rand(1,2);
    if($indice > 1){
        $ordenacao = "ASC";
    }else{
        $ordenacao = "DESC";
    }
    $consulta = "select id_jogador, abreviatura, tipo, nota ".
    "from jogador where id_jogador in ". $clausulaIn. " ".
    "order by nota ".$ordenacao.";";
    //echo $consulta;
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->tipo = $row['tipo'];
            $jog->nota = $row['nota'];
            array_push($listaOrdenada, $jog);
        }
    }
    
    return algoritmoDistribuicao($listaOrdenada); 
}

function getPartidaAtual(){
    global $conn;
    $time_a = array();
    $time_b = array();
    $id_partida = -1;
    $consulta = "select id_partida,jogador.id_jogador,jogador.tipo,jogador.abreviatura,jogador.nota, time ".
    "from jogador, atual where atual.id_jogador=jogador.id_jogador order by time";
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->tipo = $row['tipo'];
            $jog->nota = $row['nota'];
            if($row['time']=="A")
                array_push($time_a, $jog);
            else
                array_push($time_b, $jog);
            $id_partida = $row['id_partida'];
        }
    }
    
    return array($id_partida,$time_a,$time_b); 
}

function isPartidaEmAndamento(){
    global $conn;
    $retorno = false;
    $consulta = "select * from atual where venceu is null;";
    $result = pg_query($conn, $consulta);
    if($result){
        $row = pg_fetch_array($result);
        if(pg_num_rows($result)>=10){
            $retorno = true;
        }
    }
    return $retorno; 
}



function algoritmoDistribuicao($listaOrdenada){
    $time_a = array();
    $time_b = array();
    $cont=0;
    for($x=0; $x<count($listaOrdenada)/2; $x++){
        $arrayTemp = selecionaJogador($listaOrdenada[$cont],$listaOrdenada[$cont+1]);
        $time_a[$x] = $arrayTemp[0];
        $time_b[$x] = $arrayTemp[1];
        $cont+=2;
    }
    return array($time_a,$time_b);
}

function selecionaJogador($jog1, $jog2){
    $randomico = rand(0,1);
    if($randomico > 0)
        return array($jog1,$jog2);
    else
        return array($jog2,$jog1);
}

function imprimeResultado($titulo, $query){
    global $conn;
    $result = pg_query($conn, $query);
    $i = pg_num_fields($result);
    echo "<table class=\"dados\" border=1><tr>";
    echo '<td class=\"dados\" colspan='.$i.'>'.$titulo.'</td></tr><tr>';
    for ($j = 0; $j < $i; $j++) {
        $fieldname = pg_field_name($result, $j);
        echo '<td class=\"dados\">'.$fieldname.'</td>';
    }
    $i = 0;

    while ($row = pg_fetch_row($result)) 
    {
    	echo '<tr>';
    	$count = count($row);
    	$y = 0;
    	while ($y < $count)
    	{
    		$c_row = current($row);
    		echo '<td class=\"dados\">' . $c_row . '</td>';
    		next($row);
    		$y = $y + 1;
    	}
    	echo '</tr>';
    	$i = $i + 1;
    }
    echo "</tr></table>";
}

function getEstatisticas(){
    global $conn;
    
    $query = " select data,id_partida,to_char(inicio, 'HH24:MI:SS') as hora , ".
        "    time, vitorioso as venceu, jogador1,jogador2,jogador3, ".
        "    jogador4,jogador5,jogador6 from ".
        "    ( ".
        "    (select id_partida,data,".
        "    hora_inicio as inicio, 'A' as time, vitorioso,  ".
        "    joga1.abreviatura as jogador1,  ".
        "    joga2.abreviatura as jogador2,  ".
        "    joga3.abreviatura as jogador3,  ".
        "    joga4.abreviatura as jogador4,  ".
        "    joga5.abreviatura as jogador5,  ".
        "    joga6.abreviatura as jogador6 ".
        "    from partida ".
        "    inner join time on ".
        "    partida.id_time_a = time.id_time  ".
        "    inner join jogador joga1 on ".
        "    time.id_jogador1=joga1.id_jogador ".
        "    inner join jogador joga2 on ".
        "    time.id_jogador2=joga2.id_jogador ".
        "    inner join jogador joga3 on ".
        "    time.id_jogador3=joga3.id_jogador ".
        "    inner join jogador joga4 on ".
        "    time.id_jogador4=joga4.id_jogador ".
        "    inner join jogador joga5 on ".
        "    time.id_jogador5=joga5.id_jogador ".
        "    inner join jogador joga6 on ".
        "    time.id_jogador6=joga6.id_jogador".
        "    inner join torneio on".
        "    partida.id_torneio=torneio.id_torneio) ".
        "    union  ".
        "    (select id_partida, data, hora_inicio as inicio, 'B' as time, vitorioso,  ".
        "    joga1.abreviatura as jogador1,  ".
        "    joga2.abreviatura as jogador2,  ".
        "    joga3.abreviatura as jogador3,  ".
        "    joga4.abreviatura as jogador4, ".
        "    joga5.abreviatura as jogador5,  ".
        "    joga6.abreviatura as jogador6 ".
        "    from partida ".
        "    inner join time on ".
        "    partida.id_time_b = time.id_time  ".
        "    inner join jogador joga1 on ".
        "    time.id_jogador1=joga1.id_jogador ".
        "    inner join jogador joga2 on ".
        "    time.id_jogador2=joga2.id_jogador ".
        "    inner join jogador joga3 on ".
        "    time.id_jogador3=joga3.id_jogador ".
        "    inner join jogador joga4 on ".
        "    time.id_jogador4=joga4.id_jogador ".
        "    inner join jogador joga5 on ".
        "    time.id_jogador5=joga5.id_jogador ".
        "    inner join jogador joga6 on ".
        "    time.id_jogador6=joga6.id_jogador".
        "    inner join torneio on".
        "    partida.id_torneio=torneio.id_torneio".
        "    )) as time ".
        "    order by data desc,inicio desc,time;";

    $result = pg_query($conn, $query);
    $torneios = array();
    $id_torneio_atual = 0;
    if($result){
        while ($row = pg_fetch_array($result)) {
            $dt_torneio = $row['data'];
            if($dt_torneio_atual != $dt_torneio){
                $jogadores = array();
                $dt_torneio_atual = $dt_torneio;
            }
            //echo "<BR>TIME:".$row['time'];
            //echo "<BR>VITORIOSO:".$row['venceu'];
            if($row['time']==$row['venceu']){
                $jogadores[$row['jogador1']][0]+=1;
                $jogadores[$row['jogador2']][0]+=1;
                $jogadores[$row['jogador3']][0]+=1;
                $jogadores[$row['jogador4']][0]+=1;
                $jogadores[$row['jogador5']][0]+=1;
                $jogadores[$row['jogador6']][0]+=1;
            }else{
                $jogadores[$row['jogador1']][1]+=1;
                $jogadores[$row['jogador2']][1]+=1;
                $jogadores[$row['jogador3']][1]+=1;
                $jogadores[$row['jogador4']][1]+=1;
                $jogadores[$row['jogador5']][1]+=1;
                $jogadores[$row['jogador6']][1]+=1;
            }
            $torneios[$dt_torneio] = $jogadores;
        }
    }
    //echo "<BR><BR><BR>ARRAY:<BR>";
    //print_r(array_values($torneios));
    //echo "<BR>--------------------------<BR><BR><BR>";
    imprimeEstatisticas($torneios);
}

function imprimeEstatisticas($torneios){
    echo "<br><br><table class=\"dados\" border=1>";
    foreach(array_keys($torneios) as $dt_torneio){
        echo "<tr><td colspan=4>TORNEIO ".$dt_torneio."</td></tr>";
        echo "<tr><td>JOGADOR</td><td>VITORIAS</td><td>DERROTAS</td><td>APROVEITAMENTO</td></tr>";
        foreach(array_keys($torneios[$dt_torneio]) as $jogadores){
            $vitorias = $torneios[$dt_torneio][$jogadores][0]+0;
            $derrotas = $torneios[$dt_torneio][$jogadores][1]+0;
            $aprov = number_format($vitorias/($vitorias + $derrotas)*100,2)."%";
            if($aprov==100){
                $aprov = "<b><font color=blue>".$aprov."</font></b>";
                $jogadores = "<b><font color=blue>".$jogadores."</font></b>";
            }elseif
            ($aprov==0){
                $aprov = "<b><font color=red>".$aprov."</font></b>";
                 $jogadores = "<b><font color=red>".$jogadores."</font></b>";
            }
            echo "<tr><td>".$jogadores."</td>"."<td>".$vitorias."</td>".
                 "<td>".$derrotas."</td>"."<td>".$aprov."</td></tr>";

        }
    }
    echo "</table>";
}


function getEstatisticasDadosObj(){
    global $conn;
    
    $query = " select data,id_partida,to_char(inicio, 'HH24:MI:SS') as hora , ".
        "    time, vitorioso as venceu, jogador1,jogador2,jogador3, ".
        "    jogador4,jogador5,jogador6 from ".
        "    ( ".
        "    (select id_partida,data,".
        "    hora_inicio as inicio, 'A' as time, vitorioso,  ".
        "    joga1.abreviatura as jogador1,  ".
        "    joga2.abreviatura as jogador2,  ".
        "    joga3.abreviatura as jogador3,  ".
        "    joga4.abreviatura as jogador4,  ".
        "    joga5.abreviatura as jogador5,  ".
        "    joga6.abreviatura as jogador6 ".
        "    from partida ".
        "    inner join time on ".
        "    partida.id_time_a = time.id_time  ".
        "    inner join jogador joga1 on ".
        "    time.id_jogador1=joga1.id_jogador ".
        "    inner join jogador joga2 on ".
        "    time.id_jogador2=joga2.id_jogador ".
        "    inner join jogador joga3 on ".
        "    time.id_jogador3=joga3.id_jogador ".
        "    inner join jogador joga4 on ".
        "    time.id_jogador4=joga4.id_jogador ".
        "    inner join jogador joga5 on ".
        "    time.id_jogador5=joga5.id_jogador ".
        "    inner join jogador joga6 on ".
        "    time.id_jogador6=joga6.id_jogador".
        "    inner join torneio on".
        "    partida.id_torneio=torneio.id_torneio) ".
        "    union  ".
        "    (select id_partida, data, hora_inicio as inicio, 'B' as time, vitorioso,  ".
        "    joga1.abreviatura as jogador1,  ".
        "    joga2.abreviatura as jogador2,  ".
        "    joga3.abreviatura as jogador3,  ".
        "    joga4.abreviatura as jogador4, ".
        "    joga5.abreviatura as jogador5,  ".
        "    joga6.abreviatura as jogador6 ".
        "    from partida ".
        "    inner join time on ".
        "    partida.id_time_b = time.id_time  ".
        "    inner join jogador joga1 on ".
        "    time.id_jogador1=joga1.id_jogador ".
        "    inner join jogador joga2 on ".
        "    time.id_jogador2=joga2.id_jogador ".
        "    inner join jogador joga3 on ".
        "    time.id_jogador3=joga3.id_jogador ".
        "    inner join jogador joga4 on ".
        "    time.id_jogador4=joga4.id_jogador ".
        "    inner join jogador joga5 on ".
        "    time.id_jogador5=joga5.id_jogador ".
        "    inner join jogador joga6 on ".
        "    time.id_jogador6=joga6.id_jogador".
        "    inner join torneio on".
        "    partida.id_torneio=torneio.id_torneio".
        "    )) as time ".
        "    order by data desc,inicio desc,time;";

    $result = pg_query($conn, $query);
    $torneios = array();
    $id_torneio_atual = 0;
    $dt_torneio_atual = "";
    if($result){
        while ($row = pg_fetch_array($result)) {
            $dt_torneio = $row['data'];
            if($dt_torneio_atual != $dt_torneio){
                if($dt_torneio_atual != ""){
                    $torneio->jogadores = $jogadores;
                    array_push($torneios, $torneio);
                }
                $torneio = new torneio();
                $torneio->data = $dt_torneio;
                $torneio->qtPartidas = 0;
                $jogadores = array();
                $dt_torneio_atual = $dt_torneio;
            }
            $torneio->qtPartidas+=0.5;
            if($row['venceu']!=""){
                if($row['time']==$row['venceu']){
                    $jogadores[$row['jogador1']][0]+=1;
                    $jogadores[$row['jogador2']][0]+=1;
                    $jogadores[$row['jogador3']][0]+=1;
                    $jogadores[$row['jogador4']][0]+=1;
                    $jogadores[$row['jogador5']][0]+=1;
                    $jogadores[$row['jogador6']][0]+=1;
                }else{
                    $jogadores[$row['jogador1']][1]+=1;
                    $jogadores[$row['jogador2']][1]+=1;
                    $jogadores[$row['jogador3']][1]+=1;
                    $jogadores[$row['jogador4']][1]+=1;
                    $jogadores[$row['jogador5']][1]+=1;
                    $jogadores[$row['jogador6']][1]+=1;
                }
            }
        }
        $torneio->jogadores = $jogadores;
        array_push($torneios, $torneio);
    }
    //echo "<BR><BR><BR>ARRAY:<BR>";
    //print_r(array_values($torneios));
    //echo "<BR>--------------------------<BR><BR><BR>";
    return $torneios;
}

function getJogadoresObj(){
    global $conn;
    $retorno = null;
    $jogadores = array();
    $consulta = "select * from jogador  where tipo='M' order by abreviatura;";  
    $result = pg_query($conn, $consulta);
    if($result){
        while ($row = pg_fetch_array($result)) {
            $jog = new jogador();
            $jog->id_jogador = $row['id_jogador'];
            $jog->abreviatura = $row['abreviatura'];
            $jog->tipo = $row['tipo'];
            $jog->nome = $row['nome'];
            array_push($jogadores, $jog);
        }
        $retorno = $jogadores;
    }
    return $retorno;
}



?>

