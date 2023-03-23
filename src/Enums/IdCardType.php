<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum IdCardType: string
{
    case ID = 'id';
    case Passport = 'p';
    case DrivingLicence = 'dl';
    case ResidencePermit = 'ir';
    case VisaPermit = 'vs';
    case ResidentialLabel = 'ps';
    case BookWithResidencePermit = 'ix';
    case TemporaryResidence = 'ie';
    case Unknown = 'unknown';
}
