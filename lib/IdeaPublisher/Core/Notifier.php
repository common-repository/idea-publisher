<?php

namespace IdeaPublisher\Core {

    interface Notifier
    {
        public function notifySuccess(NonEmptyString $message) : void;
        public function warn(NonEmptyString $message) : void;
    }
}

