<?php

namespace Drupal\uc_wishlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Creates the WishlistEmailForm class.
 *
 * Allows a user to email a specific wish
 * list to another user/recipient.
 */
class WishlistEmailForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

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

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Exploding the emails id of the recpients.
    $emails = explode(',', $form_state->getValue['recipients']);
    $emails = array_filter($emails);
    foreach ($emails as $email) {
      $email = trim($email);
      if (strlen($form_state->getValue('recipients')) < 5) {
        $form_state->setErrorByName('recipients', $this->t('Recipients field is required.'));
      }
      if ($email != '' && !valid_email_address($email)) {
        // Generating error.
        $form_state->setError('emails', $this->t('%email is not a valid email address', ['%email' => $email]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    /**$emails = explode(',', $form_state->getValue['recipients']);
    $emails = array_filter($emails);
    // Getting subject of the mail and its sanitization.
    $subject = Html::escape($form_state->getValue['subject']);
    // Getting message of the mail and text sanitization.
    $message = check_markup($form_state->getValue['message']);
    $wid = $form_state->getValue['id'];
    $message = $message . "\n" . l(t('Wishlist'), 'wishlist/' . $wid);
    foreach ($emails as $email) {
    uc_wishlist_send_mail($email, $subject, $message);
    }*/
    drupal_set_message($this->t('Your wishlist has been emailed!', ['recipients' => $form_state->getValue('recipients')]));
    $form_state->setRedirect('uc_wishlist.wishlist.email_form');
  }

}
