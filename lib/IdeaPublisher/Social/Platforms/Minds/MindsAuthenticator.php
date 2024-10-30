<?php

namespace IdeaPublisher\Social\Platforms\Minds {

    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\Notifier;
    use IdeaPublisher\Core\Translator;
    use IdeaPublisher\Social\Authenticator;
    use IdeaPublisher\Social\LoginFormRetriever;
    use IdeaPublisher\Social\Profile;

    class MindsAuthenticator extends Authenticator
    {
        private const ACCESS_TOKEN_KEY = 'minds_access_token';
        private const REFRESH_TOKEN_KEY = 'minds_refresh_token';

        private MindsHttpClient $mindsHttpClient;

        public function __construct(
            Translator $translator,
            Notifier $notifier,
            KeyValueStore $store,
            LoginFormRetriever $loginFormRetriever,
            MindsHttpClient $mindsHttpClient
        ) {
            parent::__construct($translator, $notifier, $store, $loginFormRetriever);

            $this->mindsHttpClient = $mindsHttpClient;
        }

        protected function platformName(): NonEmptyString
        {
            return MindsConstants::platformName();
        }

        protected function authenticate(): ?Profile
        {
            $formSubmission = $this->formSubmission();

            $response = $this->mindsHttpClient->createToken(
                $formSubmission['username'],
                $formSubmission['password'],
                $formSubmission['mfa_type'],
                $formSubmission['mfa_code'],
                function ($body) {
                    $this->notifier()->warn($this->translator()->__(NonEmptyString::__('Something went wrong: %1$s'), NonEmptyString::__($body['message'])));
                }
            );

            return $this->handleTokenRequestAftermath($response);
        }

        private function storeTokens(array $response): void
        {
            $this->setAccessToken($response['access_token']);
            $this->setRefreshToken($response['refresh_token']);
        }
        
        protected function shouldRefreshProfile(): bool
        {
            debug('should refresh profile');

            if (!$this->hasAccessToken()) {
                debug('no access token');
                return false;
            }
            
            return is_null($this->getProfile($this->accessToken()));
        }

        protected function refreshProfile(): ?Profile
        {
            if (!$this->hasRefreshToken()) {
                debug('no refresh token');
                return null;
            }

            debug('refreshing tokens');
            $response = $this->mindsHttpClient->refresh($this->refreshToken());

            return $this->handleTokenRequestAftermath($response);
        }

        private function handleTokenRequestAftermath($response): ?Profile
        {
            if (!isset($response['access_token']) || !isset($response['refresh_token'])) {
                debug('no tokens in response'.json_encode($response));
                return null;
            }

            debug('tokens in response'.json_encode($response));
            
            $this->storeTokens($response);

            return $this->getProfile(NonEmptyString::__($response['access_token']));
        }

        private function getProfile(NonEmptyString $access_token): ?Profile
        {
            $response = $this->mindsHttpClient->userInfo($access_token->value());

            if ($response == null) {
                debug('no userinfo with '.$access_token->value());
                return null;
            }

            debug('got the userinfo with '.$access_token->value());
            return new MindsProfile($this->store(), $response);
        }

        protected function loadProfile(): ?Profile
        {
            $profile = new MindsProfile($this->store());

            if ($profile->exists()) {
                return $profile;
            }

            return null;
        }

        public function hasAccessToken(): bool
        {
            return $this->store()->containsKey(NonEmptyString::__(self::ACCESS_TOKEN_KEY));
        }

        private function setAccessToken(string $access_token): void
        {
            $this->store()->set(NonEmptyString::__(self::ACCESS_TOKEN_KEY), NonEmptyString::__($access_token));
        }

        public function accessToken(): NonEmptyString
        {
            return $this->store()->get(NonEmptyString::__(self::ACCESS_TOKEN_KEY));
        }

        private function hasRefreshToken(): bool
        {
            return $this->store()->containsKey(NonEmptyString::__(self::REFRESH_TOKEN_KEY));
        }

        private function setRefreshToken(string $refresh_token): void
        {
            $this->store()->set(NonEmptyString::__(self::REFRESH_TOKEN_KEY), NonEmptyString::__($refresh_token));
        }

        private function refreshToken(): NonEmptyString
        {
            return $this->store()->get(NonEmptyString::__(self::REFRESH_TOKEN_KEY));
        }

        public function disconnect(): void
        {
            parent::disconnect();
            $this->store()->remove(NonEmptyString::__(self::ACCESS_TOKEN_KEY));
            $this->store()->remove(NonEmptyString::__(self::REFRESH_TOKEN_KEY));
        }
    }
}
