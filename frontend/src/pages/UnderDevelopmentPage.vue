<template>
    <q-page class="under-development-page">
        <div class="development-container">
            <div class="development-content">
                <!-- Icon -->
                <div class="development-icon">
                    <q-icon name="construction" size="120px" color="orange-6" />
                </div>

                <!-- Title -->
                <h3 class="development-title">{{ $t('common.underDevelopment') }}</h3>

                <!-- Description -->
                <p class="development-description">
                    {{ $t('common.underDevelopmentDescription') }}
                </p>

                <!-- Action Button -->
                <q-btn color="primary" :label="$t('common.backToDashboard')" icon="arrow_back" :to="{ name: 'dashboard' }" no-caps
                    rounded class="back-btn" />

                <!-- Progress indicator -->
                <div class="progress-section">
                    <p class="progress-text">{{ $t('common.developmentProgress') }}</p>
                    <q-linear-progress :value="0.3" color="orange-6" track-color="orange-2" size="8px" rounded
                        class="progress-bar" />
                    <p class="progress-percentage">{{ $t('common.progressComplete', { percent: 30 }) }}</p>
                </div>
            </div>
        </div>
    </q-page>
</template>

<script setup>
// This page doesn't need any reactive data for now
</script>

<style lang="scss" scoped>
.under-development-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem;
}

.development-container {
    max-width: 500px;
    text-align: center;
}

.development-content {
    .development-icon {
        margin-bottom: 2rem;
        opacity: 0.8;
    }

    .development-title {
        margin: 0 0 1rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 2rem;
    }

    .development-description {
        margin: 0 0 2rem;
        color: #718096;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .back-btn {
        margin-bottom: 3rem;
        padding: 12px 24px;
        font-weight: 600;
    }

    .progress-section {
        .progress-text {
            margin: 0 0 1rem;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .progress-bar {
            margin-bottom: 0.5rem;
        }

        .progress-percentage {
            margin: 0;
            color: #718096;
            font-size: 0.8rem;
        }
    }
}

// Dark mode
.body--dark {
    .development-content {
        .development-title {
            color: #f7fafc;
        }

        .development-description {
            color: #a0aec0;
        }

        .progress-section {
            .progress-text {
                color: #e2e8f0;
            }

            .progress-percentage {
                color: #a0aec0;
            }
        }
    }
}

// Responsive
@media (max-width: 480px) {
    .development-content {
        .development-icon {
            margin-bottom: 1.5rem;

            .q-icon {
                font-size: 80px !important;
            }
        }

        .development-title {
            font-size: 1.5rem;
        }

        .development-description {
            font-size: 1rem;
        }
    }
}
</style>
