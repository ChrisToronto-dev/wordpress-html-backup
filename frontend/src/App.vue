<script setup>
import { ref } from 'vue'
import { RouterView, RouterLink } from 'vue-router'

const isDarkMode = ref(false)

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col font-sans transition-colors duration-300">
    <header class="sticky top-0 z-50 glass">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex-shrink-0 flex items-center">
            <RouterLink to="/" class="text-2xl font-bold text-primary-600 dark:text-primary-500">
              📦 WP Static Backup
            </RouterLink>
          </div>
          <div class="flex items-center space-x-4">
            <button 
              @click="toggleDarkMode" 
              class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
            >
              <span v-if="isDarkMode">☀️</span>
              <span v-else>🌙</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </RouterView>
    </main>

    <footer class="glass mt-auto py-6">
      <div class="max-w-7xl mx-auto px-4 text-center text-sm text-slate-500 dark:text-slate-400">
        &copy; {{ new Date().getFullYear() }} WordPress to Static HTML Backup Tool. All rights reserved.
      </div>
    </footer>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
