# 🏷️ Versioning Strategy & Git Workflow

## Semantic Versioning (SemVer)

We follow [Semantic Versioning](https://semver.org/) format: `MAJOR.MINOR.PATCH[-PRERELEASE]`

### Version Format Examples:
- `v1.0.0` - Stable release
- `v1.0.0-beta.1` - Beta pre-release
- `v1.0.0-alpha.1` - Alpha pre-release
- `v1.0.1` - Patch release
- `v1.1.0` - Minor feature release
- `v2.0.0` - Major breaking changes

## 🌳 Git Branching Strategy (Git Flow)

### Main Branches:
- **`main`** - Production-ready code, stable releases
- **`develop`** - Integration branch for features, pre-production

### Supporting Branches:
- **`feature/*`** - New features (`feature/user-management`, `feature/dashboard`)
- **`release/*`** - Release preparation (`release/v1.1.0`)
- **`hotfix/*`** - Critical production fixes (`hotfix/security-patch`)
- **`bugfix/*`** - Bug fixes for develop (`bugfix/roles-display`)

### Branch Flow:
```
main ←─── release/v1.1.0 ←─── develop ←─── feature/new-feature
  ↑                                ↑
  └─── hotfix/critical-fix ────────┘
```

## 📋 Development Phases & Versions

### Phase 1: Foundation (v0.1.0-alpha.1)
- ✅ Laravel backend setup
- ✅ Vue.js + Quasar frontend setup
- ✅ Basic authentication

### Phase 2: Authentication (v0.2.0-alpha.1)
- ✅ Login/Register pages
- ✅ JWT integration
- ✅ Route protection

### Phase 3: Core Backend (v0.3.0-beta.1)
- ✅ User CRUD API
- ✅ Role & Permission system
- ✅ Database design

### Phase 4: User Management (v1.0.0-beta.1) ✅ CURRENT
- ✅ User Management interface
- ✅ Role Management interface
- ✅ Complete CRUD operations
- ✅ Theme toggle

### Phase 5: UI/UX Enhancements (v1.1.0-beta.1) 🚧 NEXT
- 🔄 Language switcher
- 🔄 Advanced search & filters
- 🔄 Export/Import functionality
- 🔄 Activity logging

### Phase 6: Analytics & Reports (v1.2.0-beta.1)
- 🔄 Dashboard analytics
- 🔄 User activity reports
- 🔄 System metrics

### Phase 7: Production Ready (v1.0.0)
- 🔄 Performance optimization
- 🔄 Security hardening
- 🔄 Production deployment

## 🏗️ Release Workflow

### 1. Feature Development
```bash
# Create feature branch from develop
git checkout develop
git pull origin develop
git checkout -b feature/language-switcher

# Work on feature...
git add .
git commit -m "feat: add language switcher component"

# Push feature branch
git push origin feature/language-switcher
```

### 2. Release Preparation
```bash
# Create release branch from develop
git checkout develop
git checkout -b release/v1.1.0

# Update version numbers, documentation
git commit -m "chore: prepare release v1.1.0"

# Create release tag
git tag -a v1.1.0-beta.1 -m "Release v1.1.0 Beta 1"
```

### 3. Production Release
```bash
# Merge to main
git checkout main
git merge release/v1.1.0
git tag -a v1.1.0 -m "Release v1.1.0: UI/UX Enhancements"
git push origin main --tags

# Merge back to develop
git checkout develop
git merge main
```

## 📝 Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

### Format: `type(scope): description`

#### Types:
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style (formatting, missing semicolons)
- **refactor**: Code refactoring
- **perf**: Performance improvements
- **test**: Adding tests
- **chore**: Build process, dependency updates

#### Examples:
```bash
feat(auth): add JWT token refresh mechanism
fix(users): resolve roles display issue in table
docs(api): update authentication endpoints
style(frontend): improve responsive design
refactor(backend): optimize database queries
perf(frontend): lazy load user management components
test(users): add unit tests for user controller
chore(deps): update Laravel to v12.x
```

## 🎯 Current Status

**Current Version:** `v1.0.0-beta.1`
**Current Branch:** `feature/phase5-ui-enhancements`
**Next Release:** `v1.1.0-beta.1`

### Active Branches:
- `main` - Production ready (v1.0.0-beta.1)
- `develop` - Integration branch
- `feature/phase5-ui-enhancements` - Current development

## 🚀 Quick Commands

### Create New Feature
```bash
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name
```

### Tag New Version
```bash
git tag -a v1.1.0-beta.1 -m "Release description"
git push origin --tags
```

### View Version History
```bash
git tag -l
git log --oneline --graph --decorate
```
