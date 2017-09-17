<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Constructs the UserWishlistSettingsForm class.
 *
 * Contains user wishlist settings to extend the
 * user with the option to modify/update a wish list.
 */
class UserWishlistSettingsForm extends ConfigFormBase {

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
    return 'wishlist_user_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['uc_wishlist.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('uc_wishlist.settings');
    $wid = $form_state->getValues('id');
    $wishlist = uc_wishlist_load($wid);

    $form = [];

    $form['wishlist'] = [
      '#type' => 'fieldset',
    ];
    $form['wishlist']['wid'] = [
      '#type' => 'hidden',
      '#value' => $wishlist->wid,
    ];
    $form['wishlist']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $wishlist->title,
      '#required' => TRUE,
    ];
    $form['wishlist']['expiration'] = [
      '#type' => 'date',
      '#title' => $this->t('Event or expiration date'),
      '#default_value' => $expiration,
      '#description' => $this->t('If this wish list is associated with an event or will no longer be relevant on a specific date, enter it here.'),
    ];

    if (!$config->get('default_private', TRUE) && $config->get('allow_private', TRUE)) {
      $form['wishlist']['private'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Private'),
        '#default_value' => $wishlist->private,
        '#description' => $this->t('Check this to make your wish list private and exclude it from wish list search results.'),
      ];
    }

    if ($config->get('save_address', TRUE)) {
      $form['wishlist']['address'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Mailing address'),
        '#description' => $this->t('The address you enter here will be available as a shipping address to anyone who purchases an item from your wish list.'),
      ];

      if (uc_address_field_enabled('first_name')) {
        $form['wishlist']['address']['delivery_first_name'] = uc_textfield(uc_get_field_name('first_name'), empty($wishlist->address->firstname) ? NULL : $wishlist->address->firstname, uc_address_field_required('first_name'));
      }
      if (uc_address_field_enabled('last_name')) {
        $form['wishlist']['address']['delivery_last_name'] = uc_textfield(uc_get_field_name('last_name'), empty($wishlist->address->lastname) ? NULL : $wishlist->address->lastname, uc_address_field_required('last_name'));
      }
      if (uc_address_field_enabled('company')) {
        $form['wishlist']['address']['delivery_company'] = uc_textfield(uc_get_field_name('company'), empty($wishlist->address->company) ? NULL : $wishlist->address->company, uc_address_field_required('company'), NULL, 64);
      }
      if (uc_address_field_enabled('street1')) {
        $form['wishlist']['address']['delivery_street1'] = uc_textfield(uc_get_field_name('street1'), empty($wishlist->address->addr1) ? NULL : $wishlist->address->addr1, uc_address_field_required('street1'), NULL, 64);
      }
      if (uc_address_field_enabled('street2')) {
        $form['wishlist']['address']['delivery_street2'] = uc_textfield(uc_get_field_name('street2'), empty($wishlist->address->addr2) ? NULL : $wishlist->address->addr2, uc_address_field_required('street2'), NULL, 64);
      }
      if (uc_address_field_enabled('city')) {
        $form['wishlist']['address']['delivery_city'] = uc_textfield(uc_get_field_name('city'), empty($wishlist->address->city) ? NULL : $wishlist->address->city, uc_address_field_required('city'));
      }
      if (uc_address_field_enabled('country')) {
        $form['wishlist']['address']['delivery_country'] = [
          '#type' => 'select',
          '#title' => uc_get_field_name('country'),
          '#description' => NULL,
          '#required' => uc_address_field_required('country'),
          '#options' => uc_country_option_list(),
          '#default_value' => isset($wishlist->address->country) ? $wishlist->address->country : uc_store_default_country(),
        ];
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['delivery_country'])) {
          $country_id = intval(check_plain($_POST['delivery_country']));
        }
        else {
          $country_id = isset($wishlist->address->country) ? $wishlist->address->country : uc_store_default_country();
        }
        $form['wishlist']['address']['delivery_zone'] = uc_zone_select(uc_get_field_name('zone'), empty($wishlist->address->zone) ? NULL : $wishlist->address->zone, $country_id, ['required' => uc_address_field_required('zone')]);
        if (isset($_POST['panes']) && count($form['wishlist']['address']['delivery_zone']['#options']) == 1) {
          $form['wishlist']['address']['delivery_zone']['#required'] = FALSE;
        }
      }
      if (uc_address_field_enabled('postal_code')) {
        $form['wishlist']['address']['delivery_postal_code'] = uc_textfield(uc_get_field_name('postal_code'), empty($wishlist->address->postcode) ? NULL : $wishlist->address->postcode, uc_address_field_required('postal_code'), NULL, 10, 10);
      }
      if (uc_address_field_enabled('phone')) {
        $form['wishlist']['address']['delivery_phone'] = uc_textfield(uc_get_field_name('phone'), empty($wishlist->address->phone) ? NULL : $wishlist->address->phone, uc_address_field_required('phone'), NULL, 32, 16);
      }
    }

    $form['wishlist']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save settings'),
    ];

    return $form;

  }

  /**
   * Validation handler for wish list settings form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $wid = $form_state->getValues('id');

    $wishlist = uc_wishlist_load($wid);
    if (!$wishlist) {
      drupal_set_message($this->t('Could not find the specified wish list.'), 'error');
      return FALSE;
    }
    if ($wishlist->id() != $this->account->id() && !$this->account->hasPermission('administer store')) {
      drupal_set_message($this->t('You do not have permission to edit this wish list.'), 'error');
      return FALSE;
    }
  }

  /**
   * Submission handler for wish list settings form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $expiration = mktime(0, 0, 0, $form_state->getValue(['expiration', 'month']), $form_state->getValue(['expiration', 'day']), $form_state > getValue(['expiration', 'year']));
    $values = $form_state->getValues();
    $config = $this->config('uc_wishlist.settings');

    if ($config->get('save_address', TRUE)) {
      $address = [
        'firstname' => $form_state->getValues['delivery_first_name'],
        'lastname' => $form_state->getValues['delivery_last_name'],
        'company' => $form_state->isValueEmpty['delivery_company'] ? '' : $form_state->getValues['delivery_company'],
        'addr1' => $form_state->getValues['delivery_street1'],
        'addr2' => $form_state->isValueEmpty['delivery_street2'] ? '' : $form_state->getValues['delivery_street2'],
        'city' => $form_state->getValues['delivery_city'],
        'country' => $form_state->getValues['delivery_country'],
        'zone' => $form_state->getValues['delivery_zone'],
        'postcode' => $form_state->getValues['delivery_postal_code'],
        'phone' => $form_state->isValueEmpty['delivery_phone'] ? '' : $form_state->getValues['delivery_phone'],
      ];
    }

    else {
      $address = NULL;
    }

    $private = $config->get('default_private', FALSE) ? $config->get('default_private', FALSE) : 0;
    $private = $config->get('allow_private', FALSE) ? $form_state->getValues['private'] : $private;
    drupal_set_message($this->t('Your wish list has been updated.'));
  }

}
