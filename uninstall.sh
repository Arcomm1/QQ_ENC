#!/bin/bash


# Quickqueues uninstall script


if [[ $EUID -ne 0 ]]; then
    echo "Uninstaller must be run as root"
    exit
fi


if [ ! -f '/etc/amportal.conf' ]; then
    echo "FreePBX installation not fount, exitting"
    exit
fi


echo ""
echo "Removing installation files"
if [ -f .install_dest ];  then
    rm -rf $(cat .install_dest)
    rm -rf /usr/local/bin/qqctl
else
    echo "Istallation destination can not be found. Please locate and remove Quickqueues manually."
fi


echo ""
echo "Removing database"
AMPDBUSER=`cat /etc/freepbx.conf | grep AMPDBUSER | tr '=' ' ' | tr -d '"' | tr -d "'" | tr -d ';' | gawk '{print $2}'`
AMPDBPASS=`cat /etc/freepbx.conf | grep AMPDBPASS | tr '=' ' ' | tr -d '"' | tr -d "'" | tr -d ';' | gawk '{print $2}'`

mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_agent_last_call"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_agent_settings"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_agents"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_broadcast_notifications"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_calls"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_config"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_contacts"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_event_types"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_events"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_future_events"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_migrations"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_queue_agents"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_queue_config"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_queues"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_reset_password_tmp"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_sessions"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e 'DROP TABLE qq_call_subjects_parent'
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e 'DROP TABLE qq_call_subjects_child_1'
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e 'DROP TABLE qq_call_subjects_child_2'
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e 'DROP TABLE qq_call_subjects_child_3'
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_user_agents"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_user_logs"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_user_queues"
mysql -u$AMPDBUSER -p$AMPDBPASS asterisk -e "DROP TABLE qq_users"

rm -f /etc/cron.d/quickqueues
rm -f /var/log/asterisk/quickqueues_log
rm -f /var/run/quickqueues.lock
