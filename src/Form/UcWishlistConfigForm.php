<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class UcWishlistConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['uc_wishlist.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uc_wishlist_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('uc_wishlist.settings');

    $form['default_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default wish list title'),
      '#description' => $this->t('The default name of a new wish list. The token %user will be replaced by the user\'s name.'),
      '#default_value' => $config->get('default_title'),
    ];
    $form['save_address'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Permit a saved shipping address in a wishlist.'),
      '#default_value' => $config->get('save_address'),
      '#description' => $this->t('Check this box to permit users to specify a default delivery address when creating a wish list. If not checked, purchasers of wish list items must enter a delivery address at checkout.'),
    ];
    $form['default_private'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make users wishlist private by default.'),
      '#default_value' => $config->get('default_private'),
      '#description' => $this->t('This makes the users wish list private by default and exclude it from wish list search results.This will not give the option to users to set the privacy of their wishlists.'),
    ];
    $form['allow_private'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to make their wishlist private.'),
      '#default_value' => $config->get('allow_private'),
      '#description' => $this->t('Check this box to allow users to make their wish list private and exclude it from wish list search results.This option will disable when <em>Make users wishlist private by default.</em> is enabled.'),
    ];
    $form['show_all'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show all wish lists by default.'),
      '#default_value' => $config->get('show_all'),
      '#description' => $this->t('If no keywords are entered in the wish list search form, display all wish lists. Else keywords must be entered, and there is no way to view all created wish lists.'),
    ];
    $form['out_of_stock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to add out of stock item into wishlist.'),
      '#default_value' => $config->get('out_of_stock'),
      '#description' => $this->t('Check this box to allow user to add product to their wishlist if product is out of stock.'),
    ];
    $form['default_from'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Specify a default <em>From</em> address'),
      '#default_value' => $config->get('default_from'),
      '#description' => $this->t("If this field is set then all outgoing emails will have the From address set to the given value (normally something like no-reply@example.com). The <em>Reply To</em> address will be set to the users specified email address. This is recommended if you find your outgoing emails are being flagged as spam due to the sender address domain differing from the domain of the outgoing SMTP server."),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('uc_wishlist.settings')
      ->set('default_title', $values['default_title'])
      ->set('save_address', $values['save_address'])
      ->set('default_private', $values['default_private'])
      ->set('allow_private', $values['allow_private'])
      ->set('show_all', $values['show_all'])
      ->set('out_of_stock', $values['out_of_stock'])
      ->set('default_from', $values['default_from'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
