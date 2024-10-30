<?php

namespace IdeaPublisher\Core {

    interface AdminFacade extends Notifier
    {
        public function getAuthenticationLink() : NonEmptyString;
    }
}

