<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\AdminFacade;
    use IdeaPublisher\Core\InputSanitizer;
    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\OutputEscaper;
    use IdeaPublisher\Core\Translator;
    use IdeaPublisher\Http\HttpClient;

    class SocialsFacade
    {
        private AdminFacade $admin;
        private Translator $translator;
        private array $platforms;

        public function __construct(
            AdminFacade $admin,
            KeyValueStore $store,
            OutputEscaper $outputEscaper,
            InputSanitizer $inputSanitizer,
            Translator $translator,
            HttpClient $httpClientDelegate,
            PlatformFacade ...$platforms
        ) {
            $this->admin = $admin;
            $this->translator = $translator;
            $this->platforms = $platforms;

            foreach ($platforms as $platform) {
                $platform->initialise(
                    $outputEscaper,
                    $inputSanitizer,
                    $translator,
                    $admin,
                    $store,
                    $httpClientDelegate
                );
            }
        }

        private function getAuthenticatedPlatforms(): array
        {
            return array_filter(
                $this->platforms,
                function ($platform) {
                    return $platform->isAuthenticated();
                }
            );
        }

        private function getNonAuthenticatedPlatforms(): array
        {
            return array_filter(
                $this->platforms,
                function ($platform) {
                    return !$platform->isAuthenticated();
                }
            );
        }

        public function displayAuthenticationWarningIfNeeded(): void
        {
            $nonAuthenticatedPlatforms = $this->getNonAuthenticatedPlatforms();

            if (!empty($nonAuthenticatedPlatforms)) {
                $this->admin->warn(
                    $this->translator->__(
                        NonEmptyString::__('Don\'t forget to <a href="%1$s">authenticate</a> with the following accounts: %2$s'),
                        $this->admin->getAuthenticationLink(),
                        NonEmptyString::__(
                            implode(
                                ", ",
                                array_map(
                                    function ($platform) {
                                        return $platform->name()->value();
                                    },
                                    $nonAuthenticatedPlatforms
                                )
                            )
                        )
                    )
                );
            }
        }

        public function renderAuthenticatedProfiles(): void
        {
            foreach ($this->getAuthenticatedPlatforms() as $platform) {
                $platform->renderProfileGreetings();
            }
        }

        public function renderLoginForms(): void
        {
            foreach ($this->getNonAuthenticatedPlatforms() as $platform) {
                $platform->renderLoginForm();
            }
        }

        function disconnectAllProfiles(): void
        {
            foreach ($this->platforms as $platform) {
                $platform->disconnect();
            }
        }

        function publish(Post $post): void
        {
            foreach ($this->getAuthenticatedPlatforms() as $platform) {
                $platform->publish($post);
            }
        }
    }
}
