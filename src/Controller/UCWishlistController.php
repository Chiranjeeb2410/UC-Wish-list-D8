<?php

namespace Drupal\uc_wishlist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\uc_wishlist\Database;

class UCWishlistController extends ControllerBase {
  protected $db;

  /**
   *
   */
  public function __construct() {
    $this->db = new Database\DBQuery(\Drupal::database());
  }

  /**
   *
   */
  public function AdminWishlist() {
    $rows = [];

    $header = [
            [
                'data' => t('User'),
                'field' => 'u.name',
                'sort' => 'asc',
            ],
            [
                'data' => t('Title'),
                'field' => 'w.title',
            ],
            [
                'data' => t('Expiration date'),
                'field' => 'w.expiration',
            ],
            ['data' => t('Status')],
    ];

    // Database returns a paged list of wish lists.
    $result = $this->db->getAllWishlist();

    foreach ($result as $wishlist) {

      $deleteUrl = Url::fromRoute('uc_wishlist.admin_delete', ['uc_wishlist' => $wishlist->wid]);
      $link_options = [
        'attributes' => [],
      ];
      $deleteUrl->setOptions($link_options);

      $expired = '';
      if ($wishlist->expiration < REQUEST_TIME) {
        $expired = t('Expired');
      } else {
         $expired = t('Active');
      }
      $deleteUrl = Link::fromTextAndUrl($expired . ' | Delete', $deleteUrl)->toString();
      $account = \Drupal\user\Entity\User::load($wishlist->uid);
      $name = $account->getAccountName();
      $rows[] = [
        $name ? Link::fromTextAndUrl($name, Url::fromRoute('entity.user.canonical', ['user' => $wishlist->uid]))->toString() : t('Anonymous'),
        Link::fromTextAndUrl($wishlist->title, Url::fromRoute('uc_wishlist.wishlist_view', ['wid' => $wishlist->wid]))->toString(),
        \Drupal::service('date.formatter')->format($wishlist->expiration),
        $deleteUrl,
        ];
      }

      if (empty($rows)) {
        $rows[] = [
          [
            'data'    => t('No wish lists found.'),
            'colspan' => 4,
          ],
        ];
      }
      return [
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
      ];
  }

  /**
   *
   */
  public function viewWishlist($wid = NULL) {
    $output = '';
    $title = '';
    $wishlist = NULL;
    $render = [];
    $rendered_wishlistview_form = '';
    $own = FALSE;
    if (!$own && $wid == uc_wishlist_get_wid()) {
      //user is viewing their own wishlist so redirect them to the correct route
      return $this->redirect('uc_wishlist.wishlist');
    }

    // Attempt to load the wish list.
    $wishlist = uc_wishlist_load($wid);

    // Handle a non-existent wish list.
    if (!$wishlist) {
      // Otherwise send them to the search form.
      drupal_set_message($this->t('The wish list you requested could not be found. Perhaps you can try looking for it through the wish list search form below.'));
      return $this->redirect('uc_wishlist.search');
    }
    // Display only if the users wishlist is not set to private
    if (!$wishlist->private) {

      //Set the title to the wishlist title
      $title = $wishlist->title;
      $items = uc_wishlist_get_contents($wid);

      if (empty($items)) {
        //@TODO: add the users name to the output string
        $output = '<p>There are no products in this wish list.</p>';
        //return $render;
      }
      else {
        $form = \Drupal::formBuilder()->getForm('Drupal\uc_wishlist\Form\WishlistViewForm', $items, $wid, false);
        $rendered_wishlistview_form = \Drupal::service('renderer')->render($form);
      }
    }
    else {
      drupal_set_message($this->t('This users wish list is set to private. You may search for another user\'s wish list below.'));
      return $this->redirect('uc_wishlist.search');
    }
    $render['#theme'] = 'uc_wishlist_view_wishlist';
    $render['#type'] = 'theme';
    $render['#title'] = $title;
    $render['#message'] = $output;
    $render['#form'] = $rendered_wishlistview_form;
    $render['#wishlist'] = $wishlist;

    return $render;
  }

  /**
   *
   */
  public function myWishlist() {
    //setup our render array for all our page variables and configurations
    $render = [];
    //get the wishlist id based off of the current users id
    $wid = uc_wishlist_get_wid();
    // Attempt to load the wish list.
    $wishlist = uc_wishlist_load($wid);
    $output = '';
    if (!$wishlist) {
      // Display a message letting them know their list is empty.
      $title = 'My Wish list';
      drupal_set_message($this->t("You have not added any products to your wish list. You can add any product from the store to your wish list by clicking the 'Add to wish list' button on the product's page."));
      $render['#markup'] = 'There are no products on your wish list.';
      $render['#title'] = $title;
      return $render;
    }
    $title = 'My Wish List';
    $items = uc_wishlist_get_contents($wid);

    if (empty($items)) {
      $render['#markup'] = '<p>There are no products in your wish list. You can add products by clicking the Add to wish list button on the product\'s page</p>';
      return $render;
    }

    $form = \Drupal::formBuilder()->getForm('Drupal\uc_wishlist\Form\WishlistViewForm', $items, $wid, true);
    $rendered_wishlistview_form = \Drupal::service('renderer')->render($form);
    $render['#theme'] = 'uc_wishlist_view_wishlist';
    $render['#type'] = 'theme';
    $render['#title'] = $title;
    $render['#message'] = $output;
    $render['#form'] = $rendered_wishlistview_form;
    $render['#wishlist'] = $wishlist;
    return $render;
  }

  /**
   *
   */
  public function userWishlist($user) {
    return array();
  }

  /**
   *
   */
  public function searchWishlist() {
    $render = array();
    $render['#markup'] = 'Search for a wish list.';
    return $render;
  }

}
