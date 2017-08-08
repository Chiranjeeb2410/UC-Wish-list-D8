<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
* Email form for wishlist.
*/
class WishlistEmailForm extends FormBase {

  public function getFormId(){
    return 'uc_wishlistEmailForm';
  }

  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {

    $account = \Drupal::currentUser();
    // TODO: Handle multiple wishlists?
    $wid = uc_wishlist_get_wid($account->id());

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#required' => TRUE,
      '#description' => $this->t('Enter Email Subject.'),
    ];
    $form['recipients'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recipients'),
      '#required' => TRUE,
      '#description' => $this->t('Email address of the reciepient. Seperate recipients using a comma.'),
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#rows' => 6,
      '#required' => TRUE,
      '#description' => $this->t('Enter Email Message.'),
    ];
    $form['wid'] = [
      '#type' => 'value',
      '#value' => $wid,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * Validate function for the wishlist Email form use to send email.
   *
   * @param array $form
   *   Form array.
   *
   * @param array $form_state
   *   Formstate array contains the user submitted values.
   *
   * @return none
   *   Returns nothing.
   */

  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Exploding the emails id of the recpients.
    $emails = explode(',', $form_state->getValue['recipients']);
    $emails = array_filter($emails);
    foreach ($emails as $email) {
      $email = trim($email);
      if ($email != '' && !valid_email_address($email)) {
        // Generating error.
        $form_state->setError('emails', $this->t('%email is not a valid email address', ['%email' => $email]));
      }
    }
  }

  /**
   * Submit callback for the wishlist Email form use to send email.
   *
   * @param array $form
   *   Form array.
   *
   * @param array $form_state
   *   Formstate array contains the user submitted values.
   *
   * @return none
   *   Returns nothing.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Getting subject of the mail and its sanitization.
    $subject = Html::escape($form_state->getValue['subject']);
    // Getting message of the mail and text sanitization.
    $message = check_markup($form_state->getValue['message']);
    $wid = $form_state->getValue['wid'];
    $message = $message . "\n" . l(t('Wishlist'), 'wishlist/' . $wid);
    foreach ($emails as $email) {
      uc_wishlist_send_mail($email, $subject, $message);
    }
    $form_state->setRedirect('uc_wishlist.user_wishlist_email');
  }
}

