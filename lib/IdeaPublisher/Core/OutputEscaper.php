<?php

namespace IdeaPublisher\Core {

    interface OutputEscaper
    {
        public function escapeForAttribute(string $outputCandidate) : string;
        public function escapeForTextField(string $outputCandidate) : string;
        public function escapeForTrustedHtml(string $outputCandidate) : string;
        public function cleanUrl(string $outputCandidate) : string;
    }
}

