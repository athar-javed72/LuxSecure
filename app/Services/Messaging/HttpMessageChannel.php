<?php

namespace App\Services\Messaging;

use App\Contracts\MessageChannel;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpMessageChannel implements MessageChannel
{
    public function __construct(
        private readonly string $url,
        private readonly ?string $apiKey,
        private readonly ?string $sender,
    ) {}

    public function send(string $to, string $body, array $meta = []): void
    {
        if ($this->url === '') {
            throw new RuntimeException('SMS HTTP gateway URL is not configured.');
        }

        $response = Http::withHeaders(array_filter([
            'Authorization' => $this->apiKey ? 'Bearer '.$this->apiKey : null,
            'Accept' => 'application/json',
        ]))->post($this->url, [
            'to' => $to,
            'from' => $this->sender,
            'body' => $body,
            'meta' => $meta,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('SMS HTTP gateway request failed: '.$response->status());
        }
    }
}
