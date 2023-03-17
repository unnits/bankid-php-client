<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

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
}
