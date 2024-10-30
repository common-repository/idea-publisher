<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\OutputEscaper;

    class WordPressAdminNotifier
    {
        private OutputEscaper $outputEscaper;

        public function __construct(OutputEscaper $outputEscaper)
        {
            $this->outputEscaper = $outputEscaper;
        }

        public function success(NonEmptyString $message): void
        {
            $this->_notify('success', $message);
        }

        public function warn(NonEmptyString $message): void
        {
            $this->_notify('warning', $message);
        }

        private function _notify(string $type, string $message): void
        {
            add_action(
                'admin_notices', function () use ($type, $message) {
                    ?>
                <div class="notice notice-<?php echo $this->outputEscaper->escapeForAttribute($type); ?> is-dismissible">
                    <p><?php echo $this->outputEscaper->escapeForTrustedHtml($message); ?></p>
                </div>
                    <?php
                }
            );
        }
    }
}
