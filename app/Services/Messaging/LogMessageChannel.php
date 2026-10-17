<?php

namespace App\Services\Messaging;

use App\Contracts\MessageChannel;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Log;

class LogMessageChannel implements MessageChannel
{
    public function __construct(private readonly string $channel = 'sms') {}

    public function send(string $to, string $body, array $meta = []): void
    {
        MessageLog::create([
            'channel' => $this->channel,
            'to' => $to,
            'body' => $body,
            'status' => 'logged',
            'meta' => $meta,
        ]);

        Log::channel('single')->info(strtoupper($this->channel).' message logged', [
            'to' => $to,
            'body' => $body,
        ]);
    }
}
