<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\employee\Service\EmployeeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 */
class NodeApiController extends ControllerBase {

  protected $employeeService;

  public function __construct(EmployeeService $employee_service) {
    $this->employeeService = $employee_service;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('employee.custom_service')
    );
  }

  /**
   * Returns details of all nodes created through a custom form.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the node details.
   */
  public function getNodes() {
    $nodes_data = [];

    // Use the service to get nodes of type 'employee_form'.
    $nodes = $this->employeeService->getNodesByType('employee_form');

    foreach ($nodes as $node) {
      $node_data = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'type' => $node->getType(),
        'created' => date('Y-m-d H:i:s', $node->getCreatedTime()),
      ];

      // Extract additional fields.
      if ($node->hasField('field_emp_name') && !$node->get('field_emp_name')->isEmpty()) {
        $node_data['emp_name'] = $node->get('field_emp_name')->value;
      }

      if ($node->hasField('field_emp_age') && !$node->get('field_emp_age')->isEmpty()) {
        $node_data['emp_age'] = $node->get('field_emp_age')->value;
      }

      if ($node->hasField('field_emp_email') && !$node->get('field_emp_email')->isEmpty()) {
        $node_data['emp_email'] = $node->get('field_emp_email')->value;
      }

      $nodes_data[] = $node_data;
    }

    return new JsonResponse($nodes_data);
  }

}
