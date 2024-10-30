<?php 

namespace IdeaPublisher\Http {

    class HttpResponse
    {
        private int $code;
        private string $body;

        public function __construct(int $code, string $body)
        {
            $this->code = $code;
            $this->body = $body;
        }

        function code() : int
        {
            return $this->code;
        }

        function body() : string
        {
            return $this->body;
        }
    }
}
