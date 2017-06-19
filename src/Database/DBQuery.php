<?php

namespace Drupal\uc_wishlist\Database;
use Drupal\Core\Database\Connection;

class DBQuery {

    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getAllWishlist()
    {
        $query = $this->connection->select('uc_wishlists', 'w');
        $query->leftJoin('users', 'u', 'w.uid = u.uid');
        $query->fields('w', array(
            'wid',
            'uid',
            'title',
            'expiration',
        ));
        //$query->addField('u', 'name');
        $result = $query->execute();//$query->extend('PagerDefault')->limit(25)->execute();
        return $result;
    }

    public function createWishlist($fields,$values)
    {
        return $query = $this->connection->insert('uc_wishlists')->fields($fields,$values)->execute();
    }
    public function createWishlistProduct($fields,$values)
    {
        return $query = $this->connection->insert('uc_wishlist_products')->fields($fields,$values)->execute();
    }

    public function getWishlistIdByUser($uid)
    {
        $query = $this->connection->query("SELECT wid FROM {uc_wishlists} WHERE uid = :uid", array(':uid' => $uid))->fetchField();
        return $query;
    }

    public function getWishlistItem($wid, $nid,$data)
    {
        $query = $this->connection->query("SELECT * FROM {uc_wishlist_products} WHERE wid = :wid AND nid = :nid AND data = :data", array(':wid' => $wid, ':nid' => $nid, ':data' => serialize($data)));
        return $query->fetchObject();
    }
    public function getWishlist($wid)
    {
        $query = $this->connection->query("SELECT * FROM {uc_wishlists} WHERE wid = :wid", array(':wid' => $wid));
        return $query;
    }

    public function selectAccounts($rid,$created)
    {
        $query = $this->connection->select('users','u');
        $query->innerJoin('user_roles','ur','u.uid = ur.uid');
        $query->where('ur.rid = :rid AND u.created < :created',array(
            ':rid' => $rid,
            ':created' => $created,
        ));
        return $query->execute();
    }

    public function selectWishlistProducts($wid)
    {
        $query = $this->connection->select('node','n');
        $query->join('uc_wishlist_products', 'w', 'n.nid = w.nid');
        $query->fields('w');
        //$query->addField('n', 'title');
        $query->addField('n', 'vid');
        $query->condition('w.wid', $wid);
        $query->addTag('node_access');
        //$result = $query->execute();
        $query->join('node_field_data','f', 'n.nid = f.nid');
        $query->addField('f', 'title');

        $result = $query->execute();
        //dpm($result);
        return $result;
    }

    public function updateWantedQuantity($wpid,$qty)
    {
        $this->connection->update('uc_wishlist_products')->fields(['qty'=>$qty])->condition('wpid',$wpid,'=')->execute();
    }

    public function deleteWishlist($wid)
    {
        $this->connection->delete('uc_wishlists')->condition('wid', $wid)->execute();
        $this->connection->delete('uc_wishlist_products')->condition('wid', $wid)->execute();
    }

    public function isProductInWishlist($wid,$pid)
    {
        $query = $this->connection->query("SELECT * FROM {uc_wishlist_products} WHERE nid = :pid AND wid = :wid", [':pid'=>$pid,':wid'=>$wid]);
        return $query->fetchAll();
    }

    public function remove_item($wpid) {
        $this->connection->delete('uc_wishlist_products')->condition('wpid', $wpid)->execute();
    }

    public function remove_product($pid) {
        $this->connection->delete('uc_wishlist_products')->condition('nid',$pid)->execute();
    }

}