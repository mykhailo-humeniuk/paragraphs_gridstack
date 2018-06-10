<?php

namespace Drupal\paragraphs_gridstack\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Plugin\Field\FieldWidget\InlineParagraphsWidget;

/**
 * .
 *
 * @FieldWidget(
 *   id = "paragraphs_gridstack_widget",
 *   label = @Translation("Gridstack"),
 *   description = @Translation("A paragraphs gridstack form widget."),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class ParagraphsGridstackWidget extends InlineParagraphsWidget {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();


//    return array(
//      'title' => t('Paragraph'),
//      'title_plural' => t('Paragraphs'),
//      'edit_mode' => 'open',
//      'add_mode' => 'dropdown',
//      'form_display_mode' => 'default',
//      'default_paragraph_type' => '',
//    );

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
//  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, array &$form_state) {
//    $element = parent::formElement($items, $delta, $element, $form, $form_state);
//    return $element;
//  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    return $summary;
  }


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $elements = parent::formMultipleElements($items, $form, $form_state);
    return $elements;
  }

}