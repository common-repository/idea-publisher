<?php

namespace IdeaPublisher\Core {

    interface Translator
    {
        public function __(NonEmptyString $text, NonEmptyString ...$parameters) : NonEmptyString;
    }
}

