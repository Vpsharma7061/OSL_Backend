<?php

namespace Drupal\employee\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class PostDataController extends ControllerBase {

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

    // Retrieve fields from the decoded JSON data.
    $emp_name = isset($data['emp_name']) ? $data['emp_name'] : '';
    $emp_age = isset($data['emp_age']) ? $data['emp_age'] : '';
    $emp_email = isset($data['emp_email']) ? $data['emp_email'] : '';

    // Validate the data.
    if (empty($emp_name) || empty($emp_age) || empty($emp_email)) {
      return new JsonResponse(['status' => 'error', 'message' => 'All fields are required.'], 400);
    }

    // Create a new node of type 'employee_form'.
    $node = Node::create([
      'type' => 'employee_form', // Replace with your content type machine name
      'title' => $emp_name,
      'field_emp_name' => $emp_name, // Adjust field names based on your content type
      'field_emp_age' => $emp_age,
      'field_emp_email' => $emp_email,
    ]);

    // Save the node to the database.
    $node->save();

    // Return a success response.
    return new JsonResponse(['status' => 'success', 'message' => 'Data saved successfully!']);
  }
}
