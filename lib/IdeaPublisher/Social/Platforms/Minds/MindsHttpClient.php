<?php


namespace IdeaPublisher\Social\Platforms\Minds {

    use Closure;
    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Http\HttpClient;
    use IdeaPublisher\Http\HttpRequest;
    use IdeaPublisher\Http\HttpResponse;

    class MindsHttpClient
    {
        private static array $MFA = array(
            'device' => 'X-MINDS-2FA-CODE',
            'sms' => 'X-MINDS-SMS-2FA-KEY',
            'none' => '',
            '' => ''
        );

        private HttpClient $httpClientDelegate;

        public function __construct(HttpClient $httpClientDelegate)
        {
            $this->httpClientDelegate = $httpClientDelegate;
        }

        public function createToken(?string $username, ?string $password, ?string $mfa_type, ?string $mfa_code, Closure $failure_callback = null): ?array
        {
            return $this->requestToken(
                array(
                    'username' => $username,
                    'password' => $password,
                    'grant_type' => 'password',
                    'client_id' => 'mobile'
                ),
                array(
                    self::$MFA[$mfa_type] => $mfa_code
                ),
                $failure_callback
            );
        }

        public function refresh(string $refresh_token): ?array
        {
            return $this->requestToken(
                array(
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
                'client_id' => 'mobile'
                )
            );
        }

        private function requestToken(array $body, array $headers = array(), Closure $failure_callback = null): ?array
        {
            $response = $this->httpClientDelegate->post(
                HttpRequest::create(MindsConstants::oauthRoot()->concat('/token'), $body, $headers)
            );

            return $this->getBodyIfSuccessful(200, $response, $failure_callback);
        }

        public function userInfo(string $access_token): ?array
        {
            $response = $this->httpClientDelegate->get(
                HttpRequest::create(
                    MindsConstants::oauthRoot()->concat('/userinfo'), null, array(
                    'Authorization' => "Bearer $access_token"
                    )
                )
            );

            return $this->getBodyIfSuccessful(200, $response);
        }

        public function publish(string $access_token, Post $post): bool
        {
            $response = $this->httpClientDelegate->post(
                HttpRequest::create(
                    MindsConstants::webRoot()->concat('/api/v2/newsfeed'),
                    array(
                        'title' => $post->title(),
                        'message' => $post->message(),
                        'description' => $post->metaDescription(),
                        'url' => $post->url(),
                        'thumbnail' => $post->thumbnail(),
                        'tags' => $post->formattedTags(MindsConstants::MAX_ALLOWED_TAGS),
                    ),
                    array(
                        'Authorization' => "Bearer $access_token"
                    )
                )
            );

            return $this->getBodyIfSuccessful(200, $response) != null;
        }

        private function getBodyIfSuccessful(int $expected_code, HttpResponse $response, Closure $failure_callback = null): ?array
        {
            $body = json_decode($response->body(), true);

            if ($response->code() == $expected_code) {
                return $body;
            } else if ($failure_callback instanceof Closure) {
                $failure_callback($body);
            }

            return null;
        }
    }
}
