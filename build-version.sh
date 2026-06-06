#!/bin/bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}í³¦ Generating version config...${NC}"

# Get git info
GIT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
GIT_HASH_SHORT=$(git rev-parse --short HEAD)
GIT_HASH_FULL=$(git rev-parse HEAD)
BUILD_TIME=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
BUILD_NUMBER=$(date +%s)

# Version constants
SNIPE_VERSION="8.6.1"
CUSTOM_VERSION="1.0.0"
CUSTOM_NAMESPACE="bast"

# Generate the PHP version config
cat > config/version.php << 'PHPEOF'
<?php
return array (
  'app_version' => 'v8.6.1-bast.1.0.0',
  'full_app_version' => 'v8.6.1-bast.1.0.0 - build 23104-353be1a32',
  'build_version' => '23104',
  'prerelease_version' => '',
  'hash_version' => '353be1a32',
  'full_hash' => 'v8.6.1-bast.1.0.0-353be1a32',
  'branch' => 'feature/custom-bast-module',
);
PHPEOF

chmod 644 config/version.php

echo -e "${GREEN}âœ“ Version file generated${NC}"
echo ""
echo "   App Version: v${SNIPE_VERSION}-${CUSTOM_NAMESPACE}.${CUSTOM_VERSION}"
echo "   Build: ${BUILD_NUMBER}"
echo "   Hash: ${GIT_HASH_SHORT}"
echo "   Branch: ${GIT_BRANCH}"
echo "   Time: ${BUILD_TIME}"
echo ""

