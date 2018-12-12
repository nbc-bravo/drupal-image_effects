<?php

namespace Drupal\image_effects\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;
use Drupal\image_effects\Component\ImageUtility;

/**
 * Crop an image preserving the portion with the most entropy.
 *
 * @ImageEffect(
 *   id = "image_effects_smart_crop",
 *   label = @Translation("Smart Crop"),
 *   description = @Translation("Similar to Crop, but preserves the portion of the image with the most entropy.")
 * )
 */
class SmartCropImageEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'width' => NULL,
      'height' => NULL,
      'simulate' => FALSE,
      'algorithm' => 'entropy_slice',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return [
      '#theme' => 'image_effects_smart_crop_summary',
      '#data' => $this->configuration,
    ] + parent::getSummary();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['width'] = [
      '#type' => 'image_effects_px_perc',
      '#title' => $this->t('Width'),
      '#default_value' => $this->configuration['width'],
      '#description' => $this->t('Enter a value, and specify if pixels or percent. Leave blank to scale according to new height.'),
      '#size' => 5,
      '#maxlength' => 5,
      '#required' => FALSE,
    ];
    $form['height'] = [
      '#type' => 'image_effects_px_perc',
      '#title' => $this->t('Height'),
      '#default_value' => $this->configuration['height'],
      '#description' => $this->t('Enter a value, and specify if pixels or percent. Leave blank to scale according to new width.'),
      '#size' => 5,
      '#maxlength' => 5,
      '#required' => FALSE,
    ];

    $form['advanced'] = [
      '#type'  => 'details',
      '#title' => $this->t('Advanced settings'),
    ];
    $form['advanced']['algorithm'] = [
      '#type'  => 'select',
      '#title' => $this->t('Calculation algorithm'),
      '#options' => [
        'entropy_slice' => $this->t('Image entropy - slicing'),
      ],
      '#description' => $this->t('Select an algorithm to use to determine the crop area.'),
      '#default_value' => $this->configuration['algorithm'],
    ];
    $form['advanced']['simulate'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['simulate'],
      '#title' => t('Simulate'),
      '#description' => t('If selected, the crop will not be executed; the crop area will be highlighted on the source image instead.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['width'] = $form_state->getValue('width');
    $this->configuration['height'] = $form_state->getValue('height');
    $this->configuration['algorithm'] = $form_state->getValue(['advanced', 'algorithm']);
    $this->configuration['simulate'] = $form_state->getValue(['advanced', 'simulate']);
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
    $width = $form_state->getValue('width');
    $height = $form_state->getValue('height');
    if (((bool) $width) === FALSE && ((bool) $height) === FALSE) {
      $form_state->setError($form, $this->t("Either <em>Width</em> or <em>Height</em> must be specified."));
    }
    if (strpos($width, '%') !== FALSE && ((int) str_replace('%', '', $width)) > 100) {
      $form_state->setErrorByName('width', $this->t("A percentage crop can not be wider than the source image."));
    }
    if (strpos($height, '%') !== FALSE && ((int) str_replace('%', '', $height)) > 100) {
      $form_state->setErrorByName('height', $this->t("A percentage crop can not be higher than the source image."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function transformDimensions(array &$dimensions, $uri) {
    if (!$dimensions['width'] || !$dimensions['height']) {
      return;
    }
    if ($this->configuration['simulate']) {
      return;
    }
    $d = ImageUtility::resizeDimensions($dimensions['width'], $dimensions['height'], $this->configuration['width'], $this->configuration['height']);
    $dimensions['width'] = $d['width'];
    $dimensions['height'] = $d['height'];
  }

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    $dimensions = ImageUtility::resizeDimensions($image->getWidth(), $image->getHeight(), $this->configuration['width'], $this->configuration['height']);
    return $image->apply('smart_crop', [
      'width' => $dimensions['width'],
      'height' => $dimensions['height'],
      'algorithm' => $this->configuration['algorithm'],
      'simulate' => $this->configuration['simulate'],
    ]);
  }

}
