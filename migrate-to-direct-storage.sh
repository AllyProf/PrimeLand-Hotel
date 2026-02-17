#!/bin/bash
# Script to convert symlinked storage to direct folder storage (The "Nuclear Option")

cd /home/primkgek/public_html

echo "Starting storage migration..."

# 1. Check if public/storage is a symlink
if [ -L "public/storage" ]; then
    echo "Found symlink at public/storage. Removing..."
    rm public/storage
elif [ -d "public/storage" ]; then
    echo "Found directory at public/storage. Merging content..."
else
    echo "Creating public/storage directory..."
    mkdir -p public/storage
fi

# 2. Check source storage (../storage)
if [ -d "storage" ]; then
    echo "Moving content from ../storage to public/storage..."
    # Move content (cp -r then rm or mv)
    # Using cp -r to be safe, then we'll rename the old folder as backup
    cp -r storage/* public/storage/
    
    echo "Backing up old storage folder..."
    mv storage storage_backup_$(date +%Y%m%d)
else
    echo "Standard storage folder not found or already moved."
fi

# 3. Set permissions
echo "Setting permissions..."
chmod -R 755 public/storage

# 4. Clear config cache
echo "Clearing config cache..."
php artisan config:clear

echo "✅ MIGRATION COMPLETE!"
echo "Your uploads are now served directly from public/storage."
