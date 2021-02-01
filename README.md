# Mollo Module Template


Use this for a Drupal Starter Module

copy to /modules/custom/
delete git directory
rm -rf .git
rename directory 'mollo_module' with your module name

### To  rename automatically all files and variables for your module name
```
# go to your module
/modules/custom/your_module/

# make the rename script executable
chmod +x rename_module.sh

# start script
./rename_module.sh

```



Predefined Functions:

Page at /your-module-name

Block:
-  Twig Template Block
-  Inline Template Block


https://git.drupalcode.org/project/examples/-/tree/3.x/modules
