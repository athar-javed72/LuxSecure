<?php

namespace App\Contracts;

interface MessageChannel
{
    public function send(string $to, string $body, array $meta = []): void;
}
