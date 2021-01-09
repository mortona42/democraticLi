<?php

namespace Drupal\title_field_for_manage_display\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\BasicStringFormatter;
use Drupal\Core\Link;
use Drupal\Core\Template\Attribute;

/**
 * Plugin implementation of the 'title_style_text_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "title_value_field_formatter",
 *   label = @Translation("Title"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class TitleStyleTextFieldFormatter extends BasicStringFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $attributes = new Attribute();
    $classes = $this->getSetting('classes');
    if (!empty($classes)) {
      $attributes->addClass($classes);
    }

    $parent = $items->getParent()->getValue();

    $text = $parent->label();

    if ($this->getSetting('linked')) {
      $text = Link::fromTextAndUrl($text, $parent->toUrl())->toString();
    }
    $output[] = [
      '#type' => 'html_tag',
      '#tag' => $this->getSetting('tag'),
      '#attributes' => $attributes->toArray(),
      '#value' => $text,
    ];
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $heading_options = [
      'span' => 'span',
      'div' => 'div',
    ];
    foreach (range(1, 5) as $level) {
      $heading_options['h' . $level] = 'H' . $level;
    }
    $form['tag'] = [
      '#title' => $this->t('Tag'),
      '#type' => 'select',
      '#description' => $this->t('Select the tag which will be wrapped around the title.'),
      '#options' => $heading_options,
      '#default_value' => $this->getSetting('tag'),
    ];
    $form['linked'] = [
      '#title' => $this->t('Link to the Content'),
      '#type' => 'checkbox',
      '#description' => $this->t('Wrap the title with a link to the content.'),
      '#default_value' => $this->getSetting('linked'),
    ];
    $form['classes'] = [
      '#title' => $this->t('Classes'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('classes'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'tag' => 'h2',
      'linked' => '1',
      'classes' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = $this->getSettings();
    $replacements = ['@tag' => $settings['tag']];
    if ($settings['linked']) {
      $summary[] = $this->t('Display as @tag, linked to content', $replacements);
    }
    else {
      $summary[] = $this->t('Display as @tag', $replacements);
    }
    if (!empty($settings['classes'])) {
      $summary[] = $this->t('Classes: @classes', ['@classes' => $settings['classes']]);
    }

    return $summary;
  }

}