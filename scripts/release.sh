#!/bin/bash

# 🚀 Release Script for Dashboard App
# Usage: ./scripts/release.sh [version] [type]
# Example: ./scripts/release.sh 1.1.0 beta

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if version is provided
if [ -z "$1" ]; then
    print_error "Version number is required!"
    echo "Usage: ./scripts/release.sh [version] [type]"
    echo "Example: ./scripts/release.sh 1.1.0 beta"
    exit 1
fi

VERSION=$1
TYPE=${2:-""}

# Determine full version with pre-release suffix
if [ "$TYPE" == "alpha" ]; then
    FULL_VERSION="$VERSION-alpha.1"
elif [ "$TYPE" == "beta" ]; then
    FULL_VERSION="$VERSION-beta.1"
else
    FULL_VERSION="$VERSION"
fi

print_status "Starting release process for version v$FULL_VERSION"

# Check if we're on develop branch for pre-releases or main for stable
CURRENT_BRANCH=$(git branch --show-current)

if [ "$TYPE" == "" ] && [ "$CURRENT_BRANCH" != "main" ]; then
    print_error "Stable releases must be made from 'main' branch. Current: $CURRENT_BRANCH"
    exit 1
fi

if [ "$TYPE" != "" ] && [ "$CURRENT_BRANCH" != "develop" ] && [[ "$CURRENT_BRANCH" != feature/* ]]; then
    print_warning "Pre-releases are typically made from 'develop' or feature branches. Current: $CURRENT_BRANCH"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    print_error "There are uncommitted changes. Please commit or stash them first."
    exit 1
fi

# Update package.json version
print_status "Updating package.json version to $FULL_VERSION"
cd frontend
npm version $FULL_VERSION --no-git-tag-version
cd ..

# Commit version bump
print_status "Committing version bump"
git add frontend/package.json
git commit -m "chore: bump version to v$FULL_VERSION"

# Create git tag
print_status "Creating git tag v$FULL_VERSION"

if [ "$TYPE" == "alpha" ]; then
    TAG_MESSAGE="Alpha Release v$FULL_VERSION

🚧 Alpha Version - Development Preview
⚠️  Not recommended for production use

This is an early preview release containing:
- New features in development
- Potential bugs and issues
- Incomplete functionality
- Subject to breaking changes

For testing and feedback purposes only."

elif [ "$TYPE" == "beta" ]; then
    TAG_MESSAGE="Beta Release v$FULL_VERSION

🧪 Beta Version - Feature Complete

This beta release includes:
- All planned features for this version
- Ready for testing and feedback
- Minor bugs may still exist
- UI/UX refinements in progress

Suitable for staging/testing environments."

else
    TAG_MESSAGE="Stable Release v$FULL_VERSION

🎉 Production Ready Release

This stable release includes:
- All features fully tested
- Production-ready code
- Complete documentation
- Performance optimized

Ready for production deployment!"
fi

git tag -a "v$FULL_VERSION" -m "$TAG_MESSAGE"

# Push changes and tags
print_status "Pushing changes and tags to origin"
git push origin $CURRENT_BRANCH
git push origin --tags

# If this is a stable release from a release branch, merge to main
if [ "$TYPE" == "" ] && [[ "$CURRENT_BRANCH" == release/* ]]; then
    print_status "Merging release branch to main"
    git checkout main
    git merge $CURRENT_BRANCH --no-ff -m "Merge $CURRENT_BRANCH for v$FULL_VERSION"
    git push origin main
    
    print_status "Merging back to develop"
    git checkout develop
    git merge main --no-ff -m "Merge main back to develop after v$FULL_VERSION release"
    git push origin develop
    
    print_status "Cleaning up release branch"
    git branch -d $CURRENT_BRANCH
    git push origin --delete $CURRENT_BRANCH
fi

print_success "Release v$FULL_VERSION completed successfully! 🎉"
print_status "Tag created: v$FULL_VERSION"
print_status "Changes pushed to: $CURRENT_BRANCH"

# Show next steps
echo
print_status "Next steps:"
echo "1. Update CHANGELOG.md with release notes"
echo "2. Create GitHub release from tag v$FULL_VERSION"
echo "3. Deploy to appropriate environment"
echo "4. Announce the release to the team"

# Show recent tags
echo
print_status "Recent releases:"
git tag -l --sort=-version:refname | head -5
