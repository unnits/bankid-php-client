<?php

declare(strict_types=1);

namespace App\Contract\BankId;

enum AvailableBank: string
{
    case Air = 'Air Bank';
    case Cs = 'Česká spořitelna';
    case Csob = 'ČSOB a.s.';
    case Fio = 'Fio Banka a.s.';
    case Kb = 'Komerční banka';
    case Moneta = 'MONETA';
    case Rb = 'Raiffeisenbank a.s.';
}
