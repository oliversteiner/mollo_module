#!/bin/bash
# Replace Rename Mollo Module


red=$(tput setaf 1)
green=$(tput setaf 2)
yellow=$(tput setaf 3)
blue=$(tput setaf 4)
magenta=$(tput setaf 5)
cyan=$(tput setaf 6)
reset=$(tput sgr0)


OLD_NAME='mollo_module'
OLD_TITLE='Mollo Module'
OLD_NAME_DASH="mollo-module"
OLD_NAME_PASCAL="MolloModule"

rename_files() {
  echo "Renaming files..."

  echo "${cyan}snake_case:${reset}"
  #rename snake_case
  find . -type f -maxdepth 3 -name "$OLD_NAME*" | while
    read -r FNAME1
  do
    echo " - $FNAME1"
    mv "$FNAME1" "${FNAME2//$OLD_NAME/$NEW_NAME}"
  done

  echo "${cyan}kebab-case:${reset}"
  # rename kebab-case
  find . -type f -maxdepth 3 -name "$OLD_NAME_DASH*" | while
    read -r FNAME2
  do
    echo " - $FNAME2"
    mv "$FNAME2" "${FNAME2//$OLD_NAME_DASH/$NEW_NAME_DASH}"
  done

  echo $OLD_NAME_PASCAL

  echo "${cyan}PascalCase:${reset}"
  # Rename PascalCase
  find . -type f -maxdepth 3 -name "$OLD_NAME_PASCAL*" | while
    read -r FNAME3
  do
    echo " - $FNAME3"
    mv "$FNAME3" "${FNAME3//$OLD_NAME_PASCAL/$NEW_NAME_PASCAL}"
  done

}

replace_name() {
  echo "Replacing name..."

  grep -rl "$OLD_NAME" . --exclude-dir=.git --exclude=\*.sh | xargs sed -i "" "s/$OLD_NAME/$1/g"
  grep -rl "$OLD_NAME_DASH" . --exclude-dir=.git --exclude=\*.sh | xargs sed -i "" "s/$OLD_NAME_DASH/$NEW_NAME_DASH/g"
  grep -rl "$OLD_NAME_PASCAL" . --exclude-dir=.git --exclude=\*.sh | xargs sed -i "" "s/$OLD_NAME_PASCAL/$NEW_NAME_PASCAL/g"

}

replace_title() {
  grep -rl "$OLD_TITLE" . --exclude-dir=.git --exclude=\*.sh | xargs sed -i "" "s/$OLD_TITLE/$1/g"
}

# Greetings
echo " "
echo "---------------------------------"
echo "    Rename Module "
echo "---------------------------------"
echo " "

# Get new Name
echo "${yellow}Give a NAME for the Module:${reset}"
echo "Example: 'my_fancy_module'"
read -p "name: " INPUT
echo " "

# Check for Space in name
if [[ $NEW_NAME == *" "* ]]; then
  echo "${red}Error: Module Name can't contain spaces${reset}"
  echo "Please try again"
  eche ""
  exit 1
fi
NEW_NAME=$(echo "$INPUT" | tr '[:upper:]' '[:lower:]') # to lowercase
NEW_NAME_DASH="${NEW_NAME//_/-}"
NEW_NAME_PASCAL=$(echo "$NEW_NAME" | perl -pe 's/(^|_)./uc($&)/ge;s/_//g')

# Get new Title
echo "${yellow}Give a TITLE for the Module${reset}"
echo "Example: 'My Fancy Module'"
read -p " title: " NEW_TITLE

# Confirm
echo " "

echo "--------------------------------"
echo "New Module Name is ${cyan}$NEW_NAME${reset}"
echo "New Module Title is ${cyan}$NEW_TITLE${reset}"
echo "--------------------------------"
echo "Renaming Files in ${cyan}"
pwd
echo "${reset}--------------------------------"
echo "${red}Is this OK?${reset}"
select yn in "Yes" "No"; do
  case $yn in
  Yes)
    echo "working..."
    replace_name "$NEW_NAME"
    replace_title "$NEW_TITLE"
    rename_files "$NEW_NAME"
    # rename directory

    echo "--------------------------------"
    echo "${green}Renaming all Files done${reset}"
    echo ""
    echo "Your next steps:"
    echo "${cyan} - delete old Git Repository:${reset}    rm -rf .git"
    echo "${cyan} - rename directory 'mollo_module' to '$NEW_NAME' ${reset}"
    echo "${cyan} - enable your new Module ${reset} drush en $NEW_NAME"
    echo "${cyan} - build some awesome Module ${reset}"
    echo ""
    echo "          bye                   "
    echo "--------------------------------"
    break
    ;;
  No) exit ;;
  esac
done
