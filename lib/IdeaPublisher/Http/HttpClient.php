<?php

namespace IdeaPublisher\Http {

    abstract class HttpClient
    {
        public abstract function custom(string $verb, HttpRequest $request) : HttpResponse;

        public function get(HttpRequest $request) : HttpResponse
        {
            return $this->custom(HttpVerbs::HTTP_GET, $request);
        }

        public function post(HttpRequest $request) : HttpResponse
        {
            return $this->custom(HttpVerbs::HTTP_POST, $request);
        }
    }
}
