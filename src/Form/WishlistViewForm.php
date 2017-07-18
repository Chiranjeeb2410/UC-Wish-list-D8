<?php
namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Component\Utility\Xss;

class WishlistViewForm extends FormBase {

    protected $database;

    public function __construct()
    {
        $this->database = new \Drupal\uc_wishlist\Database\DBQuery(\Drupal::database());
    }
    /**
     * {@inheritdoc}
     */
    public function getFormId(){
        return 'uc_wishlistViewForm';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $items = null, $wid = null, $own = null){
        $form['items'] = array(
            '#tree' => TRUE,

        );

        $form['#attached']['library'][] = 'uc_wishlist/ideal_image_slider';
        $form['#attached']['library'][] = 'uc_wishlist/default';
        $sliderNumber = 1;
        // Load each wish list product and add it to the form array.
        $itemNum = 0;
        foreach ($items as $item) {
            $node = \Drupal\node\Entity\Node::load($item->nid);
            $element = array(
                '#prefix' => '<div class="uc_wishlist_product_item">',
                '#suffix' => '</div>'
            );

            $element['nid'] = array(
                '#type' => 'hidden',
                '#value' => $node->id(),
            );
            $element['wpid'] = array(
                '#type' => 'hidden',
                '#value' => $item->wpid,
            );
            $element['module'] = array(
                '#type' => 'value',
                '#value' => 'uc_product',
            );

            if ($own) {
                $element['remove'] = array(
                    '#type' => 'checkbox',
                    '#title' => 'Remove'
                );
            }

            $item->haveqty = 0;
            if (is_array($item->purchase)) {
                $item->haveqty = count($item->purchase);
            }

            $element['title'] = array(
                '#type' => 'item',
                '#markup' => Link::fromTextAndUrl($item->title, Url::fromRoute('entity.node.canonical', ['node'=>$node->id()]))->toString(),
            );
            $imagesFound = false;
            $imageUrls = array();
            $numberOfImages = 0;
            while(!$imagesFound)
            {
                if($node->get('uc_product_image')[$numberOfImages] != null) {
                    $imageUrls[] = $node->get('uc_product_image')[$numberOfImages]->entity->url();
                    $numberOfImages = $numberOfImages + 1;
                } else {
                        $imagesFound = true;
                    break;
                }
            }
            $element['images'] = array(
                '#tree'=>true,
                '#prefix' => '<div id="wishlist_image_container"> <div class="wishlist_slider" id="wishlist_slider_'.$sliderNumber.'" style="width:150px;height:150px;" class="uc_wishlist_product_images">',
                '#suffix' => '</div></div>'
            );
            $sliderNumber++;
            foreach($imageUrls as $key=>$value)
            {
                $element['images']['image_'.$key] = array(
                    '#type' => 'item',
                    '#markup' => '<img alt="test" class="uc_wishlistProductImage" src="'.$value.'" width="150" height="150" />',
                    '#theme_wrappers' => array(),

                );
            }

            $description = $node->get('body')->getValue();

            // Now allow alterations via hook_uc_product_description_alter().

            if ($description) {
                $element['description'] = array(
                    '#type' => 'item',
                    '#markup' => mb_strimwidth($description[0]['value'],0,50,'...'.Link::fromTextAndUrl('More', Url::fromRoute('entity.node.canonical', ['node'=>$node->id()]))->toString()),
                );
            }

            $element['node'] = array(
                '#type' => 'value',
                '#value' => $form_state->get('variant') ?: $node,
            );

            //$element['#total'] = $item->price; //* $item->qty;

            $element['data'] = array(
                '#type' => 'hidden',
                '#value' => serialize($item->data),
            );
            if($own) {
                $element['wanted_qty'] = array(
                    '#type' => 'uc_quantity',
                    '#title' => 'Wanted Quantity',
                    '#default_value' => $item->qty
                );
            }
            else
            {
                $element['wanted_qty'] = array(
                    '#type' => 'item',
                    '#title' => 'Wanted Quantity',
                    '#markup' => '<p class="wanted_quantity">'.$item->qty.'</p>'
                );
            }
            $price = $node->get('price')->getValue()[0]['value'];
            $element['total_price'] = array(
                '#type'=>'item',
                '#title'=>'Price for wanted quantity',
                '#markup'=>'<p class="total_price"$>'.floatval ($price)*$item->qty.'</p>',
            );
            $element['qty'] = array(
                '#type' => 'uc_quantity',
                '#title' => 'Your Quantity',
                '#default_value' => '1'
            );
            $element['price'] = array(
                '#type'=>'item',
                '#title'=>'Your Price',
                '#markup'=>'<p class="price">$'.floatval ($price).'</p>',
            );



            // Checking if uc_stock module is install in the site and
            // prevent user to add product into cart if the stock value of the product
            // is equal to 0.

            // Checking if uc_stock module install in the site.

            if (\Drupal::moduleHandler()->moduleExists('uc_stock')) {

                // If product kit module is installed in the site and wishlist node type
                // is product kit.
                if (\Drupal::moduleHandler()->moduleExists('uc_product_kit') && $node->get('type')->getValue() == 'product_kit') {

                    // Getting the number of products attached with the Product Kit.
                    // As there is no stock configuration, so we will check the stock value
                    // of the each product of Product Kit.

                    // If all products of the Product Kit has stock active, then we allow
                    // user to purchase product kit.
                    // @var unknown_type .

                    $products = $node->get('products')->getValue();

                    $stock = TRUE;

                    // Looping through each product.
                    foreach ($products as $product) {

                        // Checking stock level of the product.
                        if (!uc_stock_level($product->model)) {
                            $stock = FALSE;
                        }
                    }
                }
                else {
                    // Getting stock value of the particular product SKU.
                    // It will return FALSE, if stock level is not active to product SKU.
                    $sku = $node->get('model')->getValue();
                    $stock = uc_stock_level($sku[0]['value']);
                }

                if ($stock) {

                    $element['addcart'] = array(
                        '#type' => 'submit',
                        '#name' => 'addcart-' . $itemNum,
                        '#value' => 'Add to cart',
                        '#submit' => array('addToCart')
                    );

                }
                else {
                    $element['addcart'] = array(
                        '#type' => 'item',
                        '#name' => 'addcart-' . $itemNum,
                        '#markup' => 'Out Of Stock',
                    );
                }
            }
            else {
                $element['addcart'] = array(
                    '#type' => 'submit',
                    '#name' => 'addcart-' . $itemNum,
                    '#value' => 'Add to cart',
                    '#submit' => array('addToCart')
                );
            }
            $itemNum++;
            $form['items'][] = $element;
        }

        $form['wid'] = array(
            '#type' => 'hidden',
            '#value' => $wid,
        );

        if ($own) {

            $form['own'] = array(
                '#type' => 'value',
                '#value' => TRUE,
            );
            $form['update'] = array(
                '#type' => 'submit',
                '#attributes' => array('class'=> array('uc_wishlist_update_wishlist')),
                '#name' => 'uc_wishlist_update_wishlist',
                '#value' => 'Update wish list',
            );
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state){
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state){

        $values = $form_state->getValues();
        //get the products post data and iterate them to update one by one
        $items = $values['items'];
        foreach($items as $item)
        {
            $wpid = $item['wpid'];
            $remove = $item['remove'];
            $node = \Drupal\node\Entity\Node::load($item['nid']);
            $title = $node->get('title')->getValue()[0]['value'];
            $title = Xss::filter($title);

            //check to see if the user wanted to remove this product from the wish list and if so then delete it
            if($remove)
            {
                $this->database->remove_item($wpid);
                drupal_set_message($this->t('<b>@product_title</b> has been removed from <a href="@url">your wish list</a>.',['@product_title'=>$title,'@url'=>Url::fromRoute('uc_wishlist.wishlist')]));
            }
            else
            {
                //update the information for this product in the wish list
                //user wanted quantity of the product
                $wanted_qty = $item['wanted_qty'];
                //dpm($wpid);
                drupal_set_message($this->t('<b>@product_title</b> \'Wanted Quantity\' has been updated. <a href="@url">your wish list</a>.',['@product_title'=>$title,'@url'=>Url::fromRoute('uc_wishlist.wishlist')]));
                $database = new \Drupal\uc_wishlist\Database\DBQuery(\Drupal::database());
                $database->updateWantedQuantity($wpid,$wanted_qty);
            }
        }
        drupal_set_message($this->t('Your wish list has been updated'));
        $form_state->setRedirectUrl(Url::fromRoute('uc_wishlist.wishlist'));
    }
}
