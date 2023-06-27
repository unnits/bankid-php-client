<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum AvailableBank: string
{
    case AirBank = 'air';
    case CeskaSporitelna = 'cs';
    case Csob = 'csob';
    case FioBanka = 'fio';
    case KomercniBanka = 'kb';
    case Moneta = 'moneta';
    case RaiffeisenBank = 'rb';

    public function label(): string
    {
        return match ($this) {
            AvailableBank::AirBank => 'Air Bank',
            AvailableBank::CeskaSporitelna => 'Česká spořitelna',
            AvailableBank::Csob => 'ČSOB a.s.',
            AvailableBank::FioBanka => 'Fio banka a.s.',
            AvailableBank::KomercniBanka => 'Komerční banka',
            AvailableBank::Moneta => 'MONETA',
            AvailableBank::RaiffeisenBank => 'Raiffeisenbank a.s.',
        };
    }
}
