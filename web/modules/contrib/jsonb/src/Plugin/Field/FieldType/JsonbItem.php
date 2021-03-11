<?php

namespace Drupal\jsonb\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'jsonb' field type.
 *
 * @FieldType(
 *   id = "jsonb",
 *   label = @Translation("JSONB"),
 *   description = @Translation("This field stores a JSON object or an array of JSON objects."),
 *   category = @Translation("Document"),
 *   default_widget = "jsonb_textarea",
 *   default_formatter = "jsonb_default"
 * )
 */
class JsonbItem extends JsonItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'text',
          'pgsql_type' => 'jsonb',
          'mysql_type' => 'json',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return [
      'value' => DataDefinition::create('string')
        ->setLabel(new TranslatableMarkup('JSONB value')),
    ];
  }

}
