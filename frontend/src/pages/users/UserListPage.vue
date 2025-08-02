<template>
    <q-page class="user-list-page">
        <div class="page-header">
            <h4>User Management</h4>
            <p>Manage users and their roles</p>
        </div>

        <!-- Coming soon content -->
        <q-card flat bordered>
            <q-card-section class="text-center">
                <q-icon name="people" size="64px" color="grey-4" class="q-mb-md" />
                <h6>User Management Interface</h6>
                <p class="text-grey-6">This feature is coming soon!</p>
                <q-btn color="primary" label="Back to Dashboard" :to="{ name: 'dashboard' }" />
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
// Placeholder component
</script>

<style lang="scss" scoped>
.user-list-page {
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
