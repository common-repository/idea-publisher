<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Http\HttpClient;
    use IdeaPublisher\Http\HttpRequest;
    use IdeaPublisher\Http\HttpResponse;

    class WordPressHttpClient extends HttpClient
    {
        public function custom(string $verb, HttpRequest $request) : HttpResponse
        {
            $wp_request = array_merge(
                array('method' => $verb),
                self::convertRequestToAssociativeArray($request)
            );

            $wp_response = wp_remote_request($request->url(), $wp_request);

            $body = $wp_response['body'];
            $code = intval($wp_response['response']['code']);

            return new HttpResponse($code, $body);
        }

        private static function convertRequestToAssociativeArray(HttpRequest $request) : array
        {
            return array(
                'headers'     => $request->headers(),
                'body'        => $request->body(),
                'timeout'     => $request->timeout(),
                'redirection' => $request->redirection(),
                'httpversion' => $request->httpVersion(),
                'blocking'    => $request->blocking(),
                'cookies'     => $request->cookies(),
                'compress'    => $request->shouldCompress(),
                'decompress'    => $request->shouldDecompressResponse(),
                'sslverify' => !getenv('IDEAPUBLISHER_NO_SSL_VERIFICATION')
            );
        }
    }
}
