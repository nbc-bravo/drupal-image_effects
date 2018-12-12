<?php

namespace Drupal\image_effects\Plugin\ImageToolkit\Operation\imagemagick;

use Drupal\imagemagick\Plugin\ImageToolkit\Operation\imagemagick\ImagemagickImageToolkitOperationBase;
use Drupal\image_effects\Plugin\ImageToolkit\Operation\ScaleAndSmartCropTrait;

/**
 * Defines Imagemagick Scale and Smart Crop operation.
 *
 * @ImageToolkitOperation(
 *   id = "image_effects_imagemagick_scale_and_smart_crop",
 *   toolkit = "imagemagick",
 *   operation = "scale_and_smart_crop",
 *   label = @Translation("Scale and Smart Crop"),
 *   description = @Translation("Similar to Scale And Crop, but preserves the portion of the image with the most entropy.")
 * )
 */
class ScaleAndSmartCrop extends ImagemagickImageToolkitOperationBase {

  use ScaleAndSmartCropTrait;

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    // @todo not supported in ImageMagick. See if it could be possible.
    return TRUE;
  }

}
