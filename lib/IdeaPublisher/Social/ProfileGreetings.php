<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\OutputEscaper;
    use IdeaPublisher\Core\Translator;

    class ProfileGreetings
    {
        private OutputEscaper $outputEscaper;

        function __construct(
            OutputEscaper $outputEscaper
        ) {
            $this->outputEscaper = $outputEscaper;
        }

        public function render(Translator $translator, Authenticator $authenticator)
        {
            $profile = $authenticator->profile();
            ?>
            <div>
                <a href='<?php echo $this->outputEscaper->cleanUrl($profile->url()); ?>' target='_blank'>
                    <h2><?php
                        echo $this->outputEscaper->escapeForTrustedHtml(
                            sprintf(
                                $translator->__(
                                    NonEmptyString::__('Ready to publish on %1$s as %2$s'),
                                    $profile->platformName(),
                                    $profile->displayName()
                                )
                            )
                        );
                        ?></h2>
                    <img src="<?php echo $this->outputEscaper->cleanUrl($profile->imageUrl()); ?>" />
                </a>
                <form method="POST">
                    <input type="hidden" name="<?php echo $this->outputEscaper->escapeForAttribute($authenticator->disconnectionKey()); ?>" value="true" />
                    <input type="submit" value="<?php echo $translator->__(NonEmptyString::__('Disconnect')); ?>">
                </form>
            </div>
            <?php
        }
    }
}
