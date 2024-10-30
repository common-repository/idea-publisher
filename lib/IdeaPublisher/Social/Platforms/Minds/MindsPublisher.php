<?php

namespace IdeaPublisher\Social\Platforms\Minds {

    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Social\Publisher;

    class MindsPublisher extends Publisher
    {
        private MindsAuthenticator $mindsAuthenticator;
        private MindsHttpClient $mindsHttpClient;

        public function __construct(
            MindsAuthenticator $mindsAuthenticator,
            MindsHttpClient $mindsHttpClient
        ) {
            $this->mindsAuthenticator = $mindsAuthenticator;
            $this->mindsHttpClient = $mindsHttpClient;
        }

        protected function attemptPublishing(Post $post): bool
        {
            if ($this->mindsAuthenticator->hasAccessToken()) {
                $access_token = $this->mindsAuthenticator->accessToken();

                return $this->mindsHttpClient->publish($access_token, $post);
            }
        }

        protected function platformName() : NonEmptyString
        {
            return MindsConstants::platformName();
        }
    }
}
