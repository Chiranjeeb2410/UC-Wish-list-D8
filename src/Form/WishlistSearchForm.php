<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\uc_wishlist\Database\DBQuery;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Utility\Xss;
use Drupal\User\Entity;

class WishlistSearchForm extends FormBase {

  //protected $database;

  /**
   *public function __construct() {
      $this->db = new Database\DBQuery(\Drupal::database());
    }
   *
   */

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

    $account = \Drupal::currentUser();
    //$user = User::load($account->id());
    $form = [];

    /**
     * if (!$account->id() && !$account->hasPermission('create wish lists')) {
         $path = 'user';
         $query = ['destination' => 'wishlist'];
        }
    $form['wishlist_link'] = [
      '#type' => 'item',
      '#markup' => '<div>' . l(t('Create or manage your wish list.'), $path, array('query' => $query)) . '</div>',
    ];
    */

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

    //$result = $this->db->searchUserWishlist($keywords);

    foreach ($result as $wishlist) {
      $links[] = [
        'title' => Xss::filter($wishlist->title, []),
        'href' => 'wishlist/' . $wishlist->wid,
      ];
    }

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
