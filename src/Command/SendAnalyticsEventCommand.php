<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:send-analytics-event'
)]
class SendAnalyticsEventCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $analyticsLogger,
        private readonly string $analyticServiceUrl,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('data', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $childPid = pcntl_fork();

        if ($childPid < 0) {
            return self::FAILURE;
        } elseif ($childPid > 0) {
            return self::SUCCESS;
        }

        $data = $input->getArgument('data');

        $this->send($data);

        exit();
    }

    private function send(string $data): void
    {
        $body = ['body' => $data];

        $response = $this->httpClient->request("POST", $this->analyticServiceUrl, $body);

        if ($response->getStatusCode() === 500) {
            $this->analyticsLogger->warning(
                sprintf("Failed request to analytics server. Message body: %s", $data)
            );
        }
    }
}
