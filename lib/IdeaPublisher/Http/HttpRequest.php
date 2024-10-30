<?php

namespace IdeaPublisher\Http {

    use IdeaPublisher\Core\NonEmptyString;

    class HttpRequest
    {
        private string $url;
        private ?array $body;
        private array $headers;

        public static function create(
            NonEmptyString $url,
            ?array $body = null,
            array $headers = array()
        ): HttpRequest {
            return new HttpRequest($url, $body, $headers);
        }

        private function __construct(NonEmptyString $url, ?array $body = null, array $headers = array())
        {
            $this->url = $url->value();
            $this->body = $body;
            $this->headers = $headers;
        }

        public function url(): string
        {
            return $this->url;
        }

        public function body(): ?array
        {
            return $this->body;
        }

        public function headers(): array
        {
            return $this->headers;
        }

        public function cookies(): array
        {
            return array();
        }

        public function timeout(): int
        {
            return 5;
        }

        public function redirection(): int
        {
            return 5;
        }

        public function httpVersion(): string
        {
            return '1.0';
        }

        public function blocking(): bool
        {
            return true;
        }

        public function shouldCompress(): bool
        {
            return true;
        }

        public function shouldDecompressResponse(): bool
        {
            return true;
        }
    }
}
