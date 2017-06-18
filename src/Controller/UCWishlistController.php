<?php
namespace Drupal\uc_wishlist\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Component\Utility\Xss;
use Drupal\uc_wishlist\Database;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UCWishlistController extends ControllerBase implements ContainerInjectionInterface ,ContainerAwareInterface
{

    protected $db;

    public function __construct()
    {
        $this->db = new Database\DBQuery(\Drupal::database());
    }

    public static function create(ContainerInterface $container)
    {
        return new static($container->get('module_handler'));
    }

    public function setContainer(ContainerInterface $container = null)
    {

    }

    public function AdminWishlist()
    {
        $rows = array();

        $header = array(
            array(
                'data' => t('User'),
                'field' => 'u.name',
                'sort' => 'asc',
            ),
            array(
                'data' => t('Title'),
                'field' => 'w.title',
            ),
            array(
                'data' => t('Expiration date'),
                'field' => 'w.expiration',
            ),
            array('data' => t('Status')),
        );

        // Database returns a paged list of wish lists.

        $result = $this->db->getAllWishlist();

        foreach ($result as $wishlist) {

            $deleteUrl = Url::fromRoute('uc_wishlist.admin_delete', ['uc_wishlist' => $wishlist->wid]);
            $link_options = array(
                'attributes' => array(
                    'class' => array(
                        'my-first-class',
                        'my-second-class',
                    ),
                ),
            );
            $deleteUrl->setOptions($link_options);

            $expired = '';
            if ($wishlist->expiration < REQUEST_TIME) {
                $expired = t('Expired');
            } else {
                $expired = t('Active');
            }
            $deleteUrl = Link::fromTextAndUrl($expired . ' | Delete', $deleteUrl)->toString();
            $account = \Drupal\user\Entity\User::load($wishlist->uid); // pass your uid
            $name = $account->getAccountName();
            $rows[] = array(
                $name ? Link::fromTextAndUrl($name, Url::fromRoute('entity.user.canonical', ['user' => $wishlist->uid]))->toString() : t('Anonymous'),
                Link::fromTextAndUrl($wishlist->title, Url::fromRoute('uc_wishlist.wishlist_view', ['wid' => $wishlist->wid]))->toString(),
                \Drupal::service('date.formatter')->format($wishlist->expiration),
                $deleteUrl,
            );
        }

        if (empty($rows)) {
            $rows[] = array(array(
                'data' => t('No wish lists found.'),
                'colspan' => 4,
            ));
        }
        return array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
        );
    }
}

