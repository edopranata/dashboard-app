#!/usr/bin/env node

// =============================================================================
// PAGE TEMPLATE GENERATOR
// =============================================================================
// Generator untuk membuat halaman baru dengan global styles
// Usage: node generate-page.js PageName
// =============================================================================

const fs = require('fs')
const path = require('path')

// Get page name from command line arguments
const pageName = process.argv[2]

if (!pageName) {
  console.error('❌ Error: Please provide a page name')
  console.log('Usage: node generate-page.js PageName')
  process.exit(1)
}

// Convert PascalCase to kebab-case
const kebabCase = pageName.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase()

// Templates
const templates = {
  // Basic page template
  basic: `<template>
  <q-page class="container padded">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t('${kebabCase}.title') }}</h4>
          <p class="page-subtitle">{{ $t('${kebabCase}.description') }}</p>
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
              <q-btn flat icon="refresh" @click="fetchData" :loading="loading" />
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
          {{ $t('${kebabCase}.contentTitle') }}
        </div>
        <p class="section-subtitle">{{ $t('${kebabCase}.contentDescription') }}</p>
      </q-card-section>

      <q-card-section>
        <!-- Your content here -->
        <div class="text-center q-pa-xl">
          <q-icon name="construction" size="4rem" color="grey-5" />
          <div class="q-mt-md text-h6 text-grey-6">Content goes here</div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'

// Composables
const $q = useQuasar()
const { t } = useI18n()

// State
const loading = ref(false)
const showCreateDialog = ref(false)

const filters = reactive({
  search: ''
})

// Methods
const fetchData = async () => {
  loading.value = true
  try {
    // TODO: API call
    await new Promise(resolve => setTimeout(resolve, 1000))
  } catch (error) {
    console.error('Failed to fetch data:', error)
    $q.notify({
      type: 'negative',
      message: t('common.errorOccurred'),
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

<style lang="scss" scoped>
// Custom styles khusus untuk ${pageName}
// Global styles sudah dihandle oleh global.scss

// Add custom styles here if needed
</style>`,

  // Form page template
  form: `<template>
  <q-page class="container padded">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ isEdit ? $t('${kebabCase}.editTitle') : $t('${kebabCase}.createTitle') }}</h4>
          <p class="page-subtitle">{{ isEdit ? $t('${kebabCase}.editDescription') : $t('${kebabCase}.createDescription') }}</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="edit" />
          {{ $t('${kebabCase}.formTitle') }}
        </div>
        <p class="section-subtitle">{{ $t('${kebabCase}.formDescription') }}</p>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="standard-form">
          <div class="form-row">
            <div class="form-group">
              <q-input
                v-model="form.name"
                :label="$t('${kebabCase}.name')"
                outlined
                :rules="[val => !!val || $t('validation.required')]"
                class="form-input"
              >
                <template v-slot:prepend>
                  <q-icon name="text_fields" />
                </template>
              </q-input>
            </div>

            <div class="form-group">
              <q-input
                v-model="form.description"
                :label="$t('${kebabCase}.description')"
                outlined
                class="form-input"
              >
                <template v-slot:prepend>
                  <q-icon name="description" />
                </template>
              </q-input>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <q-input
                v-model="form.notes"
                :label="$t('${kebabCase}.notes')"
                type="textarea"
                outlined
                rows="4"
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
              icon="save"
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

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

// Composables
const $q = useQuasar()
const { t } = useI18n()
const route = useRoute()
const router = useRouter()

// State
const saving = ref(false)

const form = reactive({
  name: '',
  description: '',
  notes: ''
})

// Computed
const isEdit = computed(() => !!route.params.id)

// Methods
const onSubmit = async () => {
  saving.value = true
  try {
    // TODO: API call
    if (isEdit.value) {
      // Update logic
    } else {
      // Create logic
    }

    $q.notify({
      type: 'positive',
      message: t(isEdit.value ? '${kebabCase}.updated' : '${kebabCase}.created'),
      position: 'top'
    })

    router.back()
  } catch (error) {
    console.error('Failed to save:', error)
    $q.notify({
      type: 'negative',
      message: t('common.errorOccurred'),
      position: 'top'
    })
  } finally {
    saving.value = false
  }
}

const loadData = async () => {
  if (!isEdit.value) return

  try {
    // TODO: Load data for edit
  } catch (error) {
    console.error('Failed to load data:', error)
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>

<style lang="scss" scoped>
// Custom styles khusus untuk ${pageName}Form
// Global styles sudah dihandle oleh global.scss
</style>`,

  // Tab-based page template
  tabs: `<template>
  <q-page class="container">
    <!-- Gradient Header -->
    <div class="gradient-header">
      <div class="header-background">
        <div class="header-overlay"></div>
      </div>
      <div class="header-content">
        <div class="header-card">
          <h3 class="text-gradient">{{ $t('${kebabCase}.title') }}</h3>
          <p>{{ $t('${kebabCase}.description') }}</p>
        </div>
      </div>
    </div>

    <!-- Tab System -->
    <div class="content-wrapper">
      <div class="modern-tabs">
        <q-card flat class="tabs-card">
          <q-tabs v-model="activeTab">
            <q-tab name="overview" icon="dashboard" :label="$t('${kebabCase}.overview')" />
            <q-tab name="settings" icon="settings" :label="$t('${kebabCase}.settings')" />
            <q-tab name="advanced" icon="tune" :label="$t('${kebabCase}.advanced')" />
          </q-tabs>
        </q-card>

        <q-tab-panels v-model="activeTab" animated>
          <!-- Overview Tab -->
          <q-tab-panel name="overview">
            <q-card flat bordered class="content-card">
              <q-card-section class="card-header">
                <div class="section-title">
                  <q-icon name="dashboard" />
                  {{ $t('${kebabCase}.overviewTitle') }}
                </div>
                <p class="section-subtitle">{{ $t('${kebabCase}.overviewDescription') }}</p>
              </q-card-section>

              <q-card-section>
                <!-- Overview content -->
                <div class="text-center q-pa-xl">
                  <q-icon name="dashboard" size="4rem" color="primary" />
                  <div class="q-mt-md text-h6">Overview Content</div>
                </div>
              </q-card-section>
            </q-card>
          </q-tab-panel>

          <!-- Settings Tab -->
          <q-tab-panel name="settings">
            <q-card flat bordered class="content-card">
              <q-card-section class="card-header">
                <div class="section-title">
                  <q-icon name="settings" />
                  {{ $t('${kebabCase}.settingsTitle') }}
                </div>
                <p class="section-subtitle">{{ $t('${kebabCase}.settingsDescription') }}</p>
              </q-card-section>

              <q-card-section>
                <!-- Settings content -->
                <div class="text-center q-pa-xl">
                  <q-icon name="settings" size="4rem" color="orange" />
                  <div class="q-mt-md text-h6">Settings Content</div>
                </div>
              </q-card-section>
            </q-card>
          </q-tab-panel>

          <!-- Advanced Tab -->
          <q-tab-panel name="advanced">
            <q-card flat bordered class="content-card">
              <q-card-section class="card-header">
                <div class="section-title">
                  <q-icon name="tune" />
                  {{ $t('${kebabCase}.advancedTitle') }}
                </div>
                <p class="section-subtitle">{{ $t('${kebabCase}.advancedDescription') }}</p>
              </q-card-section>

              <q-card-section>
                <!-- Advanced content -->
                <div class="text-center q-pa-xl">
                  <q-icon name="tune" size="4rem" color="purple" />
                  <div class="q-mt-md text-h6">Advanced Content</div>
                </div>
              </q-card-section>
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'

// Composables
const $q = useQuasar()
const { t } = useI18n()

// State
const activeTab = ref('overview')

// Methods
const loadData = async () => {
  try {
    // TODO: Load data
  } catch (error) {
    console.error('Failed to load data:', error)
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>

<style lang="scss" scoped>
// Custom styles khusus untuk ${pageName}
// Global styles sudah dihandle oleh global.scss

.profile-info {
  display: flex;
  align-items: flex-end;
  gap: 2rem;

  @media (max-width: 1023px) {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .avatar-container {
    flex-shrink: 0;
    margin-bottom: -1rem;

    @media (max-width: 1023px) {
      margin-bottom: 0;
    }
  }

  .user-details {
    flex: 1;

    h3 {
      margin: 0 0 0.5rem 0;
      font-size: 2rem;
      font-weight: 700;

      @media (max-width: 599px) {
        font-size: 1.5rem;
      }
    }

    p {
      margin: 0 0 1rem 0;
      color: #718196;
      font-size: 1.1rem;

      .body--dark & {
        color: #a0aec0;
      }
    }
  }
}
</style>`,
}

// Interactive template selection
const readline = require('readline')
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
})

console.log(`🚀 Creating page: ${pageName}`)
console.log('\nSelect template type:')
console.log('1. Basic (List/Table page)')
console.log('2. Form (Create/Edit page)')
console.log('3. Tabs (Tab-based page)')

rl.question('\nEnter template number (1-3): ', (answer) => {
  let template
  let filename = `${pageName}.vue`

  switch (answer) {
    case '1':
      template = templates.basic
      break
    case '2':
      template = templates.form
      filename = `${pageName}Form.vue`
      break
    case '3':
      template = templates.tabs
      break
    default:
      console.log('❌ Invalid selection. Using basic template.')
      template = templates.basic
  }

  // Create pages directory if it doesn't exist
  const pagesDir = path.join(__dirname, '../src/pages')
  if (!fs.existsSync(pagesDir)) {
    fs.mkdirSync(pagesDir, { recursive: true })
  }

  // Write file
  const filePath = path.join(pagesDir, filename)

  if (fs.existsSync(filePath)) {
    console.log(`❌ File already exists: ${filename}`)
    rl.close()
    return
  }

  fs.writeFileSync(filePath, template)

  console.log(`\n✅ Successfully created: ${filename}`)
  console.log(`📁 Location: src/pages/${filename}`)
  console.log('\n📝 Next steps:')
  console.log('1. Add route to src/router/routes.js')
  console.log('2. Add i18n translations')
  console.log('3. Implement API calls and logic')
  console.log('4. Test responsive design')

  rl.close()
})
