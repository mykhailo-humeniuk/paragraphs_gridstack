<?php

namespace Drupal\paragraphs_gridstack\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\paragraphs\Plugin\Field\FieldWidget\InlineParagraphsWidget;
use Drupal\paragraphs_gridstack\Services;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Serialization\Json;

/**
 * Plugin implementation of the 'entity reference rendered entity' widget.
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
class ParagraphsGridstackWidget extends InlineParagraphsWidget implements WidgetInterface, ContainerFactoryPluginInterface {

  /**
   * Stores the tempstore factory.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, PrivateTempStoreFactory $temp_store_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->tempStoreFactory = $temp_store_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('tempstore.private')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'always_show_resize_handle' => FALSE,
      'float' => FALSE,
      'cell_height' => 50,
      'height' => 0,
      'vertical_margin' => 0,
      'width' => 12,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['always_show_resize_handle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Always show resize handle'),
      '#default_value' => $this->getSetting('always_show_resize_handle'),
      '#description' => $this->t('If checked the resizing handles are shown even if the user is not hovering over the widget'),
    ];
    $element['float'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Float'),
      '#default_value' => $this->getSetting('float'),
      '#description' => $this->t('Enable floating widgets'),
    ];
    $element['cell_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cell height'),
      '#default_value' => $this->getSetting('cell_height'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('One cell height in pixels'),
    ];
    $element['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $this->getSetting('height'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Maximum rows amount. Default is 0 which means no maximum rows'),
    ];
    $element['vertical_margin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vertical margin'),
      '#default_value' => $this->getSetting('vertical_margin'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Vertical gap size in pixels'),
    ];
    $element['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $this->getSetting('width'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Amount of columns'),
    ];

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

    // Create array with grid settings.
    $grid_settings = [];
    $grid_settings['always_show_resize_handle'] = $this->getSetting('always_show_resize_handle');
    $grid_settings['float'] = $this->getSetting('float');
    $grid_settings['cell_height'] = $this->getSetting('cell_height');
    $grid_settings['height'] = $this->getSetting('height');
    $grid_settings['vertical_margin'] = $this->getSetting('vertical_margin');
    $grid_settings['width'] = $this->getSetting('width');
    $grid_settings['field_id'] = $fid;

    // Use own theme for widget.
    $elements['#theme'] = 'field_gridstack_value_form';
    $elements['#unified_key'] = $fid;
    if (!$node->isNew()) {
      $elements['#nid'] = $node->id();
    }

    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.gridstack';
    $elements['#attached']['library'][] = 'paragraphs_gridstack/paragraphs_gridstack.widget';

    // Add grid comfig form on node add page.
    if (!$node->id() && !$form_state->has('grid_loaded')) {
      // Transform string values to int.
      $settings = array_map(function ($v) {
        return is_bool($v) ? $v : (int) $v;
      }, $this->getSettings());

      $form['grid_settings'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Grid settings'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#suffix' => '</div>',
        '#prefix' => '<div id="grid-settings">',
      ];
      $form['grid_settings']['always_show_resize_handle'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Always show resize handle'),
        '#default_value' => $settings['always_show_resize_handle'],
        '#description' => $this->t('If checked the resizing handles are shown even if the user is not hovering over the widget'),
      ];
      $form['grid_settings']['float'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Float'),
        '#default_value' => $settings['float'],
        '#description' => $this->t('Enable floating widgets'),
      ];
      $form['grid_settings']['cell_height'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Cell height'),
        '#default_value' => $settings['cell_height'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => $this->t('One cell height in pixels'),
      ];
      $form['grid_settings']['height'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Height'),
        '#default_value' => $settings['height'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => $this->t('Maximum rows amount. Default is 0 which means no maximum rows'),
      ];
      $form['grid_settings']['vertical_margin'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Vertical margin'),
        '#default_value' => $settings['vertical_margin'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => $this->t('Vertical gap size in pixels'),
      ];
      $form['grid_settings']['width'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Width'),
        '#default_value' => $settings['width'],
        '#size' => 60,
        '#maxlength' => 128,
        '#description' => $this->t('Amount of columns'),
      ];
      $form['grid_settings']['fid'] = [
        '#type' => 'hidden',
        '#value' => $fid,
      ];
      $form['grid_settings']['actions']['save'] = [
        '#type' => 'button',
        '#value' => $this->t('Save grid'),
        '#name' => 'grid',
        '#validate' => [],
        '#limit_validation_errors' => [],
        '#ajax' => [
          'callback' => [get_class($this), 'itemGridAjax'],
          'wrapper' => 'grid-settings',
          'effect' => 'fade',
          'method' => 'replace',
        ],
      ];
    }

    if (!$form_state->has('grid_loaded')) {
      $storage = $this->tempStoreFactory->get('paragraphs_gridstack');
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
        $data = $node->get('field_paragraphs_gridstack_json')->getValue();
        $data = JSON::decode($data[0]['value']);
        $storage->set('grid_items', $data);
      }
      $form_state->set('grid_loaded', TRUE);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function itemGridAjax(array $form, FormStateInterface $form_state) {
    // Create array with grid settings.
    $values = $form_state->getUserInput();
    $fid = $values['fid'];

    $grid_settings = [];
    $grid_settings['always_show_resize_handle'] = !empty($values['alwaysShowResizeHandle']) ? TRUE : FALSE;
    $grid_settings['float'] = !empty($values['float']) ? TRUE : FALSE;
    $grid_settings['cell_height'] = intval($values['cell_height']);
    $grid_settings['height'] = intval($values['height']);
    $grid_settings['vertical_margin'] = intval($values['vertical_margin']);
    $grid_settings['width'] = intval($values['width']);
    $grid_settings['field_id'] = $fid;

    $element = [];
    $element['#type'] = 'markup';
    $element['#prefix'] = '<div class="ajax-new-content grid-settings-replacement">';
    $element['#suffix'] = '</div>';

    $element['#attached']['drupalSettings']['gridStack']['settings'][$fid] = $grid_settings;
    // We need this param for avoiding issues during validation.
    $form_state->set('settings_added', TRUE);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function itemAjax(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    // We have to remove item from json cached data.
    if ($button['#value'] == t('Confirm removal')) {
      $storage = \Drupal::service('tempstore.private')
        ->get('paragraphs_gridstack');
      $grid_items = $storage->get('grid_items');
      $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -4));
      $uniq_key = $element['#unified_key'];
      $delta = $button['#delta'];

      // Remove item from array and reset keys.
      unset($grid_items['items'][$uniq_key][$delta]);
      $grid_items['items'][$uniq_key] = array_values($grid_items['items'][$uniq_key]);

      $storage->set('grid_items', $grid_items);
    }

    return parent::itemAjax($form, $form_state);
  }

}
