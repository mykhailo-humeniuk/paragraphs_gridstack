<?php

namespace Drupal\paragraphs_gridstack\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UpdateGrid.
 *
 * Control update grid process.
 *
 * @package Drupal\paragraphs_gridstack\Controller
 */
class UpdateGrid extends ControllerBase {

  /**
   * Stores the tempstore factory.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * UpdateGrid constructor.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore factory.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStoreFactory = $temp_store_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('tempstore.private'));
  }

  /**
   * {@inheritdoc}
   */
  public function update() {
    if (!empty($_POST['grid_items'])) {
      $data = Json::decode($_POST['grid_items']);
      $storage = $this->tempStoreFactory->get('paragraphs_gridstack');
      $storage->set('grid_items', $data);
    }

    die();
    return [];
  }

}
