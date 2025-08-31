#!/bin/bash

# GitHub Push Script for eForum
# Replace YOUR_GITHUB_USERNAME with your actual GitHub username

echo "Setting up GitHub remote..."

# Replace this with your GitHub username
GITHUB_USERNAME="YOUR_GITHUB_USERNAME"

# Add remote origin
git remote add origin "https://github.com/${GITHUB_USERNAME}/eforum.git"

echo "Remote added. Pushing to GitHub..."

# Push to GitHub
git push -u origin main

echo "Push complete!"
echo ""
echo "Your repository should now be available at:"
echo "https://github.com/${GITHUB_USERNAME}/eforum"
echo ""
echo "Next steps:"
echo "1. Go to your repository on GitHub"
echo "2. Add a description and topics"
echo "3. Update the About section"
echo "4. Consider adding branch protection rules"
