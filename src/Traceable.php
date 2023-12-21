<?php

declare(strict_types=1);

namespace Unnits\BankId;

interface Traceable
{
    public function getTraceId(): ?string;
}
