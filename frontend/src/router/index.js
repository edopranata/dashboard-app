import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'
import { useAuthStore } from 'src/stores/auth'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  // Navigation guards
  Router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // Initialize auth if token exists but user is not loaded
    if (authStore.token && !authStore.user) {
      await authStore.initAuth()
    }

    // Check if route requires authentication
    if (to.matched.some((record) => record.meta.requiresAuth)) {
      if (!authStore.isLoggedIn) {
        // User is not authenticated, redirect to login
        next({
          name: 'login',
          query: { redirect: to.fullPath },
        })
        return
      }

      // Check permission if required
      if (to.meta.permission && !authStore.hasPermission(to.meta.permission)) {
        // User doesn't have required permission
        next({
          name: 'dashboard',
          query: {
            error: 'You do not have permission to access this page',
          },
        })
        return
      }
    }

    // Check if route requires guest (not authenticated)
    if (to.matched.some((record) => record.meta.requiresGuest)) {
      if (authStore.isLoggedIn) {
        // User is authenticated, redirect to dashboard
        next({ name: 'dashboard' })
        return
      }
    }

    // Set page title if provided
    if (to.meta.title) {
      document.title = `${to.meta.title} - Dashboard Management`
    } else {
      document.title = 'Dashboard Management'
    }

    next()
  })

  return Router
})
