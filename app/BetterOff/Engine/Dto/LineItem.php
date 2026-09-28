<?php

namespace App\BetterOff\Engine\Dto;

final class LineItem
{
    public function __construct(
        public readonly string $key,
        public readonly float $amount,
        public readonly ?string $rateKey = null,
        public readonly ?string $note = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'amount' => $this->amount,
            'rate_key' => $this->rateKey,
            'note' => $this->note,
        ];
    }
}
