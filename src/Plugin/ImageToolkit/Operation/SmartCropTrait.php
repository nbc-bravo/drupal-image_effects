<?php

namespace Drupal\image_effects\Plugin\ImageToolkit\Operation;

/**
 * Base trait for Smart Crop operations.
 */
trait SmartCropTrait {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'algorithm' => [
        'description' => 'The calculation algorithm for the crop',
        'required' => TRUE,
      ],
      'simulate' => [
        'description' => 'Boolean indicating the crop shall not be executed, but just the crop area highlighted on the source image',
        'required' => FALSE,
        'default' => FALSE,
      ],
      'width' => [
        'description' => 'An integer representing the desired width in pixels',
        'required' => TRUE,
      ],
      'height' => [
        'description' => 'An integer representing the desired height in pixels',
        'required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Assure integers for all arguments.
    foreach (['width', 'height'] as $key) {
      $arguments[$key] = (int) round($arguments[$key]);
    }
    // Fail when width or height are 0 or negative.
    if ($arguments['width'] <= 0) {
      throw new \InvalidArgumentException("Invalid width ('{$arguments['width']}') specified for the image '{$this->getPluginDefinition()['operation']}' operation");
    }
    if ($arguments['height'] <= 0) {
      throw new \InvalidArgumentException("Invalid height ('{$arguments['height']}') specified for the image '{$this->getPluginDefinition()['operation']}' operation");
    }
    return $arguments;
  }

}
