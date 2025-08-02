<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <div class="auth-container">
        <div class="auth-background">
          <!-- Background pattern or image -->
          <div class="auth-pattern"></div>
        </div>

        <div class="auth-content">
          <div class="auth-card">
            <!-- Logo/Brand -->
            <div class="auth-header">
              <div class="auth-logo">
                <q-icon name="dashboard_customize" size="60px" color="primary" />
              </div>
              <h4 class="auth-title">Dashboard Management</h4>
              <p class="auth-subtitle">Modern admin dashboard solution</p>
            </div>

            <!-- Auth form content -->
            <div class="auth-form">
              <router-view />
            </div>

            <!-- Footer -->
            <div class="auth-footer">
              <p class="text-grey-6">
                © 2025 Dashboard Management. All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  // Add any initialization logic here
})
</script>

<style lang="scss" scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;

  .auth-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
      radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
    animation: float 20s ease-in-out infinite;
  }
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }

  33% {
    transform: translateY(-10px) rotate(1deg);
  }

  66% {
    transform: translateY(5px) rotate(-1deg);
  }
}

.auth-content {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
  z-index: 1;
}

.auth-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
  padding: 3rem;
  width: 100%;
  max-width: 420px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;

  .auth-logo {
    margin: 0 auto 1rem;
    border-radius: 12px;
  }

  .auth-title {
    margin: 0 0 0.5rem;
    font-weight: 600;
    color: #2d3748;
    font-size: 1.5rem;
  }

  .auth-subtitle {
    margin: 0;
    color: #718096;
    font-size: 0.9rem;
  }
}

.auth-form {
  margin-bottom: 1.5rem;
}

.auth-footer {
  text-align: center;

  p {
    margin: 0;
    font-size: 0.8rem;
  }
}

// Dark mode support
.body--dark {
  .auth-container {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
  }

  .auth-card {
    background: #2d3748;
    border: 1px solid rgba(255, 255, 255, 0.1);

    .auth-title {
      color: #f7fafc;
    }

    .auth-subtitle {
      color: #a0aec0;
    }
  }
}

// Responsive design
@media (max-width: 480px) {
  .auth-content {
    padding: 1rem;
  }

  .auth-card {
    padding: 2rem 1.5rem;
    margin: 0;
    border-radius: 12px;
  }

  .auth-header {
    .auth-title {
      font-size: 1.3rem;
    }
  }
}
</style>
