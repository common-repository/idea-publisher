<?php

namespace IdeaPublisher\Social\Platforms\Minds {

    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\Notifier;
    use IdeaPublisher\Core\Translator;
    use IdeaPublisher\Http\HttpClient;
    use IdeaPublisher\Social\Authenticator;
    use IdeaPublisher\Social\LoginForm;
    use IdeaPublisher\Social\LoginFormRetriever;
    use IdeaPublisher\Social\PlatformFacade;

    class MindsFacade extends PlatformFacade
    {
        private ?Authenticator $authenticator;
        private MindsPublisher $publisher;
        private ?LoginForm $loginForm;

        public function __construct(Authenticator $authenticator = null)
        {
            $this->authenticator = $authenticator;
            $this->loginForm = null;
        }

        public function name(): NonEmptyString
        {
            return MindsConstants::platformName();
        }

        public function initialiseAuthenticator(
            Translator $translator,
            Notifier $notifier,
            KeyValueStore $store,
            LoginFormRetriever $loginFormRetriever,
            HttpClient $httpClientDelegate
        ): void {
            $mindsHttpClient = new MindsHttpClient($httpClientDelegate);

            if ($this->authenticator === null) {
                $this->authenticator = new MindsAuthenticator(
                    $translator,
                    $notifier,
                    $store,
                    $loginFormRetriever,
                    $mindsHttpClient
                );
            }

            $this->publisher = new MindsPublisher($this->authenticator(), $mindsHttpClient);
        }

        public function authenticator(): Authenticator
        {
            return $this->authenticator;
        }

        public function loginForm(): LoginForm
        {
            if (!($this->loginForm instanceof LoginForm)) {
                $this->loginForm = LoginForm::builder()
                    ->setTitle($this->name())
                    ->addTextField('username', 'Username:')
                    ->addPasswordField('password', 'Password:')
                    ->addRadioField(
                        'mfa_type', '2FA method:', array(
                        array('key' => 'none', 'label' => 'None'),
                        array('key' => 'device', 'label' => 'Device'),
                        array('key' => 'sms', 'label' => 'SMS')
                        )
                    )
                    ->addTextField('mfa_code', '2FA code (only if Device or SMS):')
                    ->addHiddenField($this->authenticator()->authenticationKey(), NonEmptyString::__('true'))
                    ->build($this->outputEscaper(), $this->inputSanitizer(), $this->translator());
            }

            return $this->loginForm;
        }

        public function publish(Post $post): void
        {
            $this->publisher->publish($post);
        }

        public function disconnect(): void
        {
            $this->authenticator()->disconnect();
        }
    }
}
