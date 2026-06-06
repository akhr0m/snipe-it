#!/bin/bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}📦 Generating version config...${NC}"

# Fallbacks
DEFAULT_VERSION="v8.6.1-bast.1.0.0"
GIT_BRANCH="unknown"
GIT_HASH_SHORT="unknown"
BUILD_NUMBER="unknown"
APP_VERSION="$DEFAULT_VERSION"

if [ -d .git ] && command -v git >/dev/null 2>&1; then
    # Git is available, extract info
    GIT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
    GIT_HASH_SHORT=$(git rev-parse --short HEAD)
    BUILD_NUMBER=$(git rev-list --count HEAD 2>/dev/null || echo "0")
    
    # Try to find the latest custom tag
    LATEST_TAG=$(git describe --tags --match "v*-bast.*" --abbrev=0 2>/dev/null || echo "")
    if [ -n "$LATEST_TAG" ]; then
        # Check if there are commits since that tag
        COMMITS_SINCE=$(git rev-list --count "$LATEST_TAG..HEAD" 2>/dev/null || echo "0")
        if [ "$COMMITS_SINCE" -gt 0 ]; then
            # If there are commits since, append dev suffix
            APP_VERSION="$LATEST_TAG-dev.$COMMITS_SINCE"
        else
            APP_VERSION="$LATEST_TAG"
        fi
    else
        APP_VERSION="$DEFAULT_VERSION"
    fi
else
    echo -e "${YELLOW}⚠️ Git repository or CLI not detected, using defaults.${NC}"
    # Read from existing config if present
    if [ -f config/version.php ]; then
        APP_VERSION=$(php -r "\$c = include('config/version.php'); echo \$c['app_version'] ?? '$DEFAULT_VERSION';")
        GIT_BRANCH=$(php -r "\$c = include('config/version.php'); echo \$c['branch'] ?? 'unknown';")
        GIT_HASH_SHORT=$(php -r "\$c = include('config/version.php'); echo \$c['hash_version'] ?? 'unknown';")
        BUILD_NUMBER=$(php -r "\$c = include('config/version.php'); echo \$c['build_version'] ?? 'unknown';")
    fi
fi

FULL_APP_VERSION="$APP_VERSION - build $BUILD_NUMBER-$GIT_HASH_SHORT"
FULL_HASH="$APP_VERSION-$GIT_HASH_SHORT"

cat > config/version.php << PHPEOF
<?php
return array (
  'app_version' => '$APP_VERSION',
  'full_app_version' => '$FULL_APP_VERSION',
  'build_version' => '$BUILD_NUMBER',
  'prerelease_version' => '',
  'hash_version' => '$GIT_HASH_SHORT',
  'full_hash' => '$FULL_HASH',
  'branch' => '$GIT_BRANCH',
);
PHPEOF

chmod 644 config/version.php

echo -e "${GREEN}✓ Version file generated successfully!${NC}"
echo ""
echo "   App Version: $APP_VERSION"
echo "   Build:       $BUILD_NUMBER"
echo "   Hash:        $GIT_HASH_SHORT"
echo "   Branch:      $GIT_BRANCH"
echo ""
