<?php

declare(strict_types=1);

namespace Unnits\BankId;

use Stringable;
use Unnits\BankId\DTO\IdentityToken;
use Unnits\BankId\Enums\LogoutUriQueryParameter as QueryParam;

final class LogoutUri implements Stringable
{
    public function __construct(
        private readonly string $baseUri,
        private readonly string|IdentityToken $idTokenHint,
        private readonly ?string $redirectUri = null,
        private readonly ?string $state = null,
    ) {
        //
    }

    public function __toString(): string
    {
        $params = [];

        $params[QueryParam::IdTokenHint->value] = $this->idTokenHint instanceof IdentityToken
            ? $this->idTokenHint->rawValue ?? ''
            : strval($this->idTokenHint);

        if ($this->redirectUri !== null) {
            $params[QueryParam::RedirectUri->value] = $this->redirectUri;
        }

        if ($this->state !== null) {
            $params[QueryParam::State->value] = $this->state;
        }

        return sprintf(
            '%s/logout?%s',
            trim($this->baseUri, '/'),
            http_build_query($params),
        );
    }
}
