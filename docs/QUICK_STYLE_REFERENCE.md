# Quick Style Reference

## 🚀 Quick Start

### Basic Page Structure

```vue
<template>
  <q-page class="container padded">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">Page Title</h4>
          <p class="page-subtitle">Page description</p>
        </div>
        <div class="header-actions">
          <q-btn color="primary" label="Action" class="action-btn" />
        </div>
      </div>
    </div>

    <!-- Content -->
    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="icon" />
          Section Title
        </div>
      </q-card-section>
      <q-card-section>
        <!-- Content here -->
      </q-card-section>
    </q-card>
  </q-page>
</template>
```

## 📋 Common Classes

### Containers

- `.container` - Main page container
- `.container.padded` - With responsive padding
- `.content-wrapper` - Content width limiter

### Cards

- `.content-card` - Main content card with header
- `.simple-card` - Simple bordered card

### Forms

- `.standard-form` - Standard form layout
- `.form-row` - Form row with responsive columns
- `.form-group` - Form field container
- `.form-actions` - Button container

### Buttons

- `.action-btn` - Standard action button
- `.button-group` - Button group container

### Filters

- `.filter-section` - Filter container
- `.filter-card` - Filter card
- `.filter-row` - Filter row layout

### Tables

- `.enhanced-table` - Enhanced table styling

### Tabs

- `.modern-tabs` - Modern tab system

## 🎨 Utilities

### Spacing

- `.spacing-xs/sm/md/lg/xl` - Margin utilities
- `.gap-xs/sm/md/lg/xl` - Gap utilities

### Shadows

- `.shadow-sm/md/lg/xl` - Shadow utilities

### Animations

- `.hover-lift` - Lift on hover
- `.hover-scale` - Scale on hover

## 🛠️ Generate New Page

```bash
npm run generate:page PageName
```

Choose from:

1. **Basic** - List/table page
2. **Form** - Create/edit page
3. **Tabs** - Tab-based page

## 📱 Responsive

- All global classes are mobile-first responsive
- Use `.hide-mobile`, `.show-mobile` for visibility
- Forms automatically stack on mobile

## 🌙 Dark Mode

All global classes support dark mode automatically.

## ✅ Migration Checklist

- [ ] Replace custom containers with `.container.padded`
- [ ] Update headers with `.page-header` structure
- [ ] Convert cards to `.content-card`
- [ ] Update forms with `.standard-form`
- [ ] Remove duplicate custom styles
- [ ] Test dark mode

---

📚 **Full Documentation**: [GLOBAL_STYLES.md](./GLOBAL_STYLES.md)
