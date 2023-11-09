<?php

namespace Drupal\lightbox_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides a 'LightBox' Block.
 *
 * @Block(
 *   id = "lightbox_block",
 *   admin_label = @Translation("Lightbox block"),
 *   category = @Translation("LightBox Block"),
 * )
 */
class LightboxBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = \Drupal::config('lightbox_block.settings');
    $activeDate = strtotime($config->get('activedate'));
    if (time() < $activeDate) {
      return [];
    }
    $img_url = '';
    if ($file_id = $config->get('lightbox_image')[0]) {
      if ($file = File::load($file_id)) {
        $image_uri = $file->get('uri')->value;

        // Using ImageStyle you then load your style.
        $style = ImageStyle::load('news_event_card_images');

        // Use buildUrl to create the path to your styled image.
        if ($image_uri) {
          $destination_uri = $style->buildUri($file->uri->value);
          $style->createDerivative($image_uri, $destination_uri);
          $img_url = $style->buildUrl($image_uri);
        }
      }
    }
    $img_url = str_replace('www.', '', $img_url);
    $output = '<<<TEMPLATE
    <div class="bg" aria-hidden="false" style="display: none;">
      <div class="lightbox-inner">
        <img alt="@alt" src="@img_url" class="lightbox-image">
        <h3 class="title-gotham-bold-1">@title</h3>
        <p class="description-gotham-m">@description</p>
        <a aria-label="@title" class="reverse-button-normal-05-risd" href="@url">@cta</a>
        <a class="dismiss-this" href="#">DISMISS THIS</a>
      </div>
    </div>
TEMPLATE';
    $output = strip_tags($output);
    $placeholders = [
      '@title' => $config->get('title'),
      '@description' => $config->get('description'),
      '@url' => $config->get('url'),
      '@cta' => $config->get('cta'),
      '@alt' => $config->get('lightbox_image_alt_text'),
      '@img_url' => $img_url,
    ];
    return [
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => $this->getCacheContexts(),
      ],
      '#markup' => $this->t($output, $placeholders),
      '#attached' => [
        'library' => [
          'lightbox_block/block',
        ],
      ],
    ];
  }

}
