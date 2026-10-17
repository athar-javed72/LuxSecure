<?php

namespace App\Services\Messaging;

use App\Contracts\MessageChannel;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LogMessageChannel implements MessageChannel
{
    public function __construct(private readonly string $channel = 'sms') {}

    public function send(string $to, string $body, array $meta = []): void
    {
        if (app()->environment('production')
            && $this->channel === 'sms'
            && (string) config('luxsecure.messaging.default', 'log') === 'log'
            && ! config('luxsecure.messaging.allow_log_in_production')) {
            throw new RuntimeException(
                'MESSAGING_DRIVER=log is not allowed for OTP/SMS in production. Set MESSAGING_DRIVER=http or MESSAGING_ALLOW_LOG_IN_PRODUCTION=true.'
            );
        }

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
