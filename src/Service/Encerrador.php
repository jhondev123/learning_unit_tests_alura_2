<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    private $leilaoDao;
    public function __construct(LeilaoDao $leilaoDao) {
        $this->leilaoDao = $leilaoDao;
    }
    public function encerra()
    {
        $leiloes = $this->leilaoDao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                $leilao->finaliza();
                $this->leilaoDao->atualiza($leilao);
            }
        }
    }
}
