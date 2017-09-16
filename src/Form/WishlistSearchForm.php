<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates the WishlistSearchForm class.
 *
 * Allows a user to search for any specific wish
 * list.
 */
class WishlistSearchForm extends FormBase {

  /**
   * Defines an object that has a user id, roles and can have session data.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Defines an account interface which represents the current user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Interface implemented both by the global session and the user entity.
   */
  public function __construct(AccountInterface $account) {
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uc_wishlistSearchlist';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = [];

    $form['search'] = [
      '#type' => 'fieldset',
    ];
    $form['search']['keywords'] = [
      '#type' => 'textfield',
      '#title' => t('Search keywords'),
      '#description' => t('Enter the keywords to use to search wish list titles and addresses.'),
      '#default_value' => $keywords,
    ];
    $form['search']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Search'),
    ];

    $links = [];

    if (!empty($links)) {
      $output = [
        'links' => $links,
        'attributes' => [
          'class' => ['wishlist'],
        ],
        'heading' => NULL,
      ];
    }
    else {
      $output = ' ' . t('No wish lists found.');
    }

    $form['output'] = [
      '#type' => 'item',
      '#markup' => Xss::filter('<div><h2>' . t('Wish lists:') . '</h2>' . $output . '</div>'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if (empty($form_state->getValue['keywords'])) {
      $form_state->setRedirect('wishlist/search');
    }
    else {
      $form_state->setRedirect('wishlist/search/') . UrlHelper::encodePath($form_state->getValue['keywords']);
    }

  }

}
