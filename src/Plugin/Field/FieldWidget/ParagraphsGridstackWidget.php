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

    // Create array with grid settings.
//    $grid_settings = $this->getSettings();
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
      $settings = array_map(function($v) { return is_bool($v) ? $v : (int) $v; }, $this->getSettings());

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

    if (!$form_state->has('grid_loaded')) {
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
        $data = $node->get('field_paragraphs_gridstack_json')->getValue();
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
//    $grid_settings = $form_state->getValues();
//    $fid =  $grid_settings['fid'];
    $grid_settings = [];
    $grid_settings['always_show_resize_handle'] = !empty($values['alwaysShowResizeHandle']) ? true : false;
    $grid_settings['float'] = !empty($values['float']) ? true : false;
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
    if ($button['#value'] == 'Confirm removal') {
      $storage = \Drupal::service('tempstore.private')->get('paragraphs_gridstack');
      $grid_items = $storage->get('grid_items');
      $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -4));
      $uniq_key = $element['#unified_key'];
      $delta = $button['#delta'];

      // Remove item from array and reset keys.
      unset($grid_items['items'][$uniq_key][$delta]);
      $grid_items['items'][$uniq_key] = array_values($grid_items['items'][$uniq_key]);

      $storage->set('grid_items', $grid_items);
    }

    $element = parent::itemAjax($form, $form_state);
    return $element;
  }
}

