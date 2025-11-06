# Paymob Local Development Setup

## Problem

Paymob needs to redirect users back to your application after payment, but it cannot access `localhost` URLs. You need a publicly accessible URL.

## Solution: Use ngrok (Recommended for Local Development)

### Step 1: Install ngrok

**Windows:**
1. Download from [https://ngrok.com/download](https://ngrok.com/download)
2. Extract `ngrok.exe` to a folder (e.g., `C:\ngrok\`)
3. Add to PATH or use full path

**Mac:**
```bash
brew install ngrok
```

**Linux:**
```bash
# Download and install
wget https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz
tar -xvzf ngrok-v3-stable-linux-amd64.tgz
sudo mv ngrok /usr/local/bin
```

### Step 2: Start Your Laravel Server

```bash
php artisan serve
# Your app will be running on http://localhost:8000
```

### Step 3: Start ngrok

Open a new terminal and run:

```bash
ngrok http 8000
```

You'll get output like:
```
Forwarding  https://abc123def456.ngrok.io -> http://localhost:8000
```

### Step 4: Configure Paymob Return URL

1. Copy the ngrok HTTPS URL (e.g., `https://abc123def456.ngrok.io`)
2. Go to Paymob Dashboard → Settings → Integrations
3. Set Return/Callback URL to:
   ```
   https://abc123def456.ngrok.io/payment/return/paymob
   ```

### Step 5: Update Your .env (Optional)

If you want to use the ngrok URL in your code:

```env
APP_URL=https://abc123def456.ngrok.io
```

### Step 6: Test

1. Make a test payment
2. Paymob will redirect to your ngrok URL
3. Your local server will receive the request
4. Check `storage/logs/laravel.log` for debugging

## Important Notes

### ngrok URL Changes

- **Free ngrok**: URL changes every time you restart ngrok
- **Paid ngrok**: You can get a static URL
- **Solution**: Update Paymob return URL each time, or use ngrok's reserved domain (paid feature)

### ngrok Alternatives

1. **localtunnel**: `npx localtunnel --port 8000`
2. **serveo**: `ssh -R 80:localhost:8000 serveo.net`
3. **Cloudflare Tunnel**: Free static domain option

## Production Setup

For production, use your actual domain:

1. In Paymob Dashboard, set Return URL to:
   ```
   https://yourdomain.com/payment/return/paymob
   ```

2. Make sure your domain:
   - Has valid SSL certificate (HTTPS)
   - Is publicly accessible
   - Points to your production server

## Quick ngrok Setup Script

Create `start-ngrok.sh` (or `start-ngrok.bat` for Windows):

**Windows (start-ngrok.bat):**
```batch
@echo off
echo Starting ngrok...
ngrok http 8000
pause
```

**Linux/Mac (start-ngrok.sh):**
```bash
#!/bin/bash
echo "Starting ngrok..."
ngrok http 8000
```

## Troubleshooting

### ngrok not working?
- Check if port 8000 is being used by Laravel
- Try different port: `php artisan serve --port=8001` then `ngrok http 8001`
- Check firewall settings

### Paymob still showing error?
- Verify ngrok URL is accessible: Open `https://your-ngrok-url.ngrok.io` in browser
- Check Paymob dashboard return URL is correct
- Check Laravel logs for errors
- Make sure ngrok is running when testing

### URL changed?
- If ngrok URL changed, update it in Paymob dashboard
- Consider upgrading to ngrok paid plan for static URL

