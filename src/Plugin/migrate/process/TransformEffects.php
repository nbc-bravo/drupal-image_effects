<?php

namespace Drupal\image_effects\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Converts Drupal 7's imagecache_actions effects to 8's image_effects ones.
 *
 * Usage:
 *
 * @endcode
 * process:
 *   bar:
 *     plugin: image_effects_transform_effects
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "image_effects_transform_effects"
 * )
 */
class TransformEffects extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $effect_mappings = [
      'canvasactions_aspect' => 'image_effects_aspect_switcher',
      'canvasactions_canvas2file' => 'image_effects_background',
      'canvasactions_definecanvas' => 'image_effects_set_canvas',
      'canvasactions_resizepercent' => 'image_effects_resize_percentage',
    ];

    if (in_array($value['id'], array_keys($effect_mappings))) {
      // Adjust configuration to match what the D8 plugin expects.
      if ($value['id'] == 'canvasactions_aspect') {
        $value['data'] = [
          'landscape_image_style' => $value['data']['landscape'],
          'portrait_image_style' => $value['data']['portrait'],
          'ratio_adjustment' => $value['data']['ratio_adjustment'],
        ];
      }
      elseif ($value['id'] == 'canvasactions_canvas2file') {
        $value['data'] = [
          'x_offset' => $value['data']['xpos'],
          'y_offset' => $value['data']['ypos'],
          'opacity' => $value['data']['alpha'],
          'background_image' => $value['data']['path'],
        ];
      }
      elseif ($value['id'] == 'canvasactions_definecanvas') {
        $canvas_size = (!empty($value['data']['exact']['width']) && !empty($value['data']['exact']['height'])) ? 'exact' : 'relative';
        $placement = '';
        if ($canvas_size == 'exact') {
          $placement = $value['data']['exact']['xpos'] . '-' . $value['data']['exact']['ypos'];
        }
        $value['data'] = [
          'canvas_size' => $canvas_size,
          'canvas_color' => NULL,
          'exact' => [
            'width' => !empty($value['data']['exact']['width']) ? $value['data']['exact']['width'] : '',
            'height' => !empty($value['data']['exact']['height']) ? $value['data']['exact']['height'] : '',
            'placement' => $placement,
            'x_offset' => 0,
            'y_offset' => 0,
          ],
          'relative' => [
            'left' => !empty($value['data']['relative']['leftdiff']) ? $value['data']['relative']['leftdiff'] : 0,
            'right' => !empty($value['data']['relative']['rightdiff']) ? $value['data']['relative']['rightdiff'] : 0,
            'top' => !empty($value['data']['relative']['topdiff']) ? $value['data']['relative']['topdiff'] : 0,
            'bottom' => !empty($value['data']['relative']['bottomdiff']) ? $value['data']['relative']['bottomdiff'] : 0,
          ],
        ];
      }
      elseif ($value['id'] == 'canvasactions_resizepercent') {
        $value['data'] = [
          'width' => $value['data']['width'] . '%',
          'height' => $value['data']['height'] . '%',
        ];
      }

      // Replace the plugin id with the matching one in Drupal 8.
      $value['id'] = $effect_mappings[$value['id']];
    }

    return $value;
  }

}
