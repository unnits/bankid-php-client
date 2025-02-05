<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum AuthorizationUriQueryParameter: string
{
    case ApprovalPrompt = 'approval_prompt';
    case Scope = 'scope';
    case CodeChallengeMethod = 'code_challenge_method';
    case ResponseType = 'response_type';
    case AcrValue = 'acr_value';
    case State = 'state';
    case BankId = 'bank_id';
    case ClientId = 'client_id';
    case RedirectUri = 'redirect_uri';
    case RequestUri = 'request_uri';
    case Nonce = 'nonce';
}
