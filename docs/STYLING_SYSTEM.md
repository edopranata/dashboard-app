# 🎨 Global Styles System

A comprehensive, consistent styling system for the Dashboard application with automatic dark mode support and responsive design.

## ✨ Features

- **🎯 Consistent Design** - Global classes ensure visual consistency
- **📱 Responsive First** - Mobile-first approach with automatic breakpoints
- **🌙 Dark Mode Ready** - All components support dark theme automatically
- **⚡ Developer Friendly** - Easy to use classes and comprehensive documentation
- **🚀 Code Generation** - Automated page templates with best practices
- **🎨 Modern UI** - Beautiful gradients, shadows, and animations

## 🚀 Quick Start

### 1. Basic Page Template

```vue
<template>
  <q-page class="container padded">
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t("page.title") }}</h4>
          <p class="page-subtitle">{{ $t("page.description") }}</p>
        </div>
        <div class="header-actions">
          <q-btn color="primary" label="Action" class="action-btn" />
        </div>
      </div>
    </div>

    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="list" />
          Content Title
        </div>
      </q-card-section>
      <q-card-section>
        <!-- Your content -->
      </q-card-section>
    </q-card>
  </q-page>
</template>
```

### 2. Generate New Page

```bash
npm run generate:page YourPageName
```

Choose from 3 templates:

- **Basic** - List/table pages
- **Form** - Create/edit forms
- **Tabs** - Tab-based layouts

## 📚 Documentation

- **[📖 Complete Guide](./docs/GLOBAL_STYLES.md)** - Full documentation with examples
- **[⚡ Quick Reference](./docs/QUICK_STYLE_REFERENCE.md)** - Essential classes and patterns
- **[🎯 Migration Guide](./docs/GLOBAL_STYLES.md#migration-guide)** - Convert existing pages

## 🎨 Core Classes

### Containers

- `.container` - Main page wrapper with gradient background
- `.container.padded` - Add responsive padding
- `.content-wrapper` - Limit content width

### Layout Components

- `.page-header` - Standard page header
- `.content-card` - Main content cards
- `.standard-form` - Consistent form layouts
- `.modern-tabs` - Tab navigation system

### Utilities

- `.action-btn` - Standard buttons
- `.text-gradient` - Gradient text effect
- `.shadow-md` - Medium shadow
- `.hover-lift` - Hover animations

## 🌙 Dark Mode

All global classes automatically support dark mode. No additional configuration needed!

```vue
<!-- Automatically adapts to dark theme -->
<div class="content-card">
  <h3 class="text-gradient">Auto Dark Mode</h3>
</div>
```

## 📱 Responsive Design

Mobile-first responsive design with automatic breakpoints:

- **Mobile**: < 600px
- **Tablet**: 600px - 1023px
- **Desktop**: > 1024px

```vue
<!-- Automatically responsive -->
<div class="form-row">
  <div class="form-group"><!-- Stacks on mobile --></div>
  <div class="form-group"><!-- Side by side on desktop --></div>
</div>
```

## 🛠️ Migration from Custom Styles

### Before (Custom CSS)

```vue
<q-page class="user-list-page">
  <div class="custom-header">
    <h4>Title</h4>
  </div>
</q-page>

<style>
.user-list-page {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}
.custom-header h4 {
  font-weight: 600;
  color: #2d3748;
}
</style>
```

### After (Global Classes)

```vue
<q-page class="container padded">
  <div class="page-header">
    <div class="header-content">
      <div class="header-info">
        <h4 class="page-title">Title</h4>
      </div>
    </div>
  </div>
</q-page>

<!-- No custom styles needed! -->
```

## 🎯 Benefits

- **⚡ Faster Development** - No need to write custom CSS
- **🎨 Consistent UI** - Same look and feel across all pages
- **📱 Responsive Ready** - Works perfectly on all devices
- **🌙 Dark Mode** - Automatic theme switching
- **🔧 Maintainable** - Centralized styling system
- **🚀 Performance** - Optimized CSS with no duplication

## 📋 Available Templates

### 1. Basic Template

Perfect for list/table pages with filters and actions.

- Page header with title and actions
- Filter section
- Content card with data display

### 2. Form Template

Ideal for create/edit forms with validation.

- Standard form layout
- Input field styling
- Action buttons
- Responsive design

### 3. Tabs Template

Great for complex pages with multiple sections.

- Gradient header
- Modern tab navigation
- Content panels
- Profile-style layout

## 🔧 Customization

While global styles handle 95% of use cases, you can still add custom styles when needed:

```vue
<style lang="scss" scoped>
// Custom styles specific to this component
// Global styles are automatically available

.custom-feature {
  // Your custom styling
  @extend .content-card; // Extend global classes
}
</style>
```

## 📊 Implementation Status

### ✅ Completed

- [x] Global styles system
- [x] Page templates
- [x] Dark mode support
- [x] Responsive design
- [x] Code generator
- [x] Documentation
- [x] Migration examples

### 🔄 Updated Pages

- [x] ProfilePage - Modern tab-based design
- [x] UserListPage - Enhanced table with avatars
- [ ] DashboardPage - Pending migration
- [ ] RolesPage - Pending migration
- [ ] SettingsPage - Pending migration

## 🎯 Best Practices

1. **Always use global classes first** before custom CSS
2. **Test in both light and dark modes** during development
3. **Verify responsive behavior** on mobile devices
4. **Follow naming conventions** for consistency
5. **Document custom styles** when necessary

## 🆘 Support

- Check [Quick Reference](./docs/QUICK_STYLE_REFERENCE.md) for common patterns
- Review [example implementations](./src/pages/) in existing pages
- Generate new pages with `npm run generate:page` for templates

---

## 🚀 Getting Started

1. **Create a new page**:

   ```bash
   npm run generate:page MyNewPage
   ```

2. **Choose template type** when prompted

3. **Add route** to `src/router/routes.js`

4. **Add translations** to i18n files

5. **Implement logic** and test responsive design

**Ready to build consistent, beautiful interfaces! 🎨**
