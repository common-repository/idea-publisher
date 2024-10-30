<?php

namespace IdeaPublisher\Core {

    use IdeaPublisher\Core\NonEmptyString;

    interface KeyValueStore
    {
        public function containsKey(NonEmptyString $key) : bool;
        public function remove(NonEmptyString $key) : void;
        public function set(NonEmptyString $key, NonEmptyString $value) : void;
        public function get(NonEmptyString $key) : NonEmptyString;
    }
}

