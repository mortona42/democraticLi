<?php

namespace Drupal\custom_markup_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Markup' block.
 *
 * @Block(
 *  id = "custom_markup",
 *  admin_label = @Translation("Custom Markup"),
 *  category = @Translation("Custom Blocks")
 * )
 */
class CustomMarkup extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'markup' => [
        'format' => 'full_html',
        'value' => '',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['markup'] = [
      '#type' => 'text_format',
      '#title' => 'Markup',
      '#format' => $this->configuration['markup']['format'],
      '#default_value' => $this->configuration['markup']['value'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['markup'] = $form_state->getValue('markup');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if (!empty($this->configuration['markup']['value'])) {
      $build = [
        '#type' => 'processed_text',
        '#text' => $this->configuration['markup']['value'],
        '#format' => $this->configuration['markup']['format'],
      ];
    }
    return $build;
  }

}
