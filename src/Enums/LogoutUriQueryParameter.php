<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum LogoutUriQueryParameter: string
{
    case IdTokenHint = 'id_token_hint';
    case RedirectUri = 'post_logout_redirect_uri';
    case State = 'state';
}
