<?php

/**
 * @file
 * Contains the views integration for wishlists.
 */

/**
 * Implements hook_views_data().
 */
function uc_wishlist_views_data() {

  // Wishlist table.
  $data['uc_wishlists']['table']['group'] = t('Wishlist');

  // We can use this as a base table for Views.
  $data['uc_wishlists']['table']['base'] = [
    'field' => 'wid',
    'title' => t('Wishlist'),
  ];

  // Define the joins.
  $data['uc_wishlists']['table']['join']['users'] = [
    'left_field' => 'uid',
    'field' => 'uid',
    'type' => 'INNER',
  ];

  // Define the fields.
  $data['uc_wishlists']['wid'] = [
    'title' => t('Wishlist id'),
    'help' => t('Unique id for wishlist.'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],

    // Information for accepting a wid as a filter.
    'filter' => [
      'id' => 'numeric',
    ],

    // Information for sorting on a wid.
    'sort' => [
      'id' => 'standard',
    ],

    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlists']['uid'] = [
    'title' => t('user id'),
    'help' => t('user id who created the wishlist.'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],

    'filter' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlists']['title'] = [
    'title' => t('wishlist title'),
    'help' => t('Title for wishlist'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'standard',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['uc_wishlists']['expiration'] = [
    'title' => t('Expiry date'),
    'help' => t('Timestamp for when the wish list expires.'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'date',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'date',
    ],
  ];

  $data['uc_wishlists']['address'] = [
    'title' => t('Shipping Address'),
    'help' => t('Address for shipping items on the wish list.'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'standard',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['uc_wishlists']['private'] = [
    'title' => t('Private'),
    'help' => t('Access level of the wishlist of the users.'),
    'group' => 'Wishlist',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],
    'filter' => [
      'id' => 'equality',
    ],
  ];

  // Wishlist products.
  $data['uc_wishlist_products']['table']['group'] = t('Wishlist products');

  // Define the joins.
  $data['uc_wishlist_products']['table']['join']['node'] = [
    'left_field' => 'nid',
    'field' => 'nid',
  ];

  $data['uc_wishlist_products']['table']['join']['uc_wishlists'] = [
    'left_field' => 'wid',
    'field' => 'wid',
    'type' => 'INNER',
  ];

  // Define the fields.
  $data['uc_wishlist_products']['wpid'] = [
    'title' => t('Wishlist product id'),
    'help' => t('The ID of the wish list product.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],

    // Information for accepting a wid as a filter.
    'filter' => [
      'id' => 'numeric',
    ],

    // Information for sorting on a wid.
    'sort' => [
      'id' => 'standard',
    ],

    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlist_products']['wid'] = [
    'title' => t('Wishlist id'),
    'help' => t('The unique id for the wish list this product is assigned to.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],
    'relationship' => [
      'base' => 'uc_wishlists',
      'base field' => 'wid',
      'id' => 'standard',
      'label' => t('Wishlist'),
    ],
    // Information for accepting a wid as a filter.
    'filter' => [
      'id' => 'numeric',
    ],
    // Information for sorting on a wid.
    'sort' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlist_products']['nid'] = [
    'title' => t('product id'),
    'help' => t('The Product id of the product.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],
    'relationship' => [
      'base' => 'node',
      'base field' => 'nid',
      'id' => 'standard',
      'label' => t('Product'),
    ],
    // Information for accepting a wid as a filter.
    'filter' => [
      'id' => 'numeric',
    ],
    // Information for sorting on a wid.
    'sort' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlist_products']['qty'] = [
    'title' => t('Product quantity'),
    'help' => t('The quantity of this product on the wish list.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'numeric',
      'float' => FALSE,
      'click sortable' => TRUE,
    ],
    // Information for accepting a wid as a filter.
    'filter' => [
      'id' => 'numeric',
    ],
    // Information for sorting on a wid.
    'sort' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['uc_wishlist_products']['data'] = [
    'title' => t('product data'),
    'help' => t('The data array for the product.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'standard',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['uc_wishlist_products']['purchase'] = [
    'title' => t('product purchase data'),
    'help' => t('An array of purchase data for the product.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'views_id_field_serialized',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['uc_wishlist_products']['changed'] = [
    'title' => t('Change time for product'),
    'help' => t('The timestamp of the last change to this wish list product.'),
    'group' => 'Wishlist products',
    'field' => [
      'id' => 'date',
      'click sortable' => TRUE,
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'date',
    ],

  ];

  return $data;
}
