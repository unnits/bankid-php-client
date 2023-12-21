<?php

declare(strict_types=1);

namespace Unnits\BankId\Http;

use GuzzleHttp\Utils;
use PHPUnit\Event\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Unnits\BankId\Traceable;

class BankIdResponse implements Traceable
{
    private ?ResponseBody $body = null;

    public function __construct(
        public readonly ResponseInterface $originalResponse,
    ) {
        //
    }

    public function getBody(): ResponseBody
    {
        if ($this->body === null) {
            $body = $this->originalResponse->getBody()->getContents();

            if ($body === '') {
                $this->body = new ResponseBody([]);
            } else {
                try {
                    $body = Utils::jsonDecode($body, assoc: true);

                    $this->body = new ResponseBody(
                        is_array($body)
                            ? $body
                            : ['content' => $body]
                    );
                } catch (InvalidArgumentException) {
                    $this->body = new ResponseBody([]);
                }
            }
        }

        return $this->body;
    }

    public function getTraceId(): ?string
    {
        return $this->originalResponse->hasHeader('traceId')
            ? $this->originalResponse->getHeaderLine('traceId')
            : null;
    }
}
