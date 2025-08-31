# GitHub Setup Instructions for eForum

## Option 1: Using GitHub CLI (Recommended)

1. **Install GitHub CLI** (if not already installed):
   ```bash
   # On macOS with Homebrew
   brew install gh
   
   # Or download from: https://cli.github.com/
   ```

2. **Login to GitHub**:
   ```bash
   gh auth login
   ```
   - Choose "GitHub.com"
   - Choose "HTTPS"
   - Authenticate with your web browser

3. **Create the repository**:
   ```bash
   gh repo create eforum --public --source=. --remote=origin --push
   ```

## Option 2: Manual Setup

1. **Go to GitHub.com**
   - Login to your account
   - Click the "+" icon in top right
   - Select "New repository"

2. **Create Repository**:
   - Repository name: `eforum`
   - Description: "Nigerian Professional Community Platform - Forum for visa discussions, remote jobs, and networking"
   - Public or Private (your choice)
   - DO NOT initialize with README, .gitignore, or license

3. **Add Remote and Push**:
   ```bash
   # Replace YOUR_USERNAME with your GitHub username
   git remote add origin https://github.com/YOUR_USERNAME/eforum.git
   git push -u origin main
   ```

## Option 3: Using VS Code

1. **Install GitHub Extension**:
   - Open VS Code
   - Go to Extensions (Cmd+Shift+X)
   - Search for "GitHub Pull Requests and Issues"
   - Install it

2. **Sign in to GitHub**:
   - Press Cmd+Shift+P
   - Type "GitHub: Sign in"
   - Follow the authentication flow

3. **Publish to GitHub**:
   - Press Cmd+Shift+P
   - Type "Publish to GitHub"
   - Choose repository name and visibility

## After Setup

Once your repository is on GitHub, you can:

1. **Add a description and topics**:
   - Go to repository settings
   - Add description: "Nigerian Professional Community Platform"
   - Add topics: `laravel`, `php`, `forum`, `nigeria`, `visa`, `jobs`, `community`

2. **Set up branch protection** (optional):
   - Go to Settings → Branches
   - Add rule for `main` branch
   - Enable "Require pull request reviews"

3. **Add collaborators** (if needed):
   - Go to Settings → Manage access
   - Invite collaborators

## Quick Commands Reference

```bash
# Check remote
git remote -v

# Push changes
git push origin main

# Create a new branch
git checkout -b feature/new-feature

# Push new branch
git push -u origin feature/new-feature
```

---

**Note**: Your repository is ready to push. All files are committed and waiting to be pushed to GitHub.
