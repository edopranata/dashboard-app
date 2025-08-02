# Dashboard Global Styles Documentation

## Overview

Dokumentasi ini menjelaskan penggunaan global styles dan class utilities yang tersedia untuk memastikan konsistensi design di seluruh aplikasi Dashboard.

## Table of Contents

- [Container Classes](#container-classes)
- [Header Classes](#header-classes)
- [Card Classes](#card-classes)
- [Button Classes](#button-classes)
- [Form Classes](#form-classes)
- [Tab Classes](#tab-classes)
- [Table Classes](#table-classes)
- [Filter Classes](#filter-classes)
- [Utility Classes](#utility-classes)
- [Page Examples](#page-examples)
- [Best Practices](#best-practices)

---

## Container Classes

### `.container`

Class utama untuk semua `q-page`. Menyediakan layout dasar dengan gradient background.

```vue
<template>
  <q-page class="container">
    <!-- Content here -->
  </q-page>
</template>
```

**Variants:**

- `.container.flat` - Background tanpa gradient (flat color)
- `.container.padded` - Dengan padding responsive

**Example:**

```vue
<q-page class="container padded">
  <div class="content-wrapper">
    <!-- Your content -->
  </div>
</q-page>
```

### `.content-wrapper`

Wrapper untuk membatasi lebar maksimum content dan memberikan padding responsive.

---

## Header Classes

### `.page-header`

Header standar untuk semua halaman dengan title dan actions.

```vue
<div class="page-header">
  <div class="header-content">
    <div class="header-info">
      <h4 class="page-title">{{ $t('page.title') }}</h4>
      <p class="page-subtitle">{{ $t('page.subtitle') }}</p>
    </div>
    <div class="header-actions">
      <q-btn color="primary" icon="add" :label="$t('actions.create')" />
    </div>
  </div>
</div>
```

### `.gradient-header`

Header dengan gradient background (seperti profile page).

```vue
<div class="gradient-header">
  <div class="header-background">
    <div class="header-overlay"></div>
  </div>
  <div class="header-content">
    <div class="header-card">
      <!-- Header content -->
    </div>
  </div>
</div>
```

---

## Card Classes

### `.content-card`

Card utama untuk konten dengan header section.

```vue
<q-card flat bordered class="content-card">
  <q-card-section class="card-header">
    <div class="section-title">
      <q-icon name="person" />
      {{ $t('section.title') }}
    </div>
    <p class="section-subtitle">{{ $t('section.description') }}</p>
  </q-card-section>
  
  <q-card-section>
    <!-- Content -->
  </q-card-section>
</q-card>
```

### `.simple-card`

Card sederhana dengan border dan shadow.

```vue
<q-card flat class="simple-card">
  <!-- Content -->
</q-card>
```

---

## Button Classes

### `.action-btn`

Styling konsisten untuk action buttons.

```vue
<q-btn color="primary" :label="$t('actions.save')" class="action-btn" />
```

### `.button-group`

Container untuk group buttons dengan spacing yang konsisten.

```vue
<div class="button-group">
  <q-btn color="primary" label="Save" class="action-btn" />
  <q-btn color="grey-7" label="Cancel" class="action-btn" flat />
</div>
```

---

## Form Classes

### `.standard-form`

Layout standar untuk form dengan responsive design.

```vue
<q-form class="standard-form">
  <div class="form-row">
    <div class="form-group">
      <q-input 
        v-model="form.name" 
        label="Name" 
        outlined 
        class="form-input"
      >
        <template v-slot:prepend>
          <q-icon name="person" />
        </template>
      </q-input>
    </div>
    
    <div class="form-group">
      <q-input 
        v-model="form.email" 
        label="Email" 
        outlined 
        class="form-input"
      />
    </div>
  </div>
  
  <div class="form-row">
    <div class="form-group full-width">
      <q-input 
        v-model="form.description" 
        type="textarea" 
        label="Description" 
        outlined 
        class="form-input"
      />
    </div>
  </div>
  
  <div class="form-actions">
    <q-btn type="submit" color="primary" label="Save" class="action-btn" />
    <q-btn type="button" color="grey-7" label="Cancel" class="action-btn" flat />
  </div>
</q-form>
```

---

## Tab Classes

### `.modern-tabs`

Modern tab system dengan styling yang konsisten.

```vue
<div class="modern-tabs">
  <q-card flat class="tabs-card">
    <q-tabs v-model="activeTab" class="q-tabs">
      <q-tab name="general" icon="person" label="General" />
      <q-tab name="security" icon="security" label="Security" />
      <q-tab name="preferences" icon="tune" label="Preferences" />
    </q-tabs>
  </q-card>
  
  <q-tab-panels v-model="activeTab" class="q-tab-panels">
    <q-tab-panel name="general">
      <!-- Content -->
    </q-tab-panel>
  </q-tab-panels>
</div>
```

---

## Table Classes

### `.enhanced-table`

Enhanced table styling dengan hover effects.

```vue
<q-card flat bordered class="enhanced-table">
  <q-table 
    :rows="rows" 
    :columns="columns" 
    :loading="loading"
    row-key="id"
  >
    <!-- Table content -->
  </q-table>
</q-card>
```

---

## Filter Classes

### `.filter-section`

Section untuk filter dengan card styling.

```vue
<div class="filter-section">
  <q-card flat class="filter-card">
    <q-card-section>
      <div class="filter-row">
        <div class="filter-group">
          <q-input
            v-model="search"
            placeholder="Search..."
            outlined
            dense
          />
        </div>

        <div class="filter-group">
          <q-select
            v-model="status"
            :options="statusOptions"
            label="Status"
            outlined
            dense
          />
        </div>

        <div class="filter-actions">
          <q-btn flat icon="refresh" @click="refresh" />
        </div>
      </div>
    </q-card-section>
  </q-card>
</div>
```

---

## Utility Classes

### Spacing

```vue
<!-- Margin utilities -->
<div class="spacing-xs">Extra small margin</div>
<div class="spacing-sm">Small margin</div>
<div class="spacing-md">Medium margin</div>
<div class="spacing-lg">Large margin</div>
<div class="spacing-xl">Extra large margin</div>

<!-- Gap utilities untuk flex/grid -->
<div class="row gap-md">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

### Text

```vue
<!-- Gradient text -->
<h1 class="text-gradient">Gradient Title</h1>
```

### Shadows

```vue
<div class="shadow-sm">Small shadow</div>
<div class="shadow-md">Medium shadow</div>
<div class="shadow-lg">Large shadow</div>
<div class="shadow-xl">Extra large shadow</div>
```

### Border Radius

```vue
<div class="rounded-sm">Small radius</div>
<div class="rounded-md">Medium radius</div>
<div class="rounded-lg">Large radius</div>
<div class="rounded-xl">Extra large radius</div>
```

### Animations

```vue
<!-- Transition utilities -->
<div class="transition-all hover-lift">Hover to lift</div>
<div class="transition-transform hover-scale">Hover to scale</div>
```

### Responsive

```vue
<!-- Hide pada mobile -->
<div class="hide-mobile">Hidden on mobile</div>

<!-- Show hanya pada mobile -->
<div class="show-mobile">Visible only on mobile</div>

<!-- Hide pada tablet -->
<div class="hide-tablet">Hidden on tablet</div>
```

---

## Page Examples

### Basic Page Template

```vue
<template>
  <q-page class="container padded">
    <!-- Standard Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t("page.title") }}</h4>
          <p class="page-subtitle">{{ $t("page.description") }}</p>
        </div>
        <div class="header-actions">
          <q-btn
            color="primary"
            icon="add"
            :label="$t('actions.create')"
            class="action-btn"
            @click="showCreateDialog = true"
          />
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
      <q-card flat class="filter-card">
        <q-card-section>
          <div class="filter-row">
            <div class="filter-group">
              <q-input
                v-model="filters.search"
                :placeholder="$t('common.search')"
                outlined
                dense
                clearable
              >
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>

            <div class="filter-actions">
              <q-btn
                flat
                icon="refresh"
                @click="fetchData"
                :loading="loading"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Main Content -->
    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="list" />
          {{ $t("page.contentTitle") }}
        </div>
        <p class="section-subtitle">{{ $t("page.contentDescription") }}</p>
      </q-card-section>

      <q-card-section>
        <!-- Your content here -->
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";

// State
const loading = ref(false);
const showCreateDialog = ref(false);
const filters = ref({
  search: "",
});

// Methods
const fetchData = async () => {
  loading.value = true;
  try {
    // API call
  } finally {
    loading.value = false;
  }
};

// Lifecycle
onMounted(() => {
  fetchData();
});
</script>
```

### Form Page Template

```vue
<template>
  <q-page class="container padded">
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t("form.title") }}</h4>
          <p class="page-subtitle">{{ $t("form.description") }}</p>
        </div>
      </div>
    </div>

    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="edit" />
          {{ $t("form.sectionTitle") }}
        </div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="standard-form">
          <div class="form-row">
            <div class="form-group">
              <q-input
                v-model="form.name"
                :label="$t('form.name')"
                outlined
                :rules="[(val) => !!val || $t('validation.required')]"
                class="form-input"
              >
                <template v-slot:prepend>
                  <q-icon name="person" />
                </template>
              </q-input>
            </div>

            <div class="form-group">
              <q-input
                v-model="form.email"
                :label="$t('form.email')"
                type="email"
                outlined
                class="form-input"
              />
            </div>
          </div>

          <div class="form-actions">
            <q-btn
              type="submit"
              color="primary"
              :label="$t('actions.save')"
              :loading="saving"
              class="action-btn"
            />
            <q-btn
              type="button"
              color="grey-7"
              :label="$t('actions.cancel')"
              flat
              class="action-btn"
              @click="$router.back()"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>
```

### Tab-based Page Template

```vue
<template>
  <q-page class="container">
    <!-- Gradient Header -->
    <div class="gradient-header">
      <div class="header-background">
        <div class="header-overlay"></div>
      </div>
      <div class="header-content">
        <div class="header-card">
          <h3 class="text-gradient">{{ $t("page.title") }}</h3>
          <p>{{ $t("page.description") }}</p>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="content-wrapper">
      <div class="modern-tabs">
        <q-card flat class="tabs-card">
          <q-tabs v-model="activeTab">
            <q-tab name="tab1" icon="home" :label="$t('tabs.tab1')" />
            <q-tab name="tab2" icon="settings" :label="$t('tabs.tab2')" />
          </q-tabs>
        </q-card>

        <q-tab-panels v-model="activeTab">
          <q-tab-panel name="tab1">
            <q-card flat bordered class="content-card">
              <!-- Tab 1 content -->
            </q-card>
          </q-tab-panel>

          <q-tab-panel name="tab2">
            <q-card flat bordered class="content-card">
              <!-- Tab 2 content -->
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </div>
  </q-page>
</template>
```

---

## Best Practices

### 1. Consistency

- Selalu gunakan class global untuk styling yang konsisten
- Hindari inline styles kecuali untuk kasus khusus
- Gunakan theme colors dan spacing yang sudah ditentukan

### 2. Responsive Design

- Gunakan responsive utilities untuk hide/show content
- Test di berbagai screen size
- Prioritaskan mobile-first approach

### 3. Performance

- Gunakan transition classes untuk smooth animations
- Avoid heavy animations pada mobile devices
- Lazy load content bila diperlukan

### 4. Accessibility

- Pastikan contrast ratio yang cukup
- Gunakan semantic HTML
- Provide proper aria-labels

### 5. Dark Mode

- Semua global classes sudah support dark mode
- Test appearance di both light dan dark theme
- Gunakan theme-aware colors

### 6. Maintenance

- Document custom styles bila ada
- Follow naming conventions yang konsisten
- Regular cleanup unused styles

---

## Migration Guide

### Dari Style Lama ke Global Classes

**Before:**

```vue
<q-page class="user-list-page">
  <div class="page-header">
    <!-- Custom styles -->
  </div>
</q-page>

<style scoped>
.user-list-page {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}
</style>
```

**After:**

```vue
<q-page class="container padded">
  <div class="page-header">
    <div class="header-content">
      <!-- Structured content -->
    </div>
  </div>
</q-page>

<!-- No custom styles needed -->
```

### Update Checklist

- [ ] Replace custom page containers dengan `.container.padded`
- [ ] Update headers menggunakan `.page-header` structure
- [ ] Convert cards ke `.content-card` atau `.simple-card`
- [ ] Update forms dengan `.standard-form` class
- [ ] Add responsive utilities dimana diperlukan
- [ ] Remove duplicate custom styles
- [ ] Test dark mode compatibility

---

## Support

Untuk pertanyaan atau request fitur baru terkait global styles, silakan:

1. Check dokumentasi ini terlebih dahulu
2. Lihat contoh implementasi di existing pages
3. Konsultasi dengan team untuk consistency

**Happy coding! 🚀**
