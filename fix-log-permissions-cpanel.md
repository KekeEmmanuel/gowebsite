# Fix Laravel Log File Permissions via cPanel

Since the log file is not writable, fix it via cPanel File Manager:

## Steps:

1. **Log into cPanel**
2. **Go to File Manager**
3. **Navigate to:** `laravel/storage/logs/`
4. **Right-click on `laravel.log`** → **Change Permissions**
5. **Set permissions to:** `664` (rw-rw-r--)
   - Owner: Read + Write
   - Group: Read + Write  
   - Public: Read
6. **Click "Change Permissions"**

## Also fix the logs directory:

1. **Right-click on `logs` directory** → **Change Permissions**
2. **Set permissions to:** `755` (rwxr-xr-x)
   - Owner: Read + Write + Execute
   - Group: Read + Execute
   - Public: Read + Execute
3. **Click "Change Permissions"**

## Alternative: Fix entire storage directory

1. Navigate to `laravel/storage/`
2. Right-click → **Change Permissions**
3. Set to `755` and check **"Recurse into subdirectories"**
4. Click "Change Permissions"

After fixing permissions, Laravel will be able to write new log entries.
