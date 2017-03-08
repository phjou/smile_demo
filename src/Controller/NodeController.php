<?php

namespace Drupal\smile_demo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

/**
 * Returns responses for Node routes.
 */
class NodeController extends ControllerBase {

  /*
   * Generates an overview table of older revisions of a node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function specialAccess(NodeInterface $node) {

    $build = \Drupal::formBuilder()->getForm('Drupal\smile_demo\Form\NodeUserAccessSettingsForm');   

    return $build;
  }

}

