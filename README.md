# Drupal 9 Module Starter
A Ready to use Drupal 9 Module Starter.

It includes a Bash Script to automatically rename all files with your module name.

## This Module give you a ready to use Example of
- Drupal Module with file structure
- Page Controller with Page at /your-module-name
- Block Module with Inline Form
- Block Module with Twig Template
- Settings Form
- JS / SCSS

## Alternative
If you just need a Module Skeleton please use

```drush generate```


## How to use
```bash
# Go to destination Path
cd /web/modules/custom

# get the module
git clone https://github.com/oliversteiner/mollo_module

# rename directory mollo_module with your module_name
mv mollo_module your_module_name

# change to your module
cd your_module_name

# delete git directory:
rm -rf .git

# make the rename script executable
chmod +x setup.sh

# start script
./setup.sh

# enable your new module
drush en your_module_name

```


## Other Resources
https://git.drupalcode.org/project/examples/-/tree/3.x/modules



