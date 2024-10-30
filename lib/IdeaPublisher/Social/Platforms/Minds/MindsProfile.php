<?php

namespace IdeaPublisher\Social\Platforms\Minds {

    use IdeaPublisher\Core\KeyValueStore;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Social\Profile;

    class MindsProfile extends Profile
    {
        private const DISPLAY_NAME_KEY = 'minds_profile_name';
        private const USERNAME_KEY = 'minds_profile_username';
        private const ID_KEY = 'minds_profile_id';

        private KeyValueStore $store;

        public function __construct(KeyValueStore $store, array $profile_info = null)
        {
            $this->store = $store;

            if ($profile_info !== null) {
                $this->store->set(NonEmptyString::__(self::DISPLAY_NAME_KEY), NonEmptyString::__($profile_info['name']));
                $this->store->set(NonEmptyString::__(self::USERNAME_KEY), NonEmptyString::__($profile_info['username']));
                $this->store->set(NonEmptyString::__(self::ID_KEY), NonEmptyString::__($profile_info['sub']));
            }
        }

        public function clear(): void
        {
            $this->store->remove(NonEmptyString::__(self::DISPLAY_NAME_KEY));
            $this->store->remove(NonEmptyString::__(self::USERNAME_KEY));
            $this->store->remove(NonEmptyString::__(self::ID_KEY));
        }

        public function exists() : bool
        {
            return $this->store->containsKey(NonEmptyString::__(self::ID_KEY));
        }

        public function platformName(): NonEmptyString
        {
            return MindsConstants::platformName();
        }

        public function displayName(): NonEmptyString
        {
            return $this->store->get(NonEmptyString::__(self::DISPLAY_NAME_KEY));
        }

        public function url(): NonEmptyString
        {
            return NonEmptyString::__(sprintf('%1$s/%2$s', MindsConstants::webRoot(), $this->store->get(NonEmptyString::__(self::USERNAME_KEY))));
        }

        public function imageUrl(): NonEmptyString
        {
            return NonEmptyString::__(sprintf('%1$s/icon/%2$s', MindsConstants::cdnRoot(), $this->store->get(NonEmptyString::__(self::ID_KEY))));
        }
    }
}
