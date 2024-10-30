<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Content\Post;
    use WP_Post;
    use IdeaPublisher\Core\AdminFacade;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Http\HttpClient;
    use IdeaPublisher\Social\SocialsFacade;
    use IdeaPublisher\Social\PlatformFacade;

    class WordPressAdminFacade implements AdminFacade
    {
        private const IDEAPUBLISHER_PARENT_SLUG = 'tools.php';
        private const IDEAPUBLISHER_MENU_SLUG = 'idea-publisher';
        private const PLUGIN_NAME = 'Idea Publisher';

        private WordPressKeyValueStore $store;
        private WordPressTranslator $translator;
        private WordPressAdminNotifier $notifier;
        private SocialsFacade $socials;

        public static function createWithPlatforms(PlatformFacade ...$platforms) : WordPressAdminFacade
        {
            return new WordPressAdminFacade(new WordPressHttpClient(), ...$platforms);
        }

        public static function create(HttpClient $httpClient, PlatformFacade ...$platforms) : WordPressAdminFacade
        {
            return new WordPressAdminFacade($httpClient, ...$platforms);
        }

        private function __construct(HttpClient $httpClient, PlatformFacade ...$platforms)
        {
            $outputEscaper = new WordpressOutputEscaper();
            $this->store = new WordPressKeyValueStore();
            $this->translator = new WordPressTranslator();
            $this->notifier = new WordPressAdminNotifier($outputEscaper);
            $this->socials = new SocialsFacade($this, $this->store, $outputEscaper, new WordpressInputSanitizer(), $this->translator, $httpClient, ...$platforms);

            add_action('admin_init', array($this, 'adminInit'));
            add_action('admin_menu', array($this, 'adminMenu'));

            add_action('rest_after_insert_post', array($this, 'publishIdeaFromWordPress'));

            register_deactivation_hook(__FILE__,  array($this, 'deactivate'));

            WordPressPostAdapter::init();
        }

        public function adminInit(): void
        {
            global $plugin_page;
            if (is_admin() && $plugin_page !== self::IDEAPUBLISHER_MENU_SLUG) {
                $this->socials->displayAuthenticationWarningIfNeeded();
            }
        }

        public function adminMenu(): void
        {
            add_submenu_page(
                self::IDEAPUBLISHER_PARENT_SLUG,
                self::PLUGIN_NAME,
                self::PLUGIN_NAME,
                'manage_options',
                self::IDEAPUBLISHER_MENU_SLUG,
                array($this, 'renderAdminPage')
            );
        }

        public function publishIdeaFromWordPress(WP_Post $post) : void
        {
            $this->publishIdea(WordPressPostAdapter::fromPost($post));
        }

        public function publishIdea(Post $post) : void
        {
            $this->socials->publish($post);
        }

        public function renderAdminPage() : void
        {
            echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
            $this->socials->renderAuthenticatedProfiles();
            $this->socials->renderLoginForms();
        }

        public function deactivate(): void
        {
            $this->socials->disconnectAllProfiles();
        }

        public function notifySuccess(NonEmptyString $message): void
        {
            $this->notifier->success($message);
        }

        public function warn(NonEmptyString $message): void
        {
            $this->notifier->warn($message);
        }

        public function getAuthenticationLink(): NonEmptyString
        {
            return NonEmptyString::__(
                add_query_arg(
                    array(
                    'page' => self::IDEAPUBLISHER_MENU_SLUG,
                    ),
                    admin_url(self::IDEAPUBLISHER_PARENT_SLUG)
                )
            );
        }
    }
}
