# Khalti Payment Module for Magento 2

### Requirement & Compatibility
- Requires magento version at least: `2.x`
- Tested and working upto `Magento 2.4.0`

### Installation
- Create the following folder structure inside "app" folder and copy all the files
  "Fourwallsinn/Khalti"
- After you have copied all the files the folder structure should be like this
  "app/code/Fourwallsinn/Khalti/UPLOADED_FILES"
- Enable Khalti Module
    `php bin/magento module:enable --clear-static-content Fourwallsinn_Khalti`
- Run Setup Upgrade
  `php bin/magento setup:upgrade`
- Run DI Compilation to generate classes
    `php bin/magento setup:di:compile`
- If you are on Production Environment, make sure you run the following command as well
  `php bin/magento setup:static-content:deploy`
- Finally Flush the Cache
    `php bin/magento cache:flush`
