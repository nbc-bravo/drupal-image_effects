<?php

namespace Drupal\image_effects\Plugin\ImageToolkit\Operation\imagemagick;

use Drupal\imagemagick\Plugin\ImageToolkit\Operation\imagemagick\ImagemagickImageToolkitOperationBase;
use Drupal\image_effects\Plugin\ImageToolkit\Operation\SmartCropTrait;

/**
 * Defines Imagemagick Scale and Smart Crop operation.
 *
 * @ImageToolkitOperation(
 *   id = "image_effects_imagemagick_smart_crop",
 *   toolkit = "imagemagick",
 *   operation = "smart_crop",
 *   label = @Translation("Smart Crop"),
 *   description = @Translation("Similar to Crop, but preserves the portion of the image with the most entropy.")
 * )
 */
class SmartCrop extends ImagemagickImageToolkitOperationBase {

  use SmartCropTrait;

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    // @todo not supported in ImageMagick. See if it could be possible.
    return TRUE;
  }

}
