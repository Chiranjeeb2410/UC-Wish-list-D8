![GSoC 2017](https://img.shields.io/badge/GSoC-2017-red.svg)
![Drupal 8.x](https://img.shields.io/badge/Drupal-8.x-blue.svg) 
# [Porting Uc Wishlist to Drupal 8](https://www.drupal.org/project/uc_wish_list) 

## Google Summer of Code 2017 | Drupal

#### Student: [Chiranjeeb Mahanta (musk_1107)](https://www.drupal.org/u/chiranjeeb2410)

#### Mentor: [Naveen Valecha (naveenvalecha)](https://www.drupal.org/u/naveenvalecha)

## Project Overview:

The [UC wish list](https://www.drupal.org/project/uc_wishlist) module, adds wish list/gift registry support to the [Ubercart](https://www.drupal.org/project/ubercart) store, an open source e-commerce solution fully integrated with the leading open source CMS, Drupal. This module, for instance, would specifically allow customers to create and administer personalized wish list of products in their [Ubercart](https://www.drupal.org/project/ubercart) store. Other potential customers could then refer to those wish lists to get a better understanding about what items they should be purchasing and thereby purchase items on behalf of the wish list creators.

The [UC wish list](https://www.drupal.org/project/uc_wishlist), would involve the use of the [Form API (FAPI)](https://www.drupal.org/docs/8/api/form-api/introduction-to-form-api), which provides a definitive, easy to use, easy to extend and secure way of adding forms to your Drupal website. It functions alongside the normal shopping cart, presenting the customer with an "Add to wish list" button beside the normal "Add to cart" button on product pages.

### Installing/Configuring the module:
#### For a LINUX-based system:
The Uc Wishlist module installation involves the following procedure:
- **Dependencies involved:** The following essentials are required for a successful installation/configuration of the Uc Wishlist module:
  - [Ubercart 8.x-4.0-alpha5](https://www.drupal.org/project/ubercart/releases/8.x-4.0-alpha5)
  - **uc_country**  
  - **uc_store**
  - **uc_product** 
  - **uc_order**
  - **uc_cart**
  - [Composer](https://getcomposer.org/download/)/[Drush 8.1.x](http://docs.drush.org/en/master/install/) (for Drupal 8) 
- **Installing/Configuring the module:** 
  - Enter the drupal root installation directory and `cd` into `modules/` directory.
  
  - Execute the drush command `drush dl ubercart` for downloading the latest compatible version of Ubercart for Drupal 8.

  - Run `drush en ubercart` for installing/enabling the Ubercart module.
  
  - Clear caches for the impending changes to take place by executing `drush cr`. This command would also display the probable errors (if there are any) faced during installation of the module thereby temporarily terminating the installation.
  
  - Assuming no errors faced during the installation of [Ubercart](https://www.drupal.org/project/ubercart), the next step would be to download the Uc Wishlist module. Clone the `8.x-1.x` release branch of the repository in that folder `git clone --branch 8.x-1.x chiranjeeb2410@git.drupal.org:sandbox/chiranjeeb2410/2880059.git uc_wishlist`.
  
  - Enable the module by executing `drush en uc_wishlist`. Upon execution of the command, the following dependencies would be prompted to install/enable for successfully enabling the Uc Wishlist module: *uc_wishlist, uc_country, uc_store, uc_product, uc_order* and *uc_cart*. Enter y/yes in terminal to install the above dependencies along with *uc_wishlist*.
  
  - Drush would return a confirmation for the successful installation of the module. The next step would be to clear the caches for the module using drush cr to get registered to the system and function successfully.

Once installed the module can be used to perform the following actions:

- **Add Administrator wishlist settings:** Provides a set of salient features to the site administrator to enable certain functionalities for a wishlist in general. Accessed through the address `/admin/store/config/wishlist`.

- **Enable ‘Add to Cart’ and ‘Add to wishlist’ features:** These functions enable products to be added to the cart or a wish list, with the help of the mentioned buttons.  

- **Enable user to view/update a wish list:** This functionality would entitle the user to access/view a specific wish list and modify it as per the options listed. Accessed through `/wishlist`.  

- **Enable ‘Search Wishlist’ functionality:** Allows the user to search an individual wishlist amongst the list of wish lists and access/modify it later. Accessed through `/wishlist/search`.

- **Option to email wishlist to other users:** This functionality enables the  user with the possibility of e-mailing any specific wishlist to other users or potential customers. Directed through the address `/wishlist-email`.

- **Add user wishlist settings:** Extend the user with the option to modify/update a wish list and include specific features for individual wishlists. Directed through the link `/admin/store/config/user-settings`.

