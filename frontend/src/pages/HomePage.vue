<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const targetUrl = ref('')

const startBackup = () => {
  if (targetUrl.value) {
    // 임시로 uuid 생성 또는 백엔드 검증 로직 추가 예정
    router.push('/backup/configure')
  }
}
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-[60vh] text-center space-y-8">
    <div class="space-y-4 max-w-2xl">
      <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white">
        Convert WordPress to <br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-accent-500">
          Static HTML
        </span>
      </h1>
      <p class="text-lg text-slate-600 dark:text-slate-400">
        Enter your WordPress site URL and we'll crawl, download, and package it into a completely static, locally navigable HTML site.
      </p>
    </div>

    <div class="w-full max-w-xl glass p-2 rounded-2xl flex items-center shadow-2xl">
      <div class="px-4 text-slate-400">
        🌐
      </div>
      <input 
        v-model="targetUrl"
        type="url" 
        placeholder="https://your-wordpress-site.com" 
        class="flex-grow bg-transparent border-none outline-none text-lg py-3 text-slate-800 dark:text-slate-100 placeholder-slate-400"
        @keyup.enter="startBackup"
      />
      <button 
        @click="startBackup"
        class="bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-transform transform hover:scale-105 active:scale-95"
      >
        Start
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl mt-12">
      <div class="glass p-6 rounded-2xl text-left space-y-3">
        <div class="text-3xl">🕷️</div>
        <h3 class="text-xl font-bold dark:text-white">Smart Crawling</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Automatically detects internal links, sitemaps, and downloads all necessary assets like CSS, JS, and images.</p>
      </div>
      <div class="glass p-6 rounded-2xl text-left space-y-3">
        <div class="text-3xl">🔄</div>
        <h3 class="text-xl font-bold dark:text-white">URL Rewriting</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Converts all absolute URLs to relative paths, ensuring the downloaded site is fully navigable offline.</p>
      </div>
      <div class="glass p-6 rounded-2xl text-left space-y-3">
        <div class="text-3xl">📦</div>
        <h3 class="text-xl font-bold dark:text-white">ZIP Packaging</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Packages everything into a neat ZIP file with the original directory structure preserved.</p>
      </div>
    </div>
  </div>
</template>
