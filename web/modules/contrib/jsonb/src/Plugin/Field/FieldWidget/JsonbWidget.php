<?php

namespace Drupal\jsonb\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json as Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'jsonb_textarea' widget.
 *
 * @FieldWidget(
 *   id = "jsonb_textarea",
 *   label = @Translation("JSONB Object"),
 *   field_types = {
 *     "jsonb",
 *     "json",
 *   }
 * )
 */
class JsonbWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['#element_validate'][] = [static::class, 'validateJsonStructure'];
    return $widget;
  }

  /**
   * Validates the input to see if it is a properly formatted JSON object.
   *
   * If not, PgSQL will throw fatal errors upon insert.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateJsonStructure(array &$element, FormStateInterface $form_state) {
    if (mb_strlen($element['value']['#value'])) {
      Json::decode($element['value']['#value']);

      if (json_last_error() !== JSON_ERROR_NONE) {
        $form_state->setError($element['value'], new TranslatableMarkup('Field "@name" must contain a valid JSON object. Error: %error', [
          '@name' => $element['#title'],
          '%error' => json_last_error_msg(),
        ]));
      }
    }
  }

}
