<?php

namespace Drupal\paragraphs_gridstack\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Plugin implementation of the 'entity reference rendered entity' formatter.
 *
 * @FieldFormatter(
 *   id = "paragraphs_gridstack_view",
 *   label = @Translation("Paragraphs gridstack"),
 *   description = @Translation("Display grid."),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
//class ParagraphsGridstackFormatter extends EntityReferenceRevisionsEntityFormatter {
//  /**
//   * {@inheritdoc}
//   */
//  public static function defaultSettings() {
//    return parent::defaultSettings();
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function settingsForm(array $form, FormStateInterface $form_state) {
//    return parent::settingsForm($form, $form_state);
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function settingsSummary() {
//    $summary = parent::settingsSummary();
//    return $summary;
//  }
//
//
//
//
//  /**
//   * {@inheritdoc}
//   */
//  public function view(FieldItemListInterface $items, $langcode = NULL) {
//    kint($items->getSettings());
//    // Default the language to the current content language.
//    if (empty($langcode)) {
//      $langcode = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
//    }
//    $elements = parent::view($items, $langcode);
//
//
//    return $elements;
//  }
//
//
//
//
//
//  /**
//   * {@inheritdoc}
//   */
//  public function viewElements(FieldItemListInterface $items, $langcode) {
//    $elements = parent::viewElements($items, $langcode);
//
////    foreach ($elements as $key => &$item) {
////      $item['#theme'] = 'paragraphs_gridstack';
////    }
//
////    kint($elements);
//    return $elements;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public static function isApplicable(FieldDefinitionInterface $field_definition) {
//    return parent::isApplicable($field_definition);
//  }
//
//
//
//
//
//
//
//
//  /**
//   * {@inheritdoc}
//   */
//  public function prepareView(array $entities_items) {
//    // Entity revision loading currently has no static/persistent cache and no
//    // multiload. As entity reference checks _loaded, while we don't want to
//    // indicate a loaded entity, when there is none, as it could cause errors,
//    // we actually load the entity and set the flag.
//    foreach ($entities_items as $items) {
//      foreach ($items as $item) {
//
////        kint($item);
//
////        if ($item->entity) {
////          $item->_loaded = TRUE;
////        }
//      }
//    }
//  }
//}




class ParagraphsGridstackFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    // Default the language to the current content language.
    if (empty($langcode)) {
      $langcode = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    }
    $elements = parent::view($items, $langcode);

//    kint($elements);


    return $elements;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      if ($entity->id()) {
        $summary = $entity->getSummary();
        $elements[$delta] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['paragraph-formatter', 'buuuuu1']
          ]
        ];
        $elements[$delta]['info'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['paragraph-info', 'buuuuu2']
          ]
        ];
        $elements[$delta]['info'] += $entity->getIcons();
        $elements[$delta]['summary'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['paragraph-summary', 'buuuuu3']
          ]
        ];
        $elements[$delta]['summary']['description'] = [
          '#markup' => $summary,
          '#prefix' => '<div class="paragraphs-collapsed-description BUUUUUU4">',
          '#suffix' => '</div>',
        ];
      }
    }
    $elements['#attached']['library'][] = 'paragraphs/drupal.paragraphs.formatter';
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $target_type = $field_definition->getSetting('target_type');
    $paragraph_type = \Drupal::entityTypeManager()->getDefinition($target_type);
    if ($paragraph_type) {
      return $paragraph_type->isSubclassOf(ParagraphInterface::class);
    }

    return FALSE;
  }
}