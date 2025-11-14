# CI/CD Deployment Guide for Contabo Server

This guide explains how to set up automatic deployment to your Contabo server when pushing to the `master` branch.

## Prerequisites

1. **GitHub Repository**: Your code should be in a GitHub repository
2. **Contabo Server Access**: SSH access to your Contabo server
3. **Git on Server**: Git must be installed on your Contabo server
4. **PHP & Composer**: PHP 8.2+ and Composer installed on the server
5. **Node.js & NPM**: Node.js and NPM installed on the server

## Setup Instructions

### Step 1: Prepare Your Server

1. **Clone your repository** on the Contabo server (if not already done):
   ```bash
   cd /var/www  # or your preferred directory
   git clone https://github.com/your-username/your-repo.git medical-platform
   cd medical-platform
   ```

2. **Set up your `.env` file** on the server with production settings:
   ```bash
   cp .env.example .env
   nano .env  # Edit with your production settings
   php artisan key:generate
   ```

3. **Set up proper permissions**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### Step 2: Generate SSH Key Pair

1. **On your local machine**, generate an SSH key pair (if you don't have one):
   ```bash
   ssh-keygen -t ed25519 -C "github-actions-deploy"
   ```

2. **Copy the public key** to your Contabo server:
   ```bash
   ssh-copy-id -i ~/.ssh/id_ed25519.pub your-username@your-server-ip
   ```

   Or manually add it to `~/.ssh/authorized_keys` on the server.

3. **Copy the private key** content (you'll need this for GitHub Secrets):
   ```bash
   cat ~/.ssh/id_ed25519
   ```

### Step 3: Configure GitHub Secrets

1. Go to your GitHub repository
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret** and add the following secrets:

   | Secret Name | Description | Example |
   |------------|-------------|---------|
   | `SSH_HOST` | Your Contabo server IP or domain | `123.45.67.89` or `yourdomain.com` |
   | `SSH_USER` | SSH username for your server | `root` or `ubuntu` |
   | `SSH_PRIVATE_KEY` | Your private SSH key (entire content) | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
   | `DEPLOY_PATH` | Full path to your project on the server | `/var/www/medical-platform` |

### Step 4: Configure Git on Server (if needed)

If your server repository uses HTTPS, you may need to set up SSH for Git:

```bash
# On the server
cd /var/www/medical-platform
git remote set-url origin git@github.com:your-username/your-repo.git
```

Or if you prefer to keep HTTPS, you can use a deploy key or personal access token.

### Step 5: Test the Deployment

1. **Make a small change** to your code
2. **Commit and push** to the `master` branch:
   ```bash
   git add .
   git commit -m "Test deployment"
   git push origin master
   ```

3. **Check GitHub Actions**:
   - Go to your repository on GitHub
   - Click on the **Actions** tab
   - You should see the deployment workflow running
   - Check the logs if there are any errors

## Manual Deployment

If you need to deploy manually on the server, you can use the provided `deploy.sh` script:

```bash
# Make the script executable
chmod +x deploy.sh

# Run the deployment
./deploy.sh
```

## Troubleshooting

### SSH Connection Issues

- **Test SSH connection** from your local machine:
  ```bash
  ssh your-username@your-server-ip
  ```

- **Check SSH key permissions** on the server:
  ```bash
  chmod 700 ~/.ssh
  chmod 600 ~/.ssh/authorized_keys
  ```

### Permission Issues

If you encounter permission errors:

```bash
# On the server
sudo chown -R www-data:www-data /var/www/medical-platform
sudo chmod -R 755 /var/www/medical-platform
sudo chmod -R 775 /var/www/medical-platform/storage
sudo chmod -R 775 /var/www/medical-platform/bootstrap/cache
```

### PHP-FPM Not Reloading

The script tries to reload PHP-FPM automatically. If it fails, you may need to:

1. **Check your PHP version**:
   ```bash
   php -v
   ```

2. **Manually reload PHP-FPM**:
   ```bash
   sudo systemctl reload php8.2-fpm
   # or
   sudo systemctl reload php-fpm
   ```

3. **Or restart your web server**:
   ```bash
   sudo systemctl restart nginx
   # or
   sudo systemctl restart apache2
   ```

### Queue Workers

If you're using queue workers with Supervisor, make sure they're configured. The script runs `php artisan queue:restart` which will gracefully restart workers.

### Database Migrations

The deployment script runs migrations with `--force` flag. Make sure your database is properly configured in the `.env` file on the server.

## Customization

You can customize the deployment workflow by editing `.github/workflows/deploy.yml`. Common customizations:

- **Different branch**: Change `master` to your preferred branch
- **Skip migrations**: Remove the migration step if you handle them separately
- **Additional commands**: Add any custom commands you need before or after deployment
- **Notifications**: Add Slack, Discord, or email notifications

## Security Best Practices

1. **Never commit** `.env` files or sensitive data
2. **Use strong SSH keys** (ed25519 recommended)
3. **Limit SSH access** to specific IPs if possible
4. **Use deploy keys** instead of personal SSH keys when possible
5. **Review GitHub Actions logs** regularly for any issues
6. **Keep dependencies updated** for security patches

## Support

If you encounter issues:
1. Check the GitHub Actions logs for detailed error messages
2. Verify all secrets are correctly set in GitHub
3. Test SSH connection manually
4. Check server logs: `tail -f storage/logs/laravel.log`














