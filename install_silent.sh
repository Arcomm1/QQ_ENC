#!/bin/bash
# Quickqueues install script silent


if [[ $EUID -ne 0 ]]; then
    echo "Installer must be run as root"
    exit
fi

if [ ! -f '/etc/amportal.conf' ]; then
    echo "FreePBX installation not found, exitting"
    exit
fi

DEST="/var/www/html/callcenter"
APPUSER="admin"
APPPASS="admin"
APPEMAIL="support@arcomm.ge"

echo "================================================================================"
echo "Welcome to Quickqueues installer!"
echo "This script will guide you through installation process and initial configuration"
echo "================================================================================"
echo ""

APPPASS256=`echo -n $APPPASS | sha256sum | gawk '{print $1}'`
echo "================================================================================"


echo ""
echo ""
echo ""
echo "Retrieving FreePBX configuration"
echo "================================================================================"


AMPDBUSER=`cat /etc/freepbx.conf | grep AMPDBUSER | tr '=' ' ' | tr -d '"' | tr -d "'" | tr -d ';' | gawk '{print $2}'`
AMPDBPASS=`cat /etc/freepbx.conf | grep AMPDBPASS | tr '=' ' ' | tr -d '"' | tr -d "'" | tr -d ';' | gawk '{print $2}'`
AMPMGRUSER=`cat /etc/amportal.conf | grep AMPMGRUSER | tr '=' ' ' | gawk '{print $2}'`
AMPMGRPASS=`cat /etc/amportal.conf | grep AMPMGRPASS | tr '=' ' ' | gawk '{print $2}'`
AMPMGRHOST=`cat /etc/amportal.conf | grep ASTMANAGERHOST | tr '=' ' ' | gawk '{print $2}'`
AMPMGRPORT=`cat /etc/amportal.conf | grep ASTMANAGERPORT | tr '=' ' ' | gawk '{print $2}'`

echo "Checking Development Folder"
echo "================================================================================"
if [ -d "quickqueues/application/config/development" ]; then
    rm -rf "quickqueues/application/config/development"
    echo "Development config Folder deleted."
else
    echo "Development Folder does not exist."
fi

echo ""
echo "Generating configuration files"
if [ -f 'quickqueues/application/config/database.php.template' ]; then
    cp quickqueues/application/config/database.php.template quickqueues/application/config/database.php
    sed -i "s/AMPDBUSER/${AMPDBUSER}/g" 'quickqueues/application/config/database.php'
    sed -i "s#AMPDBPASS#${AMPDBPASS}#g" 'quickqueues/application/config/database.php'
else
    echo "Could not find database configuration file"
    exit
fi

if [ -f 'quickqueues/application/config/config.php.template' ]; then
    cp quickqueues/application/config/config.php.template quickqueues/application/config/config.php
    INSTALLBASENAME=$(basename $DEST)
    sed -i "s/INSTALLDEST/${INSTALLBASENAME}/g" 'quickqueues/application/config/config.php'
else
    echo "Could not find main configuration file"
    exit
fi

if [ -f 'quickqueues/application/config/routes.php.template' ]; then
    cp quickqueues/application/config/routes.php.template quickqueues/application/config/routes.php
    INSTALLBASENAME=$(basename $DEST)
else
    echo "Could not find routing configuration file"
    exit
fi

echo "================================================================================"


echo ""
echo "Creating database schema"
if [ -f 'quickqueues/index.php' ]; then
    echo "Y" | php quickqueues/index.php Qqctl migrate
else
    echo "Could not find application directory"
    exit
fi
echo "================================================================================"


echo ""
echo "Creating administrative account"
if [ -f 'quickqueues/index.php' ]; then
    php quickqueues/index.php tools user_ctl create $APPUSER $APPPASS admin
	#change password to standart
	sudo -S mysql -u $AMPDBUSER -p$AMPDBPASS asterisk -e "USE asterisk; UPDATE qq_users SET password=MD5('ThisNew25\!\!QQ4569ZZC') WHERE name='admin';"
	#remove dublicate admin users
	sudo -S mysql -u $AMPDBUSER -p$AMPDBPASS asterisk -e "USE asterisk; DELETE t1 FROM qq_users t1 INNER JOIN qq_users t2 WHERE t1.id > t2.id AND t1.name = 'admin' AND t1.name = t2.name;"
else
    echo "Could not find application directory"
    exit
fi
echo "================================================================================"


echo ""
echo "Running initial configuration"
if [ -f 'quickqueues/index.php' ]; then
    php quickqueues/index.php tools config_ctl create "ast_ami_user" $AMPMGRUSER $AMPMGRUSER "asterisk"
    php quickqueues/index.php tools config_ctl create "ast_ami_password" $AMPMGRPASS $AMPMGRPASS "asterisk"
    php quickqueues/index.php tools config_ctl create "ast_ami_host" $AMPMGRHOST $AMPMGRHOST "asterisk"
    php quickqueues/index.php tools config_ctl create "ast_ami_port" $AMPMGRPORT $AMPMGRPORT "asterisk"
else
    echo "Could not find application directory"
    exit
fi
echo "================================================================================"

echo ""
echo "Moving application files to installation destination"

echo "Copying installation files"
echo "Source: " $(pwd)/quickqueues/
echo "Dest  : " $DEST
mkdir -p $DEST
/bin/ln -sf $(pwd)/quickqueues/* $DEST/
/bin/ln -sf $(pwd)/VERSION $DEST/application/VERSION
sed -i 's|QQDEST|'$DEST'|g' 'bin/qqctl'
/bin/ln -sf $(pwd)/bin/qqctl /usr/local/bin/qqctl
echo $DEST > .install_dest	
chmod +x /usr/local/bin/qqctl

echo "================================================================================"

echo "Generating Cron job (this will overwrite any previous Quickqueues cron jobs schedules)"
/bin/rm -f /etc/cron.d/quickqueues
echo "* * * * * root php $DEST/index.php tools parse_queue_log" > /etc/cron.d/quickqueues

echo "================================================================================"

# QQ version File path
qq_version_file="/home/qq_version.sh"

# Check if the file exists
if [ -f "$qq_version_file" ]; then
    echo "$qq_version_file exists. Running the script..."
    # Run the script
    bash "$qq_version_file"
else
    echo "$qq_version_file does not exist."
fi

# Cron remove for queuelock deletion for new version
cron_create_file="/home/cron_create.sh"

# Check if the file exists
if [ -f "$cron_create_file" ]; then
    echo "$cron_create_file exists. Running the script..."
    # Run the script
    bash "$cron_create_file"
else
    echo "$cron_create_file does not exist."
fi

echo "====== INSTALLATION SUMMARY ===================================================="

echo "⠀⠀⠀⠀⢀⣀⣐⡙⠶⣿⣷⣌⡳⣿⡿⠿⢷⣦⣄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⣠⡴⠮⠁⠀⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠙⠟⢿⣦⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⢠⡾⠟⠁⠀⠀⠀⠀⠀⠀⠀⠀⣀⡀⠀⠀⠀⠀⠀⣀⡘⠿⣤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⣿⠃⠀⠀⠀⣀⠀⠀⠀⠀⠀⠀⠻⣅⠀⠀⠀⠀⠀⢈⣹⣄⣼⣧⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⣿⠓⠒⠂⠀⣿⣰⣠⡀⠀⠀⢰⣦⣾⠷⠶⢤⣾⡦⠾⠉⠉⠉⠙⠿⢶⣦⣤⣄⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⣿⣤⣤⣀⢀⣠⠄⠉⣻⡷⠾⠛⠁⠀⠀⠀⠀⠀⠝⢦⠀⠀⠀⠀⠀⠀⠀⠀⠀⣻⣇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⡄⢀⣀⣠⡴⢿⣤⣀⣀⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠻⣧⣀⣌⠁⢀⡶⠞⠁⠀⠀⠀⣀⣤⣤⣄⠀⠀⢀⡾⠀⠀⠀⠀⠀⣠⡾⠿⠛⠛⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⡼⠛⠋⠉⠀⠀⠀⠀⠀⠉⠉⠙⠳⣄⡀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⣠⣿⡿⠉⠉⠉⠀⠀⠀⠀⣰⣾⣿⣿⠾⠋⠀⠀⠈⢹⡷⠶⠶⠶⠿⠟⣷⠀⠀⠀⠀⠀⠀⠀⠀⣀⣠⣴⠟⠒⠛⠉⠛⠒⠦⣄⣀⡀⠀⠀⠀⠀⠀⠀⠀⠙⢦⡀⠀⠀"⠀
echo "⠀⠀⢀⣼⡿⠋⠀⠀⠀⠀⠀⠀⠀⠘⠉⠀⠀⠀⠀⠀⠀⠀⠀⣸⠇⠀⠀⠀⠀⠀⠹⣷⣶⠶⠶⠤⠶⣶⣟⡋⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠑⢦⠀⠀⠀⠀⠀⠀⠀⠀⢻⡄⠀⠀"
echo "⠀⣠⣾⡟⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣼⠏⠀⠀⠀⠀⠀⠀⠀⠙⣷⡄⠀⠀⠀⠀⠀⠙⣆⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⡄⠀"
echo "⣸⡿⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠛⣿⠟⠁⠀⠀⠀⠀⠀⠀⠀⠀⠰⣏⢿⡄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠹⣄"
echo "⢿⣇⠀⢀⣶⠆⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣠⡾⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣰⠋⠘⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠐⢺"
echo "⢸⣿⢿⡏⠀⠀⠀⣀⡤⠀⢀⣀⣀⣠⣤⡶⠶⠟⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣰⠟⠀⠾⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸"
echo "⠀⠙⢿⡟⠓⠛⠋⣁⣀⣴⣿⣿⣿⡉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠶⠋⠀⠀⣤⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀"
echo "⠀⠀⠀⠙⠻⠟⠛⠛⠉⠉⠀⠀⢹⣿⠦⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣰⠃⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⣆⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣰⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢰⡟⠛⠓⠦⢤⠆⠀⠀⠀⠀⠀⠀⠀⠀⢀⣠⠖⠋⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⡗⠢⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣠⠟⠃⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⢻⣶⢤⣤⣀⣀⡀⠀⢀⣴⠴⠚⠋⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⡼⡀⠀⠀⠀⠀⢸⡏"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠘⣿⡀⣈⠉⠉⠉⡟⠉⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢐⡏⢰⡇⠀⠀⠀⠀⣾⡇"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⢻⣿⠓⠶⣞⣁⡀⠀⢀⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣹⠀⠀⠀⠀⠀⠀⣰⠟⢐⡿⠃⠀⠀⠀⠀⣸⠃"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣧⠀⠀⠀⠀⠙⠛⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢠⡴⠋⠀⠀⠀⢠⣤⠞⠁⢀⡿⠃⠀⠀⣠⠟⣴⡟⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⡛⠲⢦⣄⣀⣠⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⠀⠀⣀⣀⢀⡴⠋⠀⠀⠀⠀⠀⠀⠀⣠⣴⣿⣁⣀⣠⣞⣡⣼⡿⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠘⣷⣄⣀⣈⡉⣁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣼⠀⠀⠉⠉⢹⡇⠀⠀⠀⠀⠀⣀⣰⣾⠛⣻⡟⠈⠉⠉⠉⢿⣿⠁⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⣌⣉⠉⠉⣡⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣠⠞⠁⠀⠀⢀⣴⡿⣤⣀⣴⠶⡴⠏⠉⢹⣿⠀⣿⡇⠀⠀⠀⠀⠈⣿⡄⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠹⣧⡉⠓⠆⠀⠙⣦⡀⠀⠀⣀⡀⠀⣀⣤⠴⠛⠁⠀⠀⠀⠀⢼⡏⠀⠀⠀⣿⡇⠀⠀⠀⢸⣿⠀⢿⣇⠀⠀⠀⠀⣰⣿⠃⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹⣷⠤⢤⣄⣀⣈⣙⣻⡿⠋⠉⠻⣿⣀⠀⠀⠀⢀⣀⣤⣶⠏⠀⠀⠀⠀⣿⡇⠀⠀⠀⢸⣿⠀⠘⣿⡄⠀⠀⠀⣿⡏⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠸⣇⠀⠀⠈⠉⠉⢿⣿⠃⠀⠀⠀⢻⣿⡟⠃⠀⠈⠉⢹⣿⠀⠀⠀⠀⢀⣿⠁⠀⠀⠀⠘⣿⠀⠀⢹⡇⠀⠀⠀⣿⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⡀⠀⠀⠀⠀⢸⣿⠀⠀⠀⠀⠈⣿⡇⠀⠀⠀⠀⢸⡟⠀⠀⠀⠀⢸⣿⠀⠀⠀⠀⢀⣿⡇⠀⢸⡇⠀⠀⠀⣿⡆⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢻⣇⠀⠀⠀⠀⣿⡏⠀⠀⠀⠀⠀⢻⣇⠀⠀⠀⠀⢸⣧⠀⠀⠀⠀⢸⣿⠀⢀⡀⠀⣾⣿⠁⠀⣼⡇⠀⠀⠀⠘⣿⡀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⠀⠀⠀⢀⣿⡇⠀⠀⠀⠀⠀⠀⣿⡀⠀⠀⠀⢀⣿⡄⠀⠀⠀⣿⣏⣀⣾⣀⣰⡿⠃⠀⣰⣿⠀⠀⠀⠀⠀⣼⡇⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⠃⠀⠀⢸⣿⡀⠀⠀⠀⠀⠀⠀⣿⡇⠀⠀⠀⣼⣿⠇⠀⠀⠀⠉⠉⠉⠉⠉⠉⠀⠀⠀⣿⠇⠀⢀⡀⠀⢰⣿⠁⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⡏⠀⠀⠀⠀⢿⣇⠀⠀⠀⠀⠀⠀⣿⡇⠀⠀⠀⣿⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢠⣿⠀⠀⣼⡇⢀⣿⡿⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⡟⠀⠀⠀⠀⢀⣾⣿⠂⠀⠀⠀⠀⠀⣿⡇⠀⠀⠀⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠻⠷⠿⠿⠟⠛⠋⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⠀⠀⠀⠀⠀⣼⣿⠁⠀⠀⠀⠀⠀⣠⣿⠀⠀⠀⠀⣿⣧⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⠇⢀⡆⠀⠀⣠⣿⡏⠀⠀⠀⠀⠀⣠⣿⠇⠀⠀⠀⠀⢹⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠿⠶⠛⠻⠿⠟⠛⠋⠀⠀⠀⠀⠀⢀⣿⠏⠀⠀⠀⠀⢀⣾⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢠⣾⠋⢀⡄⠀⠀⣰⣿⠛⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀"
echo "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢻⣧⣤⣾⣷⡤⠾⢟⢁⠀⠀"
