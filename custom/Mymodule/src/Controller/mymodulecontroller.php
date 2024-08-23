<?php

namespace Drupal\Mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class mymodulecontroller extends ControllerBase {

  /**
   * Returns a custom page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function customPage() {
    return [
      '#markup' => $this->t('Hello, this is a custom page!'),
    ];
  }

}
?>