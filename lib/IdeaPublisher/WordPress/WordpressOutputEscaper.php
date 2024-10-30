<?php

namespace IdeaPublisher\WordPress {
    use IdeaPublisher\Core\OutputEscaper;

    class WordpressOutputEscaper implements OutputEscaper
    {
        public function escapeForAttribute(string $outputCandidate): string
        {
            return esc_attr($outputCandidate);
        }

        public function escapeForTextField(string $outputCandidate): string
        {
            return esc_textarea($outputCandidate);
        }

        public function escapeForTrustedHtml(string $outputCandidate): string
        {
            return wp_kses_post($outputCandidate);
        }

        public function cleanUrl(string $outputCandidate): string
        {
            return esc_url($outputCandidate);
        }
    }
}
