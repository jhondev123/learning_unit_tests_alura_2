<?php

namespace Alura\Leilao\Tests\Service;

use PDO;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use Alura\Leilao\Dao\Leilao as LeilaoDao;


class EncerradorTest extends TestCase
{
    private $encerrador;
    private $leilaoFiat;
    private $leilaoVariante;
    private $leiloes;
    private $enviadorEmail;
    protected function setUp(): void
    {
        $this->leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));
        $this->leiloes = [$this->leilaoFiat, $this->leilaoVariante];



        // $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
        // ->setConstructorArgs([new PDO('sqlite::memory:')])
        // ->getMock();
        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')->willReturn($this->leiloes);
        $leilaoDao->method('recuperarFinalizados')->willReturn($this->leiloes);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$this->leilaoFiat],
                [$this->leilaoVariante]
            );



        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);


        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {




        $this->encerrador->encerra();

        static::assertCount(2, $this->leiloes);
        static::assertTrue($this->leiloes[0]->estaFinalizado());
        static::assertTrue($this->leiloes[1]->estaFinalizado());
    }
    public function testeDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $exception = new \DomainException('Não foi possível enviar email.');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificaTerminoLeilao')
            ->willThrowException($exception);

        $this->encerrador->encerra();
    }
    public function testSoDeveEnviarLeilaoPorEmailAposFinalizado()
    {
        $this->enviadorEmail->expects(self::exactly(2))
            ->method('notificaTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                static::assertTrue($leilao->estaFinalizado());
            });

        $this->encerrador->encerra();
    }
}
