<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\employee\Service\EmployeeService;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class PostDataController extends ControllerBase {

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
   * Handles the POST request, saves form data, and creates a node.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object containing form data.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response indicating success or failure.
   */
  public function saveData(Request $request) {
    // Extract data from the JSON request body.
    $data = json_decode($request->getContent(), TRUE);

    // Validate the data using the service.
    if (!$this->employeeService->validateEmployeeData($data)) {
      return new JsonResponse(['status' => 'error', 'message' => 'Invalid data provided.'], 400);
    }

    try {
      // Create the node after validation.
      $node = Node::create([
        'type' => 'employee_form',
        'title' => $data['emp_name'],
      // Assuming field name is 'field_emp_name'.
        'field_emp_name' => $data['emp_name'],
      // Assuming field name is 'field_emp_age'.
        'field_emp_age' => $data['emp_age'],
      // Assuming field name is 'field_emp_email'.
        'field_emp_email' => $data['emp_email'],
        'uid' => 1,
      ]);

      $node->save();

      return new JsonResponse(['status' => 'success', 'message' => 'Congratulations!! Data POST successfully!'], 201);
    }
    catch (\Exception $e) {
      $this->logger()->error('Failed to save node: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse(['status' => 'error', 'message' => 'Failed to save data.'], 500);
    }
  }

}
