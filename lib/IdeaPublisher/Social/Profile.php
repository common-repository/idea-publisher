<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Core\NonEmptyString;

    abstract class Profile
    {
        public abstract function clear() : void;
        public abstract function platformName(): NonEmptyString;
        public abstract function displayName(): NonEmptyString;
        public abstract function url(): NonEmptyString;
        public abstract function imageUrl(): NonEmptyString;
    }
}
