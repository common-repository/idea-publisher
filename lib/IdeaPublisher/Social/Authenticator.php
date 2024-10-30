<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\Notifier;
    use IdeaPublisher\Core\Translator;

    abstract class Authenticator
    {
        private Translator $translator;
        private Notifier $notifier;
        private ?Profile $profile;
        private KeyValueStore $store;
        private LoginFormRetriever $loginFormRetriever;

        public function __construct(
            Translator $translator,
            Notifier $notifier,
            KeyValueStore $store,
            LoginFormRetriever $loginFormRetriever
        ) {
            $this->translator = $translator;
            $this->notifier = $notifier;
            $this->store = $store;
            $this->loginFormRetriever = $loginFormRetriever;
            $this->profile = $this->loadProfile();
        }

        public function updateProfile() : void
        {
            $this->refreshProfileIfNeeded();
            $this->authenticateIfRequested();
            $this->disconnectIfRequested();
        }

        protected abstract function platformName(): NonEmptyString;

        protected abstract function loadProfile(): ?Profile;

        public function profile(): ?Profile
        {
            return $this->profile;
        }

        public function isAuthenticated() : bool
        {
            return $this->profile instanceof Profile;
        }

        protected function notifier() : Notifier
        {
            return $this->notifier;
        }

        protected function translator() : Translator
        {
            return $this->translator;
        }

        protected function store() : KeyValueStore
        {
            return $this->store;
        }

        protected function formSubmission() : array
        {
            return $this->loginFormRetriever->loginForm()->submission();
        }
        
        protected function authenticateIfRequested(): void
        {
            if ($this->shouldAuthenticate()) {
                $this->profile = $this->authenticate();

                if ($this->profile instanceof Profile) {
                    $this->displaySuccessfulAuthenticationMessage();
                } else {
                    $this->displayFailedAuthenticationMessage();
                }
            }
        }

        private function displaySuccessfulAuthenticationMessage(): void
        {
            $successfulAuthenticationMessage = $this->translator()->__(NonEmptyString::__('Authentication successful.'));
            $this->notifier()->notifySuccess($successfulAuthenticationMessage);
        }

        private function displayFailedAuthenticationMessage(): void
        {
            $failedAuthenticationMessage = $this->translator()->__(
                NonEmptyString::__('Could not connect to your %1$s account.'),
                $this->platformName()
            );
            $this->notifier()->warn($failedAuthenticationMessage);
        }

        public function authenticationKey(): NonEmptyString
        {
            return NonEmptyString::__($this->platformName() . '_authenticate');
        }

        private function shouldAuthenticate(): bool
        {
            return isset($_POST[$this->authenticationKey()->value()]);
        }

        protected abstract function authenticate(): ?Profile;

        protected function disconnectIfRequested(): void
        {
            if ($this->shouldDisconnect()) {
                $this->disconnect();
            }
        }

        public function disconnectionKey(): NonEmptyString
        {
            return NonEmptyString::__($this->platformName() . '_disconnect');
        }

        private function shouldDisconnect(): bool
        {
            return isset($_POST[$this->disconnectionKey()->value()]);
        }

        public function disconnect(): void
        {
            $this->profile->clear();
            $this->profile = null;
        }

        public function refreshProfileIfNeeded(): void
        {
            if ($this->shouldRefreshProfile()) {
                print_r('Refreshing');
                $this->profile = $this->refreshProfile();
            }
        }

        protected function shouldRefreshProfile(): bool
        {
            return false;
        }

        protected function refreshProfile(): ?Profile
        {
            return null;
        }
    }
}
