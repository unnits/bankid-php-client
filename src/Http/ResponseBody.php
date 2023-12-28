<?php

declare(strict_types=1);

namespace Unnits\BankId\Http;

class ResponseBody
{
    /**
     * @param array<int|string, mixed> $data
     */
    public function __construct(public readonly array $data)
    {
        //
    }

    public function stringOrNull(string $key): ?string
    {
        return array_key_exists($key, $this->data)
            ? strval($this->data[$key])
            : null;
    }

    public function string(string $key, string $default = ''): string
    {
        return strval($this->data[$key] ?? $default);
    }

    public function integer(string $key, int $default = 0): int
    {
        return intval($this->data[$key] ?? $default);
    }
}
