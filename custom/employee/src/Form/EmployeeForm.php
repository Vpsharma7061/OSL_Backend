<?php

namespace Drupal\employee\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class EmployeeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'employee_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    // Check if we are editing an existing employee
    $employee = NULL;
    if ($id) {
      $query = \Drupal::database();
      $employee = $query->select('employees', 'e')
        ->fields('e', ['emp_name', 'emp_age', 'emp_email'])
        ->condition('id', $id)
        ->execute()
        ->fetchAssoc();
    }

    $form['emp_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Employee Name'),
      '#default_value' => $employee ? $employee['emp_name'] : '',
      '#required' => TRUE,
    ];

    $form['emp_age'] = [
      '#type' => 'number',
      '#title' => $this->t('Employee Age'),
      '#default_value' => $employee ? $employee['emp_age'] : '',
      '#required' => TRUE,
    ];

    $form['emp_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Employee Email'),
      '#default_value' => $employee ? $employee['emp_email'] : '',
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $id ? $this->t('Update') : $this->t('Submit'),
    ];

    // Store the employee ID if we're editing
    if ($id) {
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!ctype_alpha(str_replace(' ', '', $form_state->getValue('emp_name')))) {
      $form_state->setErrorByName('emp_name', $this->t('The name should contain only letters and spaces.'));
    }

    $age = $form_state->getValue('emp_age');
    if ($age < 18 || $age > 65) {
      $form_state->setErrorByName('emp_age', $this->t('Age must be between 18 and 65.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();

    // If we're editing, update the record
    if ($form_state->getValue('id')) {
      $query->update('employees')
        ->fields([
          'emp_name' => $form_state->getValue('emp_name'),
          'emp_age' => $form_state->getValue('emp_age'),
          'emp_email' => $form_state->getValue('emp_email'),
        ])
        ->condition('id', $form_state->getValue('id'))
        ->execute();
      \Drupal::messenger()->addMessage($this->t('Employee details updated successfully.'));
    } else {
      // If it's a new entry, insert the record
      $query->insert('employees')
        ->fields([
          'emp_name' => $form_state->getValue('emp_name'),
          'emp_age' => $form_state->getValue('emp_age'),
          'emp_email' => $form_state->getValue('emp_email'),
        ])
        ->execute();
      \Drupal::messenger()->addMessage($this->t('Employee details saved successfully.'));
    }
  }
}
