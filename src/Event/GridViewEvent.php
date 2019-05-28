<?php


namespace Drupal\paragraphs_gridstack\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Grid view event.
 */
final class GridViewEvent extends Event {

  const GRID_VIEW = 'paragraphs_gridstack.grid_view';


  private static $elements;


  /**
   * .
   *
   * @param $elements
   */
  public function __construct(&$elements) {
    static::$elements = &$elements;
  }

  /**
   * .
   *
   * @return array
   */
    public function getElement() {
      return static::$elements;
    }

    /**
     * @param $elements
     */
    public function setElement($elements) {
       static::$elements = $elements;
    }
}