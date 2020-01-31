GaiaBB Unified Installer

Welcome to GaiaBB!

Contents:

* Installation
* Upgrade
* Repair
  - In case of emergency
* Security
* Known issues


Installation

Installation has been streamlined in this release. The process for installation is simple:

* Create a MySQL database, noting all the details for later
* Upload all the files and folders to your host (which will we will call www.example.com)
* Type in the address of the installation folder (such as http://www.example.com/forum/install/)
* You should see a welcome screen. Click Next
* Read and agree to the License
* Fill in the details of the database configuration and the other settings
* Type in the super admininistrator account details. Click Next
> Your forum will now be installed

You must remove the install/ directory from the system once installation has finished to use your
new forum. 


Upgrade

The upgrade process is reasonably robust. Like all upgrades, you must have a full
backup prior to starting the upgrade, as if something goes wrong, GaiaBB cannot help
you and you will lose everything.

Upgrade is supported only for XMB 1.9.11 release. It should work with
a bit of effort on other releases, but they are untested and are not supported.

If you have any hacks installed, they will be lost by this process. 

To run the upgrade process is identical to the installation process:

Assuming your host is http://www.example.com, type in:
http://www.example.com/forums/install/

The installer will detect your installation and offer to upgrade, repair or re-install.

* Choose Upgrade and click Next
* Enter the super admin username and password. The e-mail address is ignored
> The upgrade process will suspend forum operations and upgrade the database (if necessary).
  Once complete, the upgrade process will re-enable the forum.

It is safe to re-run the upgrade process if it stops for any reason. If you have hacks
installed, it may take several runs of the upgrade process for upgrade to complete.

You must remove the install/ directory from the system once upgrade has finished to
resume board operations

Repair GaiaBB

If your settings are no longer functional for any reason, use the Repair option
to reset to factory settings. To do this:

* Click Repair
* Type in the super admin username and password
> Repair resets to factory defaults
* Login as the super admin and click on the Administration Panel -> Board Settings
* Revise any settings you need. At the very least, you need to re-enable the
  forum in General settings as Repair puts the forum offline for safety reasons.

You should remove the install/ directory from the system once repair has finished.


Emergency Factory Reset

If you are completely locked out of your forum, you can force the repair
process to bypass super admin credential checks. When changed, the repair
will instead create a super admin account and fix up the settings record.

Create a file called "emergency.php" and upload to the install directory.
The emergency.php file should contain only these lines (and no others):

<?php
    define('RECOVER', true);
?>

DO NOT LEAVE THIS FILE ON YOUR SYSTEM ONCE YOU HAVE RECOVERED! GaiaBB will
not run with this file in place. We strongly suggest you remove the install
folder to be on the safe side.

When in this mode, anyone who knows of the existence of this mode can do
anything the repair utility can do, including locking you out. If this utility
is being misused whilst in emergency mode, remove the install directory 
(including emergency.php) and config.php. This will prevent anyone from 
using the board until you try again later. You may need to perform repair's
actions from within your CPanel or other administrative interface to the 
database if you are under severe attack.

If you have run this due to a criminal attack on your system, you should
disable all super admin and admin accounts other than the one you have just
recreated and ask the users via e-mail to contact you for new accounts.
These accounts should have new passwords given to them as otherwise the
attacker who compromised you may re-gain access to your system.


Security

The install directory, when there is no "emergency.php" file in place, is
safe enough to leave on the file system after installation.

However, for maximum safety, it is strongly recommended you remove the
installation folder once you have installed, upgraded or repaired the forum.


Known Issues

* If you have hacks installed, upgrade may need to be re-run several times
  for upgrade to complete. The reason for this is that hacks sometimes
  create their columns out of order, and upgrade can only handle one out of
  order column ordering issue per run. We only support unhacked databases,
  so we never have out of order columns so this limitation will not be fixed

* If you have extra indexes (typically from well-written hacks), upgrade may
  fail, requiring you to drop them manually prior to continuing.

  Upgrade does not expect to drop indexes and cannot continue if there are any.
  The worst offenders in this regard is when the index is the current primary
  key - upgrade cannot fix this type of problem - you will have to resolve it
  manually.

GaiaBB Team January 2020