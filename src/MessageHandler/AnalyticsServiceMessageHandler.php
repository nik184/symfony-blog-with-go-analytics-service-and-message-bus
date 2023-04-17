<?php

namespace App\MessageHandler;

use App\Message\AnalyticsServiceMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class AnalyticsServiceMessageHandler
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $analyticsLogger,
        private readonly string $analyticServiceUrl
    )
    {}

    public function __invoke(AnalyticsServiceMessage $message)
    {
        $body = ['body' => $message->data];

        $response = $this->httpClient->request("POST", $this->analyticServiceUrl, $body);

        if ($response->getStatusCode() === 500) {
            $this->analyticsLogger->warning(
                sprintf("Failed request to analytics server. Message body: %s", $message->data)
            );
        }
    }
}
