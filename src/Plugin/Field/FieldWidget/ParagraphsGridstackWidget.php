<?php

namespace Drupal\paragraphs_gridstack\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Plugin\Field\FieldWidget\InlineParagraphsWidget;
use Drupal\paragraphs_gridstack\Services;
use Drupal\Component\Utility;

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
class ParagraphsGridstackWidget extends InlineParagraphsWidget implements WidgetInterface {

//  /**
//   * The database connection.
//   *
//   * @var \Drupal\Core\Database\Connection
//   */
//  protected $databaseApi;
//
//  /**
//   * Constructs a new CustomService object.
//   *
//   * @param \Drupal\Core\Database\Connection $connection
//   *   A Database connection to use for reading and writing configuration data.
//   *
//   */
//  public function __construct(Connection $connection) {
//    $this->databaseApi = $connection;
//  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'always_show_resize_handle' => FALSE,
      'float' => FALSE,
      'cell_height' => 60,
      'height' => 0,
      'vertical_margin' => 20,
      'width' => 12,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['always_show_resize_handle'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Always show resize handle'),
      '#default_value' => $this->getSetting('always_show_resize_handle'),
      '#description' => $this->t('If checked the resizing handles are shown even if the user is not hovering over the widget '),
    );
    $element['float'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Float'),
      '#default_value' => $this->getSetting('float'),
      '#description' => $this->t('Enable floating widgets'),
    );
    $element['cell_height'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Cell height'),
      '#default_value' => $this->getSetting('cell_height'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('One cell height in pixels'),
    );
    $element['height'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $this->getSetting('height'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Maximum rows amount. Default is 0 which means no maximum rows'),
    );
    $element['vertical_margin'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Vertical margin'),
      '#default_value' => $this->getSetting('vertical_margin'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Vertical gap size in pixels'),
    );
    $element['width'] = array(
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

    $databaseApi = \Drupal::service('paragraphs_gridstack.databaseApi');

    $buildInfo = $form_state->getBuildInfo();
    $node = $buildInfo['callback_object']->getEntity();
    $fid = $this->fieldDefinition->getUniqueIdentifier();
//    dsm($node->id(), 'id');
//    dsm($node->isNew(), 'isNew');

//    if (!$form_state->hasValue('form_key')) {
//      $random = new Random();
//      $string = $random->name();
//      $form_state->setValue('form_key', $string);
//    }


    // Create array with grid settings.
    $grid_settings = [];
    $grid_settings['always_show_resize_handle'] = $this->getSetting('always_show_resize_handle');
    $grid_settings['float'] = $this->getSetting('float');
    $grid_settings['cell_height'] = $this->getSetting('cell_height');
    $grid_settings['height'] = $this->getSetting('height');
    $grid_settings['vertical_margin'] = $this->getSetting('vertical_margin');
    $grid_settings['width'] = $this->getSetting('width');


//    $grid_settings = $this->getSettings();
//    $paragraph_settings = [
//      'title',
//      'title_plural',
//      'edit_mode',
//      'add_mode',
//      'form_display_mode',
//      'default_paragraph_type'
//    ];
//    foreach ($grid_settings as $key => $value) {
//      if (in_array($key, $paragraph_settings)) {
//
//      }
//    }

    // Use own theme for widget.
    $elements['#theme'] = 'field_gridstack_value_form';
    $elements['#unified_key'] = $fid;

//    dsm($this->fieldDefinition->getUniqueIdentifier(), 'FID');
//    dsm($this->fieldDefinition->getName(), 'FIELD NAME');

    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.gridstack';
    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.widget';

    $elements['#attached']['drupalSettings']['gridStack']['settings'][$fid] = $grid_settings;

//    $elements['json_field'] = [
//      '#type' => 'textfield',
//      '#title' => t('JSON'),
//      '#default_value' => '',
//      '#size' => 60,
//      '#maxlength' => 128,
//      '#required' => FALSE,
//    ];

    return $elements;
  }
}
