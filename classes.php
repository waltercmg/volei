<?php
class jogador
{
    public $id_jogador;
    public $nome;
    public $abreviatura;
    public $ativo;
    public $beneficios;
    public $tipo;
    public $id_jog_revez;
    public $presente;
    public $qtVitorias;
    public $qtDerrotas;
}

class torneio
{
    public $id_torneio;
    public $data;
    public $qtPartidas;
    public $jogadores;
}
?>
