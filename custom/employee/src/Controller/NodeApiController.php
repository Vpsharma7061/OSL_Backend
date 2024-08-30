<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\employee\Service\EmployeeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for handling API requests related to nodes.
 */
class NodeApiController extends ControllerBase {

  protected $employeeService;

  public function __construct(EmployeeService $employee_service) {
    $this->employeeService = $employee_service;
  }

  /**
   * Creates an instance of this controller.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   *
   * @return static
   *   The controller instance.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('employee.custom_service')
    );
  }

  /**
   * Returns details of nodes including nid and emp_name.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing node IDs and employee names.
   */
  public function getNodes() {
    $nodes_data = [];

    // Use the service to get nodes of type 'employee_form'.
    $nodes = $this->employeeService->getNodesByType('employee_form');

    foreach ($nodes as $node) {
      $nid = $node->id(); // Node ID (nid)
      $emp_name = $node->hasField('field_emp_name') ? $node->get('field_emp_name')->value : NULL;

      $nodes_data[] = [
        'nid' => $nid,       // Node ID
            // Alias for Node ID (optional, same as 'nid')
        'emp_name' => $emp_name, // Employee name
      ];
    }

    return new JsonResponse($nodes_data);
  }

}
