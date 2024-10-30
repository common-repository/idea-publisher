<?php

namespace IdeaPublisher\Social\Platforms\Minds {

    use IdeaPublisher\Core\NonEmptyString;

    final class MindsConstants
    {
        const MAX_ALLOWED_TAGS = 5;

        const PLATFORM_NAME = 'Minds';
        const WEB_ROOT = 'https://www.minds.com';
        const CDN_ROOT = 'https://cdn.minds.com';
        const OAUTH_ROOT =  self::WEB_ROOT ;

        public static final function platformName(): NonEmptyString
        {
            return NonEmptyString::__(self::PLATFORM_NAME);
        }

        public static final function webRoot(): NonEmptyString
        {
            $webRootOverride = trim(strval(getenv('IDEAPUBLISHER_MINDS_WEB_ROOT_OVERRIDE')));
            return NonEmptyString::__(
                !empty($webRootOverride) ?
                    $webRootOverride :
                    self::WEB_ROOT
            );
        }

        public static final function cdnRoot(): NonEmptyString
        {
            return NonEmptyString::__(self::CDN_ROOT);
        }

        public static final function oauthRoot(): NonEmptyString
        {
            return NonEmptyString::__(self::webRoot() . '/api/v3/oauth');
        }
    }
}
