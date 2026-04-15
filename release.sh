#!/usr/bin/env bash
set -euo pipefail

# ─────────────────────────────────────────────────────────────
# Release script for kirby-languagerelease
#
# Usage:
#   ./release.sh patch    # 1.0.1 → 1.0.2
#   ./release.sh minor    # 1.0.1 → 1.1.0
#   ./release.sh major    # 1.0.1 → 2.0.0
# ─────────────────────────────────────────────────────────────

RELEASE_TYPE="${1:-}"
REPO_URL="https://github.com/nerdcel/kirby-languagerelease"

# ── Colors ────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

info()  { echo -e "${BLUE}ℹ${NC}  $1"; }
ok()    { echo -e "${GREEN}✔${NC}  $1"; }
warn()  { echo -e "${YELLOW}⚠${NC}  $1"; }
error() { echo -e "${RED}✖${NC}  $1"; exit 1; }

# ── Validate input ────────────────────────────────────────────
if [[ ! "$RELEASE_TYPE" =~ ^(major|minor|patch)$ ]]; then
  echo ""
  echo -e "  ${YELLOW}Usage:${NC} ./release.sh <major|minor|patch>"
  echo ""
  echo "  Examples:"
  echo "    ./release.sh patch   # Bugfix release"
  echo "    ./release.sh minor   # New feature release"
  echo "    ./release.sh major   # Breaking change release"
  echo ""
  exit 1
fi

# ── Pre-flight checks ────────────────────────────────────────
info "Running pre-flight checks..."

# Check we're on main branch
CURRENT_BRANCH=$(git branch --show-current)
if [[ "$CURRENT_BRANCH" != "main" ]]; then
  error "You must be on the 'main' branch to release (currently on '$CURRENT_BRANCH')"
fi

# Check for uncommitted changes
if [[ -n $(git status --porcelain) ]]; then
  error "Working directory is not clean. Commit or stash your changes first."
fi

# Check remote is up to date
git fetch origin --tags --quiet
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main 2>/dev/null || echo "")
if [[ -n "$REMOTE" && "$LOCAL" != "$REMOTE" ]]; then
  error "Local branch is not in sync with origin/main. Pull or push first."
fi

ok "Pre-flight checks passed"

# ── Determine versions ────────────────────────────────────────
# Get latest tag
LATEST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
CURRENT_VERSION="${LATEST_TAG#v}"

# Split version into parts
IFS='.' read -r MAJOR MINOR PATCH <<< "$CURRENT_VERSION"

# Calculate new version
case "$RELEASE_TYPE" in
  major) NEW_VERSION="$((MAJOR + 1)).0.0" ;;
  minor) NEW_VERSION="${MAJOR}.$((MINOR + 1)).0" ;;
  patch) NEW_VERSION="${MAJOR}.${MINOR}.$((PATCH + 1))" ;;
esac

NEW_TAG="v${NEW_VERSION}"
TODAY=$(date +%Y-%m-%d)

info "Current version: ${YELLOW}${CURRENT_VERSION}${NC}"
info "New version:     ${GREEN}${NEW_VERSION}${NC} (${RELEASE_TYPE})"

# ── Generate changelog from commits ──────────────────────────
info "Generating changelog from git commits..."

# Collect commits since last tag, grouped by type
BREAKING=""
FEATURES=""
FIXES=""
DOCS=""
TESTS=""
CHORES=""
OTHER=""

while IFS= read -r line; do
  # Skip empty lines
  [[ -z "$line" ]] && continue

  # Extract commit message (skip hash)
  MSG="${line#* }"

  # Check for breaking changes
  if [[ "$MSG" =~ ^.*!: ]] || [[ "$MSG" =~ BREAKING ]]; then
    BREAKING="${BREAKING}\n- ${MSG}"
  # Categorize by conventional commit prefix
  elif [[ "$MSG" =~ ^feat ]]; then
    # Strip prefix for cleaner output
    CLEAN="${MSG#feat: }"
    CLEAN="${CLEAN#feat\(*\): }"
    FEATURES="${FEATURES}\n- ${CLEAN}"
  elif [[ "$MSG" =~ ^fix ]]; then
    CLEAN="${MSG#fix: }"
    CLEAN="${CLEAN#fix\(*\): }"
    FIXES="${FIXES}\n- ${CLEAN}"
  elif [[ "$MSG" =~ ^docs ]]; then
    CLEAN="${MSG#docs: }"
    CLEAN="${CLEAN#docs\(*\): }"
    DOCS="${DOCS}\n- ${CLEAN}"
  elif [[ "$MSG" =~ ^test ]]; then
    CLEAN="${MSG#test: }"
    CLEAN="${CLEAN#test\(*\): }"
    TESTS="${TESTS}\n- ${CLEAN}"
  elif [[ "$MSG" =~ ^(chore|ci|build|style|refactor|perf) ]]; then
    # Strip any conventional commit prefix
    CLEAN=$(echo "$MSG" | sed -E 's/^(chore|ci|build|style|refactor|perf)(\([^)]*\))?: //')
    CHORES="${CHORES}\n- ${CLEAN}"
  else
    OTHER="${OTHER}\n- ${MSG}"
  fi
done <<< "$(git --no-pager log "${LATEST_TAG}..HEAD" --oneline 2>/dev/null)"

# Build the changelog entry
CHANGELOG_ENTRY="## [${NEW_VERSION}] - ${TODAY}"

if [[ -n "$BREAKING" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### ⚠ BREAKING CHANGES\n${BREAKING}"
fi
if [[ -n "$FEATURES" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Added\n${FEATURES}"
fi
if [[ -n "$FIXES" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Fixed\n${FIXES}"
fi
if [[ -n "$DOCS" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Documentation\n${DOCS}"
fi
if [[ -n "$TESTS" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Tests\n${TESTS}"
fi
if [[ -n "$CHORES" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Maintenance\n${CHORES}"
fi
if [[ -n "$OTHER" ]]; then
  CHANGELOG_ENTRY="${CHANGELOG_ENTRY}\n\n### Other\n${OTHER}"
fi

# ── Show preview ──────────────────────────────────────────────
echo ""
echo -e "${BLUE}━━━ Changelog Preview ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "$CHANGELOG_ENTRY"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# ── Confirm ───────────────────────────────────────────────────
read -rp "$(echo -e "${YELLOW}Release ${NEW_TAG}? [y/N]${NC} ")" CONFIRM
if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
  warn "Release aborted."
  exit 0
fi

# ── Update CHANGELOG.md ──────────────────────────────────────
info "Updating CHANGELOG.md..."

# Build the new link line
NEW_LINK="[${NEW_VERSION}]: ${REPO_URL}/compare/${LATEST_TAG}...${NEW_TAG}"
OLD_LINK_PATTERN="\[${CURRENT_VERSION}\]: "

# Create the new changelog content
{
  # Header (first 6 lines)
  head -6 CHANGELOG.md
  echo ""
  # New entry
  echo -e "$CHANGELOG_ENTRY"
  echo ""
  # Old entries (skip header)
  tail -n +7 CHANGELOG.md | head -n -2
  # Links section
  echo "${NEW_LINK}"
  # Keep existing links
  grep '^\[' CHANGELOG.md | head -20
  echo ""
} > CHANGELOG.md.tmp

mv CHANGELOG.md.tmp CHANGELOG.md

ok "CHANGELOG.md updated"

# ── Run tests ─────────────────────────────────────────────────
info "Running tests..."
if vendor/bin/phpunit --no-coverage 2>&1 | tail -1 | grep -q "^OK"; then
  ok "All tests passed"
else
  warn "Tests produced warnings (check output above)"
  read -rp "$(echo -e "${YELLOW}Continue anyway? [y/N]${NC} ")" CONTINUE
  if [[ "$CONTINUE" != "y" && "$CONTINUE" != "Y" ]]; then
    # Revert changelog
    git checkout CHANGELOG.md
    error "Release aborted due to test issues."
  fi
fi

# ── Commit, tag & push ───────────────────────────────────────
info "Committing and tagging..."

git add CHANGELOG.md
git commit -m "chore(release): ${NEW_VERSION}"
git tag -a "$NEW_TAG" -m "Release ${NEW_VERSION}"

ok "Created tag ${NEW_TAG}"

info "Pushing to origin..."
git push origin main
git push origin "$NEW_TAG"

ok "Pushed to origin"

# ── Done ──────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  ✔ Released ${NEW_TAG} successfully!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "  GitHub Actions will now:"
echo "  • Run tests"
echo "  • Build the ZIP archive"
echo "  • Create the GitHub Release"
echo "  • Packagist will pick up the new version automatically"
echo ""
echo -e "  ${BLUE}→${NC} ${REPO_URL}/releases/tag/${NEW_TAG}"
echo ""

