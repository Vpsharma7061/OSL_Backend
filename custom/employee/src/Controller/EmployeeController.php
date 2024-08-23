<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for rendering the employee form.
 */
class EmployeeController extends ControllerBase {

  /**
   * Renders the employee form.
   *
   * @return array
   *   A render array containing the employee form.
   */
  public function createEmployee() {
    $form = \Drupal::formBuilder()->getForm('Drupal\employee\Form\EmployeeForm');
    $rendered_form = \Drupal::service('renderer')->render($form);

    return [
      '#type' => 'markup',
      '#markup' => $rendered_form,
    ];
  }
}
