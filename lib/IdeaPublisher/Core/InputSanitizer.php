<?php

namespace IdeaPublisher\Core {

    interface InputSanitizer
    {
        public function sanitizeTextInput(?string $input): ?string;
        public function sanitizeKey(?string $input): ?string;
    }
}

