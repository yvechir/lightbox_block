<?php

namespace Drupal\lightbox_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Defines a form that configures forms module settings.
 *
 * @package Drupal\lightbox_block\Form
 */
class ConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lightbox_block_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'lightbox_block.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('lightbox_block.settings');
    $form['lightbox_image'] = [
      '#title' => $this->t('Upload image'),
      '#type' => 'managed_file',
      '#default_value' => $config->get('lightbox_image'),
      '#upload_location' => 'public://',
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
    ];
    $form['lightbox_image_alt_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alt text for the image'),
      '#default_value' => $config->get('lightbox_image_alt_text'),
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('title'),
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('description'),
      '#required' => TRUE,
    ];
    $form['cta'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Call-to-Action'),
      '#default_value' => $config->get('cta'),
      '#required' => TRUE,
    ];
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#default_value' => $config->get('url'),
      '#required' => TRUE,
    ];
    $form['active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active?'),
      '#default_value' => $config->get('active'),
    ];
    $form['activedate'] = [
      '#type' => 'date',
      '#title' => $this->t('Active from'),
      '#default_value' => $config->get('activedate'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $image = $form_state->getValue('lightbox_image');
    $config = \Drupal::config('lightbox_block.settings');
    if ($image != $config->get('lightbox_image')) {
      if (!empty($image[0])) {
        $file = File::load($image[0]);
        $file->setPermanent();
        $file->save();
      }
    }
    $this->config('lightbox_block.settings')
      ->set('lightbox_image', $image)
      ->set('title', $values['title'])
      ->set('description', $values['description'])
      ->set('url', $values['url'])
      ->set('cta', $values['cta'])
      ->set('lightbox_image_alt_text', $values['lightbox_image_alt_text'])
      ->set('activedate', $values['activedate'])
      ->set('active', $values['active'])
      ->save();
    parent::submitForm($form, $form_state);

    // @todo Target more specific Caches
    drupal_flush_all_caches();
  }

}
