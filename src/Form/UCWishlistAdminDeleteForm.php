<?php
/**
 * Confirm the deletion of a wish list.
 */
namespace Drupal\uc_wishlist\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

class UCWishlistAdminDeleteForm extends ConfirmFormBase {

    protected $user;
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
    public function buildForm(array $form, FormStateInterface $form_state, $wishlist = null) {
        $this->node = $node;
        $this->featureId = $fid;
        $this->feature = uc_product_feature_load($pfid);
        $wishlist = uc_wishlist_load($wishlist);
        $form['wishlist'] = array(
            '#type' => 'value',
            '#value' => $wishlist,
        );
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        $database = new \Drupal\uc_wishlist\Database\DBQuery(\Drupal::database());
        $database->deleteWishlist($values['wishlist']->wid);
        drupal_set_message(t('@title has been deleted.', array('@title' => str_replace('.','',$values['wishlist']->title))));
        $form_state->setRedirect('uc_wishlist.admin_wishlist');
    }
}
