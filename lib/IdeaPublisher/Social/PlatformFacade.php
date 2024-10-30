<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\InputSanitizer;
    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\Notifier;
    use IdeaPublisher\Core\OutputEscaper;
    use IdeaPublisher\Core\Translator;
    use IdeaPublisher\Http\HttpClient;

    abstract class PlatformFacade implements LoginFormRetriever
    {
        private OutputEscaper $outputEscaper;
        private InputSanitizer $inputSanitizer;
        private Translator $translator;
        private ProfileGreetings $profileGreetings;

        public function initialise(
            OutputEscaper $outputEscaper,
            InputSanitizer $inputSanitizer,
            Translator $translator,
            Notifier $notifier,
            KeyValueStore $store,
            HttpClient $httpClientDelegate
        ): void {
            $this->inputSanitizer = $inputSanitizer;
            $this->translator = $translator;
            $this->outputEscaper = $outputEscaper;

            $this->profileGreetings = new ProfileGreetings($this->outputEscaper);

            $this->initialiseAuthenticator(
                $translator,
                $notifier,
                $store,
                $this,
                $httpClientDelegate
            );

            $this->updateProfile();
        }

        public function updateProfile(): void
        {
            $this->authenticator()->updateProfile();
        }

        public abstract function authenticator(): Authenticator;
        public abstract function name(): NonEmptyString;

        public abstract function initialiseAuthenticator(
            Translator $translator,
            Notifier $notifier,
            KeyValueStore $store,
            LoginFormRetriever $loginFormRetriever,
            HttpClient $httpClientDelegate
        ): void;

        public function profile(): ?Profile
        {
            return $this->authenticator()->profile();
        }

        public abstract function loginForm(): LoginForm;

        public function renderLoginForm(): void
        {
            $this->loginForm()->render();
        }

        public function translator(): Translator
        {
            return $this->translator;
        }

        public function isAuthenticated(): bool
        {
            return $this->authenticator()->isAuthenticated();
        }

        public function renderProfileGreetings(): void
        {
            $this->profileGreetings->render($this->translator(), $this->authenticator());
        }

        public abstract function publish(Post $post): void;

        public abstract function disconnect(): void;

        public function inputSanitizer(): InputSanitizer
        {
            return $this->inputSanitizer;
        }

        public function outputEscaper(): OutputEscaper
        {
            return $this->outputEscaper;
        }
    }
}
