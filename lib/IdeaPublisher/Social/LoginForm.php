<?php

namespace IdeaPublisher\Social {

    use IdeaPublisher\Core\InputSanitizer;
    use IdeaPublisher\Core\NonEmptyString;
    use IdeaPublisher\Core\OutputEscaper;
    use IdeaPublisher\Core\Translator;

    class LoginForm
    {
        private OutputEscaper $outputEscaper;
        private InputSanitizer $inputSanitizer;
        private Translator $translator;
        private string $title;
        private array $fields;
        private array $radioOptions;
        private array $hiddenValues;

        function __construct(
            OutputEscaper $outputEscaper,
            InputSanitizer $inputSanitizer,
            Translator $translator,
            string $title,
            array $fields,
            array $radioOptions,
            array $hiddenValues
        ) {
            $this->outputEscaper = $outputEscaper;
            $this->inputSanitizer = $inputSanitizer;
            $this->title = $title;
            $this->translator = $translator;
            $this->title = $title;
            $this->fields = $fields;
            $this->radioOptions = $radioOptions;
            $this->hiddenValues = $hiddenValues;
        }

        public final static function builder(): LoginFormBuilder
        {
            return new LoginFormBuilder();
        }

        public final function submission(): array
        {
            $postSubmission = array();

            foreach ($this->fields as $field) {
                $postSubmission[$field['key']] = isset($_POST[$field['key']]) ?
                    $this->inputSanitizer->sanitizeTextInput($_POST[$field['key']]) : '';
            }

            return $postSubmission;
        }

        public final function render(): void
        {
            ?>
            <div class="wrap">
                <h2><?php echo $this->outputEscaper->escapeForTrustedHtml($this->title); ?></h2>
                <form method="POST">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php
                            foreach ($this->fields as $field) {
                                $this->renderField($field);
                            }
                            ?>
                        </tbody>
                    </table>
                    <input id="ideapublisher_submit" type="submit" value="Authenticate">
                </form>
            </div>
            <?php
        }

        private function renderField(array $field): void
        {
            switch ($field['type']) {
            case 'text':
            case 'password':
                $this->renderTextField($field);
                break;

            case 'radio':
                $this->renderRadioField($field);
                break;

            case 'hidden':
                $this->renderHiddenField($field);
                break;

            default:
                $this->renderCustomField($field);
                break;
            }
        }

        private function renderTextField(array $field): void
        {
            $label = $this->translator->__($field['label']);
            $key = $field['key'];
            $type = $field['type'];
            ?>
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->outputEscaper->escapeForAttribute($key); ?>">
                        <?php echo $this->outputEscaper->escapeForTrustedHtml($label); ?>
                    </label>
                </th>
                <td>
                    <input name="<?php echo $this->outputEscaper->escapeForAttribute($key); ?>" type="<?php echo $this->outputEscaper->escapeForAttribute($type); ?>" id="<?php echo $this->outputEscaper->escapeForAttribute($key); ?>" class="regular-text" value="<?php echo $this->outputEscaper->escapeForTextField($this->getSubmittedValue($key)); ?>" autocomplete=off onfocus="this.removeAttribute('readonly');">
                </td>
            </tr>
            <?php
        }

        private function renderRadioField(array $field): void
        {
            $label = $this->translator->__($field['label']);
            $key = $field['key'];
            $type = $field['type'];
            ?>
            <tr>
                <th scope="row"><label for="<?php echo $this->outputEscaper->escapeForAttribute($key); ?>"><?php echo $this->outputEscaper->escapeForTrustedHtml($label); ?></label></th>
                <td>
                    <?php
                    foreach ($this->radioOptions[$key] as $option) {
                        $optionKey = $option['key'];
                        $optionLabel = $this->translator->__(NonEmptyString::__($option['label']));
                        ?>

                        <input type="<?php echo $this->outputEscaper->escapeForAttribute($type); ?>" id="<?php echo $this->outputEscaper->escapeForAttribute($optionKey); ?>" name="<?php echo $this->outputEscaper->escapeForAttribute($key); ?>" value="<?php echo $this->outputEscaper->escapeForAttribute($optionKey); ?>" <?php echo $this->getSubmittedValue($key) === $optionKey ? 'checked' : '' ?>>
                        <label for="<?php echo $this->outputEscaper->escapeForAttribute($optionKey); ?>"><?php echo $this->outputEscaper->escapeForTrustedHtml($optionLabel); ?></label>

                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }

        private function renderHiddenField(array $field): void
        {
            echo sprintf(
                '<input type="hidden" name="%1$s" value="%2$s" />',
                $this->outputEscaper->escapeForAttribute($field['key']),
                $this->outputEscaper->escapeForTextField($this->hiddenValues[$field['key']])
            );
        }

        protected function renderCustomField(array $field): void
        {
        }

        private function getSubmittedValue($key): string
        {
            return isset($_POST[$key]) ? $this->inputSanitizer->sanitizeTextInput($_POST[$key]) : '';
        }
    }
}
