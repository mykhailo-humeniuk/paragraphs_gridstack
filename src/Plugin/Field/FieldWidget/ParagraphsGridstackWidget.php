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
    return [
      'alwaysShowResizeHandle' => 0,
      'float' => 0,
      'cellHeight' => 60,
      'height' => 0,
      'verticalMargin' => 20,
      'width' => 12,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['grid_settings'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Grid settings'),
      '#weight' => 9999,
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );
    $element['grid_settings']['alwaysShowResizeHandle'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Always show resize handle'),
      '#default_value' => $this->getSetting('alwaysShowResizeHandle'),
      '#description' => $this->t('If checked the resizing handles are shown even if the user is not hovering over the widget '),
    );
    $element['grid_settings']['float'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Float'),
      '#default_value' =>$this->getSetting('float'),
      '#description' => $this->t('Enable floating widgets'),
    );
    $element['grid_settings']['cellHeight'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Cell height'),
      '#default_value' => $this->getSetting('cellHeight'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('One cell height in pixels'),
    );
    $element['grid_settings']['height'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $this->getSetting('height'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Maximum rows amount. Default is 0 which means no maximum rows'),
    );
    $element['grid_settings']['verticalMargin'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Vertical margin'),
      '#default_value' => $this->getSetting('verticalMargin'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Vertical gap size in pixels'),
    );
    $element['grid_settings']['width'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $this->getSetting('width'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Amount of columns'),
    );

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

    $elements['#theme'] = 'field_gridstack_value_form';

    kint($this->getSettings());

    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.gridstack';
    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.widget';

    return $elements;
  }

}
