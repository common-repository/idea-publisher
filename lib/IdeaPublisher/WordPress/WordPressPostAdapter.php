<?php

namespace IdeaPublisher\WordPress {

    use WP_Post;
    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Validators\StringValidator;

    class WordPressPostAdapter implements Post
    {
        private const SIDEBAR_MESSAGE_META_KEY = 'ideapublisher_sidebar_message';

        private const DEFAULT_META_DESCRIPTION_WORD_COUNT = 55;

        private const PUBLISHED_META_KEY = 'ideapublisher_ispublished';
        private const PUBLISHED_META_VALUE = 'published';


        private WP_Post $post;

        public static function fromPost(WP_Post $post): WordPressPostAdapter
        {
            return new WordPressPostAdapter($post);
        }

        private function __construct(WP_Post $post)
        {
            $this->post = $post;
        }

        public function title(): string
        {
            return $this->getPostField('post_title');
        }

        private function excerpt(): string
        {
            return $this->getPostField('post_excerpt');
        }

        public function message(): string
        {
            $sidebar_message = $this->getMetaField(NonEmptyString::__(self::SIDEBAR_MESSAGE_META_KEY));

            if (StringValidator::isNonEmptyString($sidebar_message)) {
                return $sidebar_message;
            }

            return $this->metaDescription();
        }

        public function metaDescription(): string
        {
            $yoast_seo_meta_description = $this->getMetaField(NonEmptyString::__('_yoast_wpseo_metadesc'));

            if (StringValidator::isNonEmptyString($yoast_seo_meta_description)) {
                return $yoast_seo_meta_description;
            }

            $excerpt = $this->excerpt();

            if (StringValidator::isNonEmptyString($excerpt)) {
                return $excerpt;
            }

            return wp_trim_words($this->post->post_content, self::DEFAULT_META_DESCRIPTION_WORD_COUNT);
        }

        public function url(): string
        {
            return wp_get_canonical_url($this->post);
        }

        public function thumbnail(): string
        {
            return get_the_post_thumbnail_url($this->post, 'post-thumbnail');
        }

        public function tags(?int $max_count = null): array
        {
            $wp_tags = get_the_tags($this->post);

            if ($wp_tags) {
                return array_map(
                    function ($tag) {
                        return $tag->name;
                    },
                    array_slice($wp_tags, 0, $max_count)
                );
            }

            return array();
        }

        public function formattedTags(?int $max_count = null): array
        {
            return array_map(
                function ($unclean_tag) {
                    return trim(preg_replace('/[^a-zA-Z0-9_]+/', '_', $unclean_tag), '_');
                },
                $this->tags($max_count)
            );
        }

        public function isPublishedWith(NonEmptyString $platform_name): bool
        {
            return strcmp(
                strval(self::PUBLISHED_META_VALUE),
                $this->getMetaField(NonEmptyString::__(self::PUBLISHED_META_KEY))
            ) === 0;
        }

        public function markAsPublishedWith(NonEmptyString $platform_name): void
        {
            $this->setMetaField(
                NonEmptyString::__(self::PUBLISHED_META_KEY),
                NonEmptyString::__(self::PUBLISHED_META_VALUE)
            );
        }

        private function getMetaField(NonEmptyString $key): ?string
        {
            return get_post_meta($this->post->ID, sprintf('%1$s', $key), true);
        }

        private function setMetaField(NonEmptyString $key, NonEmptyString $value): void
        {
            update_post_meta($this->post->ID, sprintf('%1$s', $key), $value->value());
        }

        private function getPostField($field_name)
        {
            return sanitize_post_field($field_name, $this->post->$field_name, $this->post->ID, 'display');
        }

        public static function init()
        {
            self::registerMetaFields(
                self::SIDEBAR_MESSAGE_META_KEY,
                self::PUBLISHED_META_KEY,
            );
        }

        private static function registerMetaFields(string ...$fieldKeys)
        {
            foreach ($fieldKeys as $fieldKey) {
                register_post_meta(
                    'post',
                    $fieldKey,
                    array(
                        'show_in_rest' => true,
                        'single' => true,
                        'type' => 'string',
                    )
                );
            }
        }
    }
}
