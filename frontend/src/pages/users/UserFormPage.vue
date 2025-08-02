<template>
    <q-page class="user-form-page">
        <div class="page-header">
            <h4>{{ isEdit ? 'Edit User' : 'Create User' }}</h4>
            <p>{{ isEdit ? 'Update user information' : 'Add a new user' }}</p>
        </div>

        <!-- Coming soon content -->
        <q-card flat bordered>
            <q-card-section class="text-center">
                <q-icon name="person_add" size="64px" color="grey-4" class="q-mb-md" />
                <h6>User Form Interface</h6>
                <p class="text-grey-6">This feature is coming soon!</p>
                <q-btn color="primary" label="Back to Users" :to="{ name: 'users.index' }" />
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
.user-form-page {
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
