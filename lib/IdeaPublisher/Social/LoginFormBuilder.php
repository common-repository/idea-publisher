<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Core\InputSanitizer;
    use IdeaPublisher\Core\Keyword;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\OutputEscaper;
    use IdeaPublisher\Core\Translator;

    class LoginFormBuilder
    {
        private array $fields = [];
        private array $radioOptions = [];
        private array $hiddenValues = [];

        public function setTitle(string $title): LoginFormBuilder
        {
            $this->title = $title;
            return $this;
        }

        private function addField(string $key, string $label, string $type): LoginFormBuilder
        {
            assert(Keyword::__($key) !== null, 'Invalid key, it must match specifications from ' . Keyword::class);

            $this->fields[] = array(
                'type' => $type,
                'key' => $key,
                'label' => NonEmptyString::__($label)
            );

            return $this;
        }

        public function addTextField(string $key, string $label): LoginFormBuilder
        {
            return $this->addField($key, $label, 'text');
        }

        public function addPasswordField(string $key, string $label): LoginFormBuilder
        {
            return $this->addField($key, $label, 'password');
        }

        public function addRadioField(string $key, string $label, array $options): LoginFormBuilder
        {
            $this->addField($key, $label, 'radio');
            $this->radioOptions[$key] = $options;
            return $this;
        }

        public function addHiddenField(string $key, NonEmptyString $value): LoginFormBuilder
        {
            $this->addField($key, '_', 'hidden');
            $this->hiddenValues[$key] = $value;
            return $this;
        }

        public final function build(
            OutputEscaper $outputEscaper,
            InputSanitizer $inputSanitizer,
            Translator $translator
        ): LoginForm {
            return new LoginForm(
                $outputEscaper,
                $inputSanitizer,
                $translator,
                $this->title,
                $this->fields,
                $this->radioOptions,
                $this->hiddenValues
            );
        }
    }
}
