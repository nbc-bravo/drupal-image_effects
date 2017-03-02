<?php

namespace Drupal\image_effects\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines GD AutoOrient operation.
 *
 * @ImageToolkitOperation(
 *   id = "image_effects_gd_auto_orient",
 *   toolkit = "gd",
 *   operation = "auto_orient",
 *   label = @Translation("Auto orient image"),
 *   description = @Translation("Automatically adjusts the orientation of an image.")
 * )
 */
class AutoOrient extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    // This operation does not use any parameters.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments) {
    // If image has been created in memory, this will not apply.
    if (!$source_path = $this->getToolkit()->getSource()) {
      return TRUE;
    }

    // Will not work without EXIF extension installed.
    if (!function_exists('exif_read_data')) {
      $this->logger->notice('The image %file could not be auto-rotated because the exif_read_data() function is not available in this PHP installation. Check if the PHP EXIF extension is enabled.', array('%file' => $this->getToolkit()->getSource()));
      return FALSE;
    }

    // Read EXIF data.
    $exif = @exif_read_data(\Drupal::service('file_system')->realpath($source_path));
    if (isset($exif['Orientation'])) {
      // http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/EXIF.html:
      // 1 = Horizontal (normal)                 [top-left].
      // 2 = Mirror horizontal                   [top-right].
      // 3 = Rotate 180                          [bottom-right].
      // 4 = Mirror vertical                     [bottom-left].
      // 5 = Mirror horizontal and rotate 270 CW [left-top].
      // 6 = Rotate 90 CW                        [right-top].
      // 7 = Mirror horizontal and rotate 90 CW  [right-bottom].
      // 8 = Rotate 270 CW                       [left-bottom].
      switch ($exif['Orientation']) {
        case 2:
          return $this->getToolkit()->apply('mirror', ['x_axis' => TRUE]);

        case 3:
          return $this->getToolkit()->apply('rotate', ['degrees' => 180]);

        case 4:
          return $this->getToolkit()->apply('mirror', ['y_axis' => TRUE]);

        case 5:
          $tmp = $this->getToolkit()->apply('mirror', ['x_axis' => TRUE]);
          if ($tmp) {
            $tmp = $this->getToolkit()->apply('rotate', ['degrees' => 270]);
          }
          return $tmp;

        case 6:
          return $this->getToolkit()->apply('rotate', ['degrees' => 90]);

        case 7:
          $tmp = $this->getToolkit()->apply('mirror', ['x_axis' => TRUE]);
          if ($tmp) {
            $tmp = $this->getToolkit()->apply('rotate', ['degrees' => 90]);
          }
          return $tmp;

        case 8:
          return $this->getToolkit()->apply('rotate', ['degrees' => 270]);

        default:
          return TRUE;
      }
    }
  }

}
