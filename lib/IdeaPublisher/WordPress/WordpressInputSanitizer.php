<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Core\InputSanitizer;

    class WordpressInputSanitizer implements InputSanitizer
    {
        public function sanitizeTextInput(?string $input): ?string
        {
            if ($input === null) {
                return null;
            }

            return sanitize_text_field(strval($input));
        }

        public function sanitizeKey(?string $input): ?string
        {
            if ($input === null) {
                return null;
            }
            
            return sanitize_key(strval($input));
        }
    }
}
