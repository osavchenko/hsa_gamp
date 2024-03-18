<?php

declare(strict_types=1);

namespace App\Command;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExchangeRateExporterCommand extends Command
{
    private HttpClientInterface $exchangeRateClient;
    private HttpClientInterface $measurementClient;

    public function __construct()
    {
        parent::__construct('app:exhange-rate-exporter');

        $this->exchangeRateClient = HttpClient::createForBaseUri(
            'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange'
        );
        $this->measurementClient = HttpClient::createForBaseUri('https://www.google-analytics.com/mp/', [
            'query' => [
                'api_secret' => 'r3dMJgvNTr-VilOH6tpXWQ',
                'measurement_id' => 'G-LGNSX1SKQN',
            ],
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', date('Y-02-G'));
        $rate = $this->getExchangeRateByDate($date);
        $this->sendRateToGoogleAnalytics($date, $rate);

        return 0;
    }

    private function getExchangeRateByDate(DateTimeInterface $dateTime): float
    {
        $response = $this->exchangeRateClient->request('GET', '', [
            'query' => [
                'valcode' => 'USD',
                'date' => $dateTime->format('Ymd'),
                'json',
            ],
        ]);

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $responseData[0]['rate'];
    }

    private function sendRateToGoogleAnalytics(DateTimeInterface $date, float $exchangeRate): void
    {
        $response = $this->measurementClient->request('POST', 'collect', [
            'json' => [
                'client_id' => '562454151.1710698541',
                'events' => [
                    [
                        'name' => 'exchange_rate',
                        'params' => [
                            'date' => $date->format('Y-m-d'),
                            'value' => $exchangeRate,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
