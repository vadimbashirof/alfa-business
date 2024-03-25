<?php

namespace App\Services\AlfaBusiness\Request;

class Request
{
    public function __construct(
        private string $endpoint,
        private string $accept,
        private string $contentType,
        private string $method,
        private array $params,
        private bool $isBearer,
        private ?string $bearer = null,
        private bool $isClientId = false,
        private bool $isClientSecret = false,
        private bool $isRedirectUri = false,
        private ?string $redirectEndpoint = null,
    ) {
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function isBearer(): bool
    {
        return $this->isBearer;
    }

    public function getBearer(): ?string
    {
        return $this->bearer;
    }

    public function isClientId(): bool
    {
        return $this->isClientId;
    }

    public function isClientSecret(): bool
    {
        return $this->isClientSecret;
    }

    public function isRedirectUri(): bool
    {
        return $this->isRedirectUri;
    }

    public function getRedirectEndpoint(): ?string
    {
        return $this->redirectEndpoint;
    }
}
