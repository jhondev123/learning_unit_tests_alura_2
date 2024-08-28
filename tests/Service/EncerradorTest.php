<?php

namespace Alura\Leilao\Tests\Service;

use PDO;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Dao\Leilao as LeilaoDao;


class EncerradorTest extends TestCase
{

    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));
        $leiloes = [$leilaoFiat, $leilaoVariante];
        // $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
        // ->setConstructorArgs([new PDO('sqlite::memory:')])
        // ->getMock();
        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')->willReturn($leiloes);
        $leilaoDao->method('recuperarFinalizados')->willReturn($leiloes);
        $leilaoDao->expects($this->exactly(2))
        ->method('atualiza')
        ->withConsecutive(
            [$leilaoFiat],
            [$leilaoVariante]
        );

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        static::assertCount(2, $leiloes);
        static::assertTrue($leiloes[0]->estaFinalizado());
        static::assertTrue($leiloes[1]->estaFinalizado());
    }
}
