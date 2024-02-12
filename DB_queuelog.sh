#!/bin/bash

CONFIG_FILE="/etc/asterisk/extconfig_custom.conf"
SETTINGS="[settings]"
QUEUE_LOG="queue_log => odbc,asteriskcdrdb,queuelog"

# Function to add queue_log line under [settings]
add_queue_log() {
    if ! grep -qF "$QUEUE_LOG" "$CONFIG_FILE"; then
        awk -v s="$SETTINGS" -v ql="$QUEUE_LOG" '/^\[settings\]/{print;print ql;next}1' "$CONFIG_FILE" > temp && mv temp "$CONFIG_FILE"
        echo "Added $QUEUE_LOG under $SETTINGS in $CONFIG_FILE"
    fi
}

# Function to remove queue_log line
remove_queue_log() {
    # Remove the queue_log line if it exists
    grep -vF "$QUEUE_LOG" "$CONFIG_FILE" > temp && mv temp "$CONFIG_FILE"
    echo "Removed $QUEUE_LOG from $CONFIG_FILE"
    # Optionally, remove [settings] if it's now empty (no other lines)
    if ! grep -qF -v "^\[settings\]$" "$CONFIG_FILE"; then
        grep -vF "$SETTINGS" "$CONFIG_FILE" > temp && mv temp "$CONFIG_FILE"
        echo "Removed empty $SETTINGS from $CONFIG_FILE"
    fi
}

# Main operation based on command line argument
case "$1" in
    install)
        if grep -qF "$SETTINGS" "$CONFIG_FILE"; then
            add_queue_log
        else
            echo "$SETTINGS" >> "$CONFIG_FILE"
            echo "$QUEUE_LOG" >> "$CONFIG_FILE"
            echo "Added $SETTINGS and $QUEUE_LOG to $CONFIG_FILE"
        fi
        asterisk -rx "core reload"
        asterisk -rx "module reload logger"
        echo "Asterisk logger module reloaded."
        ;;
    remove)
        remove_queue_log
        asterisk -rx "core reload"
        asterisk -rx "module reload logger"
        echo "Asterisk logger module reloaded."
        ;;
    *)
        echo "Usage: $0 {install|remove}"
        ;;
esac
