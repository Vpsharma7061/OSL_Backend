<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Controller for handling employee-related functionality.
 */
class EmployeeController extends ControllerBase {

  /**
   * Renders the employee form.
   *
   * @return array
   *   A render array containing the employee form.
   */
  public function createEmployee() {
    // Fetch the employee form.
    $form = \Drupal::formBuilder()->getForm('Drupal\employee\Form\EmployeeForm');

    return [
      '#theme' => 'employee',
      '#items' => $form,
    ];
  }

  /**
   * Displays the list of employees with pagination and edit/delete links.
   *
   * @return array
   *   A render array containing the employee data.
   */
  public function displayEmployees() {
    $limit = 3;
    $query = \Drupal::database();
    $result = $query->select('employees', 'e')
      ->fields('e', ['id', 'emp_name', 'emp_age', 'emp_email'])
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit)
      ->execute()
      ->fetchAll(\PDO::FETCH_OBJ);

    $data = [];
    $count = 0;
    $params = \Drupal::request()->query->all();

    if (empty($params) ||  $params['page'] == 0) {
      $count = 1;
    }
    elseif ($params['page'] == 1) {
      $count = $params['page'] + $limit;
    }
    else {
      $count = $params['page'] * $limit;
      $count++;
    }

    foreach ($result as $row) {
      $edit_url = Url::fromRoute('employee.edit', ['id' => $row->id]);
      $delete_url = Url::fromRoute('employee.delete', ['id' => $row->id]);

      $data[] = [
        'id'        => $count . ".",
        'emp_name'  => $row->emp_name,
        'emp_age'   => $row->emp_age,
        'emp_email' => $row->emp_email,
        'Edit'      => Link::fromTextAndUrl($this->t('Edit'), $edit_url)->toString(),
        'Delete'    => Link::fromTextAndUrl($this->t('Delete'), $delete_url)->toString(),
      ];
      $count++;
    }

    $header = ['Id', 'Name', 'Age', 'Email', 'Edit', 'Delete'];
    $build['table'] = [
      '#type'   => 'table',
      '#header' => $header,
      '#rows'   => $data,
      '#attributes' => ['class' => ['employee-table']],
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }

  /**
   * Handles the editing of an employee.
   *
   * @param int $id
   *   The ID of the employee to edit.
   *
   * @return array
   *   A render array containing the employee form with existing data.
   */
  public function editEmployee($id) {
    $form = \Drupal::formBuilder()->getForm('Drupal\employee\Form\EmployeeForm', $id);
    return [
      '#theme' => 'employee',
      '#items' => $form,
    ];
  }

  /**
   * Handles the deletion of an employee.
   *
   * @param int $id
   *   The ID of the employee to delete.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirects to the employee list page after deletion.
   */
  public function deleteEmployee($id) {
    $query = \Drupal::database();
    $query->delete('employees')
      ->condition('id', $id)
      ->execute();

    \Drupal::messenger()->addMessage(t('Employee has been deleted successfully.'));
    // Replace 'employee.list' with the correct route name for your list page.
    return $this->redirect('employee.details');
  }

}
