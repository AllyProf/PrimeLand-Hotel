#!/bin/bash

# Fix Laravel Storage Symlink on cPanel
# Run this script on your cPanel server via SSH or Terminal

cd /home/primkgek/public_html

# Remove old symlink if it exists
if [ -L "storage" ]; then
    echo "Removing old storage symlink..."
    rm storage
fi

# Create new symlink pointing to the correct location
echo "Creating storage symlink..."
ln -s /home/primkgek/public_html/storage/app/public /home/primkgek/public_html/public/storage

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 /home/primkgek/public_html/storage
chmod -R 755 /home/primkgek/public_html/public/storage

echo "✅ Storage symlink created successfully!"
echo "Images should now be visible at: https://yourdomain.com/storage/rooms/..."
