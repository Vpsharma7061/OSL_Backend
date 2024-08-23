<?php

namespace Drupal\employee\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

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
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['emp_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Employee Name'),
      '#required' => TRUE,
    ];

    $form['emp_age'] = [
      '#type' => 'number',
      '#title' => $this->t('Employee Age'),
      '#required' => TRUE,
    ];

    $form['emp_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Employee Email'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Create a new node of type 'employee_form'.
    $node = Node::create([
      'type' => 'employee_form',
      'title' => $form_state->getValue('emp_name'),
      'field_emp_name' => $form_state->getValue('emp_name'),
      'field_emp_age' => $form_state->getValue('emp_age'),
      'field_emp_email' => $form_state->getValue('emp_email'),
    ]);
    $node->save();

    \Drupal::messenger()->addMessage($this->t('Employee @name has been saved.', ['@name' => $form_state->getValue('emp_name')]));
  }
}
