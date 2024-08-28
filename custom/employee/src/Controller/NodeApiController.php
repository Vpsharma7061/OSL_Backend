<?php

namespace Drupal\employee\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class NodeApiController extends ControllerBase {

  /**
   * Returns details of all nodes created through a custom form.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the node details.
   */
  public function getNodes() {
    // Array to store node details.
    $nodes_data = [];

    // Load all nodes. You can add conditions to filter specific content types.
    $nids = \Drupal::entityQuery('node')
    ->condition('type', 'employee_form')
    ->accessCheck(TRUE)
    ->execute();
  
    $nodes = Node::loadMultiple($nids);

    // Iterate over each node to extract details.
    foreach ($nodes as $node) {
      $node_data = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'type' => $node->getType(),
        'created' => date('Y-m-d H:i:s', $node->getCreatedTime()),
      ];
    
      // Check if the field exists before accessing it
      if ($node->hasField('field_emp_name') && !$node->get('field_emp_name')->isEmpty()) {
        $node_data['emp_name'] = $node->get('field_emp_name')->value;
      }
    
      if ($node->hasField('field_emp_age') && !$node->get('field_emp_age')->isEmpty()) {
        $node_data['emp_age'] = $node->get('field_emp_age')->value;
      }
    
      if ($node->hasField('field_emp_email') && !$node->get('field_emp_email')->isEmpty()) {
        $node_data['emp_email'] = $node->get('field_emp_email')->value;
      }
    
      // Add more fields as needed.
    
      $nodes_data[] = $node_data;
    }
    

    // Return the nodes data as a JSON response.
    return new JsonResponse($nodes_data);
  }
}
