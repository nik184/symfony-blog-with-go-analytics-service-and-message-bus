<?php

namespace App\Message;

final class AnalyticsServiceMessage
{
    public function __construct(public readonly string $data)
    {
    }
}
