<?php

declare(strict_types=1);

namespace App\Command;

use App\Traits\CalculationTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

#[AsCommand(
    name: 'addBonusCommand',
    description: 'Command add bonuses to uni card trough Beeline',
)]
final class AddBonusCommand extends Command
{
    use CalculationTrait;

    private const string URL = 'https://fliprunner-api.bemobile.ge/apiV5.php';
    private const array CARDS = [
        '1199110400239578', // Meine Kartennummer
    ];

    public function __construct(
        private readonly string $password,
        private readonly LoggerInterface $logger,
    ){
        parent::__construct('addBonusCommand');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = [
            "addBonus" => 1,
            "amount" => $this->generateUniBonusAmount(),
            "id" => $this->generateId(),
            "msisdn" => "568156132",
            "password" => $this->password,
            "unicardNumber" => self::CARDS[0],
            "username" => "flip",
        ];

        $response = $this->sendPOST(self::URL, $data);
        if ($response === false) {
            $io->error('failed send request');
            return Command::FAILURE;
        }

        $io->success('request sent successfully');
        return Command::SUCCESS;
    }

    /**
     * @param string $url
     * @param array $data
     * @return string|bool
     */
    private function sendPOST(string $url, array $data): string|bool
    {
        try {
            $ch = curl_init($url);

            if ($ch === false) {
                throw new RuntimeException('Failed to initialize CURL');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));

            $response = curl_exec($ch);
            if ($response === false) {
                throw new RuntimeException(curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode < 200 || $httpCode >= 300) {
                throw new RuntimeException("HTTP request failed with code {$httpCode}");
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        curl_close($ch);
        return $response ?? false;
    }
}
