<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum BankService: string
{
    case Authentication = 'AUTHENTICATION';
    case SignSinglePdf = 'SIGN_SINGLE_PDF';
    case SignMultiplePdf = 'SIGN_MULTIPLE_PDF';
    case SignSo = 'SIGN_SO';
    case Notification = 'NOTIFICATION';
    case UniqueIdentity = 'UNIQUE_ID' ?? 'UNIQUE_IDENTITY';
    case QualifiedSignature = 'QSIGN';
}
