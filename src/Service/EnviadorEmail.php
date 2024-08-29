<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    public function notificaTerminoLeilao(Leilao $leilao)
    {
        $sucesso = mail(
             'usuario@gmail.com',
             'Leilão Finalizado ',
             'O leilão ' . $leilao->recuperarDescricao() . ' acabou.'
        );

        if (!$sucesso) {
            throw new \DomainException('Não foi possível enviar email.');
        }
    }
}
