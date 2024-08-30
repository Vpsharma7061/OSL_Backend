<?php

namespace Drupal\employee\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 */
class EmployeeService {

  protected $entityTypeManager;
  protected $logger;

  /**
   * Constructs a new EmployeeService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
  }

  /**
   * Get all nodes of a specific type.
   *
   * @param string $content_type
   *   The content type to query.
   *
   * @return \Drupal\node\Entity\Node[]
   *   An array of node entities.
   */
  public function getNodesByType($content_type) {
    try {
      $nids = $this->entityTypeManager->getStorage('node')->getQuery()
        ->condition('type', $content_type)
        ->accessCheck(TRUE)
        ->execute();

      return $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    }
    catch (\Exception $e) {
      $this->logger->error('Error retrieving nodes of type @type: @message', [
        '@type' => $content_type,
        '@message' => $e->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Validate the employee data.
   *
   * @param array $data
   *   The employee data to validate.
   *
   * @return bool
   *   TRUE if the data is valid, FALSE otherwise.
   */
  public function validateEmployeeData(array $data) {
    if (empty($data['emp_name']) || empty($data['emp_age']) || empty($data['emp_email'])) {
      return FALSE;
    }
    if (!filter_var($data['emp_email'], FILTER_VALIDATE_EMAIL)) {
      return FALSE;
    }
    if (!is_numeric($data['emp_age'])) {
      return FALSE;
    }
    return TRUE;
  }

}
