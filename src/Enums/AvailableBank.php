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
    case Creditas = 'creditas';

    public function id(): string
    {
        return match ($this) {
            AvailableBank::AirBank => '297d3f16-c4c0-4f48-8d98-c94d16eb9e35',
            AvailableBank::CeskaSporitelna => '7e623fed-8aab-4d24-9918-c610d3057859',
            AvailableBank::Csob => '032051de-f43d-4cb8-a911-2ef03773a3b4',
            AvailableBank::FioBanka => '1aebb93d-3643-46eb-a6cf-a1760e02a810',
            AvailableBank::KomercniBanka => '3b42a926-7d02-472b-bd22-f12fdf22bf0f',
            AvailableBank::Moneta => 'bd86df8c-56a0-4d0b-b7f6-bcff83fa09de',
            AvailableBank::RaiffeisenBank => '297c962c-6c1c-4758-81bd-93191ca62749',
            AvailableBank::Creditas => '87071d79-f3b6-47d0-8d3d-bbd41207b431',
        };
    }
}
