<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Validators\StringValidator;

    class WordPressKeyValueStore implements KeyValueStore
    {
        public function containsKey(NonEmptyString $key): bool
        {
            return StringValidator::isNonEmptyString(get_option(self::strengthenKey($key)));
        }

        public function remove(NonEmptyString $key): void
        {
            delete_option(self::strengthenKey($key));
        }

        public function set(NonEmptyString $key, NonEmptyString $value): void
        {
            $this->remove($key);
            add_option(self::strengthenKey($key), $value->value());
        }

        public function get(NonEmptyString $key): NonEmptyString
        {
            return NonEmptyString::__(get_option(self::strengthenKey($key)));
        }

        private static function strengthenKey(NonEmptyString $key) : string
        {
            return 'IdeaPublisher_WordPress_' . $key;
        }
    }
}
