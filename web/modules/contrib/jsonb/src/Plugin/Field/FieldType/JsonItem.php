<?php

namespace Drupal\jsonb\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'json' field type.
 *
 * @FieldType(
 *   id = "json",
 *   label = @Translation("JSON"),
 *   description = @Translation("This field stores a JSON object or an array of JSON objects."),
 *   category = @Translation("Document"),
 *   default_widget = "jsonb_textarea",
 *   default_formatter = "jsonb_default"
 * )
 */
class JsonItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'json',
          'pgsql_type' => 'json',
          'mysql_type' => 'json',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return [
      'value' => DataDefinition::create('string')
        ->setLabel(new TranslatableMarkup('JSON value')),
    ];
  }

}
