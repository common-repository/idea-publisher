<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Content\Post;
    use IdeaPublisher\Core\NonEmptyString;

    abstract class Publisher
    {
        public final function publish(Post $post): void
        {
            $platform_name = $this->platformName();

            if ($post->isPublishedWith($platform_name)) {
                return;
            }

            if ($this->attemptPublishing($post)) {
                $post->markAsPublishedWith($platform_name);
            }
        }

        protected abstract function attemptPublishing(Post $post): bool;
        protected abstract function platformName() : NonEmptyString;
    }
}
