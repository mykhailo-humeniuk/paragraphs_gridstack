<?php

namespace Drupal\paragraphs_gridstack\Services;

use Drupal\Core\Database\Connection;

/**
 * Class CustomService.
 */
class DatabaseApi {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new CustomService object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   A Database connection to use for reading and writing configuration data.
   *
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function save($nid, $data) {
    $gridstack = new \stdClass();
    $gridstack->nid = $nid;
    $gridstack->data = $data;
    // Save gridstack data into the database.
    $this->connection->merge('gridstack')
      ->keys(['nid' => $gridstack->nid])
      ->fields(['data' => serialize($gridstack->data)])
      ->execute();
  }
  /**
   * Here you can pass your values as $array.
   */
  public function load($nid, $reset = FALSE) {
    $cache = &drupal_static(__FUNCTION__);
    if (!isset($cache[$nid]) || $reset) {
      $query = $this->connection->select('gridstack', 'gs');
      $query->addField('gs', 'data');
      $query->condition('nid', $nid);
      $cache[$nid] = $query->execute()->fetchField();

      if (!empty($cache[$nid])) {
        // Crop data stores serialized so we have to unserialize it before using.
        $cache[$nid] = unserialize($cache[$nid]);
      }
    }
    return $cache[$nid];
  }

  public function delete($nid) {
    // Load gridstack before deletion to ensure that it exists.
    $gridstack = $this->load($nid);
    if (!empty($gridstack)) {
      // Delete gridstack settings from table by its nid.
      $query = $this->connection->delete('gridstack');
      $query->condition('nid', $nid);
      $query->execute();
    }
  }
}
