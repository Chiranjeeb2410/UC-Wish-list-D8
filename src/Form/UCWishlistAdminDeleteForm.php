<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\uc_wishlist\Database\UcWishlistManager;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates the UCWishlistAdminDeleteForm class.
 *
 * Allows the admin to delete/alter a pre-defined wish
 * list.
 */
class UCWishlistAdminDeleteForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  protected $wishlistManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(UcWishlistManager $wishlist_manager) {
    $this->wishlistManager = $wishlist_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('uc_wishlist.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected $wishlistId;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Confirm Wish List Deletion');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return 'Are you sure you want to delete this users wish list? This action cannot be undone.';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete Wish List');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('uc_wishlist.wishlist');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uc_wishlist_confirm_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $wishlist = NULL) {
    $wishlist = uc_wishlist_load($wishlist);
    $form['wishlist'] = [
      '#type' => 'value',
      '#value' => $wishlist,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    drupal_set_message($this->t('@title has been deleted.',
      [
        '@title' => str_replace('.',
          '', $values['wishlist']->title),
      ]));
    $form_state->setRedirect('uc_wishlist.admin_wishlist');
  }

}
