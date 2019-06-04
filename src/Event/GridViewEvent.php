<?php

namespace Drupal\paragraphs_gridstack\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GridViewEvent
 *
 * Grid view event.
 *
 * @package Drupal\paragraphs_gridstack\Event
 */
final class GridViewEvent extends Event {

  const GRID_VIEW = 'paragraphs_gridstack.grid_view';

  private static $elements;

  /**
   * GridViewEvent constructor.
   *
   * @param array $elements
   *   Event element.
   */
  public function __construct(array &$elements) {
    static::$elements = &$elements;
  }

  /**
   * Get the event element.
   *
   * @return array
   *   Event element.
   */
  public function getElement() {
    return static::$elements;
  }

  /**
   * Set the element.
   *
   * @param array $elements
   *   Element of event.
   */
  public function setElement(array $elements) {
    static::$elements = $elements;
  }

}
