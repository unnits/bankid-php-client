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
    case BirthNumber = 'profile.birthnumber';
    case BirthplaceNationality = 'profile.birthplaceNationality';
    case Email = 'profile.email';
    case Gender = 'profile.gender';
    case IdCards = 'profile.idcards';
    case LegalStatus = 'profile.legalstatus';
    case Locale = 'profile.locale';
    case MaritalStatus = 'profile.maritalstatus';
    case Name = 'profile.name';
    case PaymentAccounts = 'profile.paymentAccounts';
    case PhoneNumber = 'profile.phonenumber';
    case Titles = 'profile.titles';
    case UpdatedAt = 'profile.updatedat';
    case ZoneInfo = 'profile.zoneinfo';
    case Verification = 'profile.verification';
    case NotificationClaimsUpdated = 'notification.claims_updated';

    /**
     * @param Stringable|string $scope
     * @return Scope[]
     */
    public static function collectionFromString(Stringable|string $scope): array
    {
        return self::collectionFromArray(explode(' ', strval($scope)));
    }

    /**
     * @param string[] $scopes
     * @return Scope[]
     */
    public static function collectionFromArray(array $scopes): array
    {
        /** @var array<int, Scope|null> $scopes */
        $scopes = array_map(
            fn (string $scope) => Scope::tryFrom($scope),
            $scopes
        );

        /** @var Scope[] $scopes */
        $scopes = array_filter($scopes, fn (?Scope $scope) => $scope !== null);

        return $scopes;
    }
}
