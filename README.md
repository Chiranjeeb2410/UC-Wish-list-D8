Google Summer of Code 2017 | Drupal

Student: Chiranjeeb Mahanta (musk_1107)

Mentor: Naveen Valecha (naveenvalecha)

Project Overview:

The UC wish list module, adds wish list/gift registry support to the Ubercart store, an open source e-commerce solution fully integrated with the leading open source CMS, Drupal. This module, for instance, would specifically allow customers to create and administer personalized wish lists of products in their Ubercart store. Other potential customers could then refer to those wish lists to get a better understanding about what items they should be purchasing and thereby purchase items on behalf of the wish list creators.

Installation Walkthrough: 
       For a LINUX-based system
The Uc Wishlist module installation involves the following procedure:

Dependencies involved: The following essentials are required for a successful installation/configuration of the Uc Wishlist module:
Ubercart 8.x-4.0-alpha5
uc_country
uc_store
uc_product
uc_order
uc_cart
Composer/Drush 8.1.x (for Drupal 8)

Installing/Configuring the module:

Enter the drupal root installation directory and cd into modules/ directory.
Execute the drush command drush dl ubercart for downloading the latest compatible version of Ubercart for Drupal 8.
Run drush en ubercart for installing/enabling the Ubercart module.
Clear caches for the impending changes to take place by executing drush cr. This command would also display the probable errors (if there are any) faced during installation of the module thereby temporarily terminating the installation.
Assuming no errors faced during the installation of Ubercart, the next step would be to download the Uc Wishlist module. Clone the 8.x-1.x release branch of the repository in that folder git clone --branch 8.x-1.x chiranjeeb2410@git.drupal.org:sandbox/chiranjeeb2410/2880059.git uc_wishlist.
Enable the module by executing drush en uc_wishlist. Upon execution of the command, the following dependencies would be prompted to install/enable for successfully enabling the Uc Wishlist module: uc_wishlist, uc_country, uc_store, uc_product, uc_order and uc_cart. Enter y/yes in terminal to install the above dependencies along with uc_wishlist.
Drush would return a confirmation for the successful installation of the module. The next step would be to clear the caches for the module using drush cr to get registered to the system and function successfully.

The Uc Wishlist module is now ready for use.

Enjoy!
