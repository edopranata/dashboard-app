<template>
    <q-page class="role-form-page">
        <div class="page-header">
            <h4>{{ isEdit ? 'Edit Role' : 'Create Role' }}</h4>
            <p>{{ isEdit ? 'Update role information' : 'Add a new role' }}</p>
        </div>

        <!-- Coming soon content -->
        <q-card flat bordered>
            <q-card-section class="text-center">
                <q-icon name="add_moderator" size="64px" color="grey-4" class="q-mb-md" />
                <h6>Role Form Interface</h6>
                <p class="text-grey-6">This feature is coming soon!</p>
                <q-btn color="primary" label="Back to Roles" :to="{ name: 'roles.index' }" />
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isEdit = computed(() => route.params.id !== undefined)
</script>

<style lang="scss" scoped>
.role-form-page {
    padding: 1.5rem;
}

.page-header {
    margin-bottom: 2rem;

    h4 {
        margin: 0 0 0.5rem;
        font-weight: 600;
        color: #2d3748;
    }

    p {
        margin: 0;
        color: #718096;
    }
}
</style>
