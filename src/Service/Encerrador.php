<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Error;

class Encerrador
{
    private $leilaoDao;
    private $enviadorEmail;
    public function __construct(LeilaoDao $leilaoDao, EnviadorEmail $enviadorEmail)
    {
        $this->leilaoDao = $leilaoDao;
        $this->enviadorEmail = $enviadorEmail;
    }
    public function encerra()
    {
        $leiloes = $this->leilaoDao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                try {

                    $leilao->finaliza();
                    $this->leilaoDao->atualiza($leilao);
                    $this->enviadorEmail->notificaTerminoLeilao($leilao);
                } catch (\DomainException $e) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}
