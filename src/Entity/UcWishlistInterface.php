<?php

/* @file
 * Contains \Drupal\uc_wishlist\uc_wishlistInterface.
 */

namespace Drupal\uc_wishlist\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a wish list entity type.
 */
interface UcWishlistInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Renders wishlist title.
   *
   * @return string
   *   Wishlist title.
   */
  public function getTitle();

  /**
   * Sets wishlist title.
   *
   * @param string $title.
   *   Wishlist title.
   *
   * @return $this;
   */
  public function setTitle($title);

  /**
   * Renders wishlist expiration timestamp.
   *
   * @return int
   *   Wishlist expiration timestamp.
   */
  public function getExpirationTime();

  /**
   * Sets wishlist exiration timestamp.
   *
   * @param int $expiration
   *   Wishlist expiration timestamp.
   *
   * @return $this;
   */
  public function setExpirationTime($expiration);

  /**
   * Renders wishlist address.
   *
   * @return string
   *   Wishlist address.
   */
  public function getAddress();

  /**
   * Sets wishlist address.
   *
   * @param string $address
   *   Wishlist address.
   *
   * @return $this;
   */
  public function setAddress($address);

  /**
   * Renders wishlist private status.
   *
   * @return int
   *   Wishlist private status.
   */
  public function getPrivate();

  /**
   * Sets wishlist private status.
   *
   * @param string $description
   *   Wishlist private status.
   *
   * @return $this;
   */
  public function setPrivate($description);

  /**
   * Renders wishlist description.
   *
   * @return string
   *   Wishlist description.
   */
  public function getdescription();

  /**
   * Sets wishlist description.
   *
   * @param string $description
   *   Wishlist description.
   *
   * @return $this;
   */
  public function setdescription($description);
  /**
   * Renders the user id of the owner
   *
   * @return int
   *   id of user.
   */
  public function getUserId();

}
