<?php

namespace Drupal\paragraphs_gridstack\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Serialization\Json;

class UpdateGrid extends ControllerBase {


  public function update() {
    if (!empty($_POST['grid_items'])) {
      $data = Json::decode($_POST['grid_items']);
//      \Drupal::logger('paragraphs_gridstack')->notice($_POST['grid_items']);

      $storage = \Drupal::service('tempstore.private')->get('paragraphs_gridstack');
      $storage->set('grid_items', $data);

//      $storage2 = \Drupal::state();
//      $storage2->set('grid_items', $data);
    }
    die();
    return [];
  }
}
