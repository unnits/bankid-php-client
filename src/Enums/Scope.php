<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

use Stringable;

enum Scope: string
{
    case OpenId = 'openid';
    case OfflineAccess = 'offline_access';
    case Address = 'profile.addresses';
    case BirthDate = 'profile.birthdate';
    case BirthNumber = 'profile.birthNumber';
    case BirthplaceNationality = 'profile.birthplaceNationality';
    case Email = 'profile.email';
    case Gender = 'profile.gender';
    case IdCards = 'profile.idCards';
    case LegalStatus = 'profile.legalStatus';
    case Locale = 'profile.locale';
    case MaritalStatus = 'profile.maritalStatus';
    case Name = 'profile.name';
    case PaymentAccounts = 'profile.paymentAccounts';
    case PhoneNumber = 'profile.phoneNumber';
    case Titles = 'profile.titles';
    case UpdatedAt = 'profile.updatedAt';
    case ZoneInfo = 'profile.zoneInfo';
    case Verification = 'profile.verification';

    /**
     * @param Stringable|string $scope
     * @return Scope[]
     */
    public static function collectionFromString(Stringable|string $scope): array
    {
        /** @var array<int, Scope|null> $scopes */
        $scopes = array_map(
            fn (string $scope) => Scope::tryFrom($scope),
            explode(' ', strval($scope))
        );

        /** @var Scope[] $scopes */
        $scopes = array_filter($scopes, fn (?Scope $scope) => $scope !== null);

        return $scopes;
    }
}
