<?php

namespace Drupal\uc_wishlist\Database;

use Drupal\Core\Database\Connection;

/**
 * Defines an UcWishlistManager service.
 */
class UcWishlistManager {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs an UcWishlistManager object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The connection object.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Retrieves a list of all wish lists created by a specific user.
   *
   * @return \Drupal\Core\Database\StatementInterface[]
   *   List of wish lists created by any particular user.
   */
  public function getAllWishlist() {
    $query = $this->connection->select('uc_wishlists', 'w');
    $query->leftJoin('users', 'u', 'w.uid = u.uid');
    $query->fields('w', [
      'wid',
      'uid',
      'title',
      'expiration',
    ]);
    $result = $query->execute();

    // @todo
    // extend default pager limit
    // $query->extend('PagerDefault')->limit(25)->execute();
    return $result;
  }

  /**
   * Function invoked to create a particular user wish list.
   *
   * @param string $fields
   *   Variable that contains all the custom fields for creating a
   *   wish list.
   * @param int $values
   *   Refers to the values of the wish list product parameters for
   *   creating a specific user wish list.
   */
  public function createWishlist($fields, $values) {
    $this->connection->insert('uc_wishlists')->fields($fields, $values)->execute();
  }

  /**
   * Function invoked to create/add related wish list products.
   *
   * @param string $fields
   *   Variable that contains all the custom fields for creating a
   *   wish list.
   * @param int $values
   *   Refers to the values of the wish list product parameters for
   *   creating a specific user wish list.
   */
  public function createWishlistProduct($fields, $values) {
    $this->connection->insert('uc_wishlist_products')->fields($fields, $values)->execute();
  }

  /**
   * Retrieves the id of a particular wish list.
   *
   * @param int $uid
   *   Refers to the id used for retrieving specific wish
   *    lists created by a specific user.
   */
  public function getWishlistIdByUser($uid) {
    $this->connection->query('SELECT wid FROM {uc_wishlists} WHERE uid = :uid;', [':uid' => $uid])->fetchField();
  }

  /**
   * Retrieves a specific wish list product.
   *
   * @param int $wid
   *   Refers to a particular wish list id used to retrieve a
   *   specific wish list item.
   * @param int $nid
   *   Refers to a particular node id used to retrieve a specific
   *   wish list item.
   * @param array $data
   *   Refers to the data associated with a particular wish list
   *   item.
   */
  public function getWishlistItem($wid, $nid, array $data) {
    $this->connection->query("SELECT * FROM {uc_wishlist_products} WHERE wid = :wid AND nid = :nid AND data = :data", [
      ':wid' => $wid,
      ':nid' => $nid,
      ':data' => serialize($data),
    ]);
  }

  /**
   * Displays list of wish lists on executing a keyword-based search.
   *
   * @param string $keywords
   *   Refers to the keywords to make three queries and return
   *   a new DatabaseCondition.
   *
   * @return string
   *   Displays a list of user wish lists.
   */
  public function searchUserWishlist($keywords) {
    if (!empty($keywords)) {
      // Check for user, wish list title, or address matches.
      $query = $this->connection->select('uc_wishlists', 'w');
      $query->join('users', 'u', 'w.uid = u.uid');
      $query->fields('w', [
        'wid',
        'title',
      ]);
      $query->distinct();
      $query->condition(db_or()
        ->condition('u.name', '%' . $keywords . '%', 'LIKE')
        ->condition('w.title', '%' . $keywords . '%', 'LIKE')
        ->condition('w.address', '%' . $keywords . '%', 'LIKE'));
    }
    else {
      $query = $this->connection->select('uc_wishlists', 'w');
      $query->fields('w', [
        'wid',
        'title',
      ]);
    }
    $query->condition('w.private', 0, '=');
    $result = $query->orderBy('w.title')->execute;

    // @todo
    // extend default pager limit
    // $query->extend('PagerDefault')->limit(25)->execute();
    return $result;
  }

  /**
   * Retrieves a specific wish list by passing a wid as the parameter.
   *
   * @param int $wid
   *   Displays a particular wish list by retrieving
   *   its wid.
   */
  public function getWishlist($wid) {
    $this->connection->query("SELECT * FROM {uc_wishlists} WHERE wid = :wid", [':wid' => $wid]);
  }

  /**
   * Refers to the current user account to be selected.
   *
   * @param int $rid
   *   Refers to the user role id for a specific user
   *   account.
   * @param string $created
   *   Refers to the created user for a particular user
   *   account.
   */
  public function selectAccounts($rid, $created) {
    $query = $this->connection->select('users', 'u');
    $query->innerJoin('user_roles', 'ur', 'u.uid = ur.uid');
    $query->where('ur.rid = :rid AND u.created < :created', [
      ':rid' => $rid,
      ':created' => $created,
    ]);
    return $query->execute();
  }

  /**
   * A specific wish list product can be selected by for altering/removing it.
   *
   * @param int $wid
   *   Refers to a particular wish list id.
   *
   * @return string
   *   Relect(s) a any specific wishlist product.
   */
  public function selectWishlistProducts($wid) {
    $query = $this->connection->select('node', 'n');
    $query->join('uc_wishlist_products', 'w', 'n.nid = w.nid');
    $query->fields('w');
    $query->addField('n', 'vid');
    $query->condition('w.wid', $wid);
    $query->addTag('node_access');
    $query->join('node_field_data', 'f', 'n.nid = f.nid');
    $query->addField('f', 'title');

    $result = $query->execute();
    return $result;
  }

  /**
   * The wish list gets updated on altering quantity of any product.
   *
   * @param int $wpid
   *   Refers to a particular wish list product id.
   * @param int $qty
   *   Refers to the quantity assigned to the corresponding
   *   wish list product.
   */
  public function updateWantedQuantity($wpid, $qty) {
    $this->connection->update('uc_wishlist_products')->fields(['qty' => $qty])->condition('wpid', $wpid, '=')->execute();
  }

  /**
   * Deletes a specific user wish list by passing the wid as a parameter.
   *
   * @param int $wid
   *   Points to a particular wish list id.
   */
  public function deleteWishlist($wid) {
    $this->connection->delete('uc_wishlists')->condition('wid', $wid)->execute();
    $this->connection->delete('uc_wishlist_products')->condition('wid', $wid)->execute();
  }

  /**
   * Checks availability of a particular product within a wish list.
   *
   * @param int $wid
   *   Refers to a particular wish list id.
   * @param int $pid
   *   Refers to a particular product id.
   *
   * @return bool
   *   Returns true if product within list, otherwise false.
   */
  public function isProductInWishlist($wid, $pid) {
    $this->connection->query("SELECT * FROM {uc_wishlist_products} WHERE nid = :pid AND wid = :wid", [':pid' => $pid, ':wid' => $wid]);
    return $this->connection;
  }

  /**
   * Removes any specific wish list product from list.
   *
   * @param int $wpid
   *   Refers to the wish list product id.
   *
   * @return string
   *   Removes the selected item from the wishlist.
   */
  public function removeItem($wpid) {
    $this->connection->delete('uc_wishlist_products')->condition('wpid', $wpid)->execute();
    return $this->connection;
  }

  /**
   * This function is invoked to remove a specific product using the pid.
   *
   * @param int $pid
   *   Refers to the id of a particular product.
   *
   * @return string
   *   Removes the desired product using the pid.
   */
  public function removeProduct($pid) {
    $this->connection->delete('uc_wishlist_products')->condition('nid', $pid)->execute();
    return $this->connection;
  }

}
