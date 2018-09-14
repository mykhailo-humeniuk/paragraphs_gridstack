<?php

namespace Drupal\paragraphs_gridstack\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
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
    $grid_settings['field_id'] = $fid;


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
    if (!$node->isNew()) {
      $elements['#nid'] = $node->id();
    }


//    dsm($this->fieldDefinition->getUniqueIdentifier(), 'FID');
//    dsm($this->fieldDefinition->getName(), 'FIELD NAME');

    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.gridstack';
    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.widget';












    // Add grid comfig form on node add page.
    if (!$node->id()) {
      $settings = $this->getSettings();

      // Transform string values to int.
      $settings['cell_height'] = intval($settings['cell_height']);
      $settings['height'] = intval($settings['height']);
      $settings['vertical_margin'] = intval($settings['vertical_margin']);
      $settings['width'] = intval($settings['width']);

      $form['grid_settings'] = array(
        '#type' => 'fieldset',
        '#title' => t('Grid settings'),
//        '#weight' => 0,
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#suffix' => '</div>',
        '#prefix' => '<div id="grid-settings">'
      );
      $form['grid_settings']['always_show_resize_handle'] = array(
        '#type' => 'checkbox',
        '#title' => t('Always show resize handle'),
        '#default_value' => $settings['always_show_resize_handle'],
        '#description' => t('If checked the resizing handles are shown even if the user is not hovering over the widget '),
      );
      $form['grid_settings']['float'] = array(
        '#type' => 'checkbox',
        '#title' => t('Float'),
        '#default_value' => $settings['float'],
        '#description' => t('Enable floating widgets'),
      );
      $form['grid_settings']['cell_height'] = array(
        '#type' => 'textfield',
        '#title' => t('Cell height'),
        '#default_value' => $settings['cell_height'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => t('One cell height in pixels'),
      );
      $form['grid_settings']['height'] = array(
        '#type' => 'textfield',
        '#title' => t('Height'),
        '#default_value' => $settings['height'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => t('Maximum rows amount. Default is 0 which means no maximum rows'),
      );
      $form['grid_settings']['vertical_margin'] = array(
        '#type' => 'textfield',
        '#title' => t('Vertical margin'),
        '#default_value' => $settings['vertical_margin'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => t('Vertical gap size in pixels'),
      );
      $form['grid_settings']['width'] = array(
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#default_value' => $settings['width'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => t('Amount of columns'),
      );
      $form['grid_settings']['fid'] = array(
        '#type' => 'hidden',
        '#value' => $fid,
      );
      $form['grid_settings']['actions']['save'] = array(
        '#type' => 'button',
        '#value' => t('Save grid'),
        '#name' => 'grid',
        '#validate' => array(),
        '#limit_validation_errors' => array(),
        '#ajax' => array(
          'callback' => array(get_class($this), 'itemGridAjax'),
          'wrapper' => 'grid-settings',
          'effect' => 'fade',
          'method' => 'replace',
        ),
      );
    }


















    if (!$form_state->has('grid_loaded') && !$form_state->get('grid_loaded')) {
      $storage = \Drupal::service('tempstore.private')->get('paragraphs_gridstack');
      // Clear cache.
      if ($storage->get('grid_items')) {
        $storage->delete('grid_items');
      }
      // Pass general settings only once on first page add load.
      if (!$node->id()) {
        $elements['#attached']['drupalSettings']['gridStack']['settings'][$fid] = $grid_settings;
      }
      // On edit page fill up cache from JSON field.
      if ($node->id()) {
        $data = $node->get('field_json')->getValue();
        $data = json_decode($data[0]['value'], true);
        $storage->set('grid_items', $data);
      }

      $form_state->set('grid_loaded', TRUE);
    }

    return $elements;
  }


  public static function itemGridAjax(array $form, FormStateInterface $form_state) {
    // Create array with grid settings.
    $values = $form_state->getValues();
    $fid = $values['fid'];
    $grid_settings = [];
    $grid_settings['always_show_resize_handle'] = $values['alwaysShowResizeHandle'] ? true : false;
    $grid_settings['float'] = $values['float'] ? true : false;
    $grid_settings['cell_height'] = intval($values['cell_height']);
    $grid_settings['height'] = intval($values['height']);
    $grid_settings['vertical_margin'] = intval($values['vertical_margin']);
    $grid_settings['width'] = intval($values['width']);
    $grid_settings['field_id'] = $fid;

    $element = [];
    $element['#type'] = 'markup';
    $element['#prefix'] = '<div class="ajax-new-content">';
    $element['#suffix'] = '</div>';


    $element['#attached']['drupalSettings']['gridStack']['settings'][$fid] = $grid_settings;

    return $element;
  }
}
