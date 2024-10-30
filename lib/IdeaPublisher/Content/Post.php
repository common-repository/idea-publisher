<?php

namespace IdeaPublisher\Content {

    use IdeaPublisher\Core\NonEmptyString;

    interface Post
    {
        public function title() : string;
        public function message() : string;
        public function metaDescription() : string;
        public function url() : string;
        public function thumbnail() : string;
        public function tags() : array;
        public function formattedTags() : array;
        
        public function isPublishedWith(NonEmptyString $platform_name) : bool;
        public function markAsPublishedWith(NonEmptyString $platform_name) : void;
    }
}
