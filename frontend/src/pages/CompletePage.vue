<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const downloadZip = () => {
  // 실제 다운로드 API 연동 예정
  alert('Downloading site-backup.zip...')
}
</script>

<template>
  <div class="max-w-3xl mx-auto flex flex-col items-center justify-center text-center space-y-8 min-h-[50vh] animate-fade-in">
    <div class="w-24 h-24 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center text-5xl mb-4">
      ✓
    </div>
    
    <div class="space-y-2">
      <h2 class="text-4xl font-bold dark:text-white">Backup Completed!</h2>
      <p class="text-slate-600 dark:text-slate-400">Your static HTML site is ready to download.</p>
    </div>

    <div class="glass p-8 rounded-2xl w-full grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
      <div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Pages</div>
        <div class="text-2xl font-bold dark:text-white">120</div>
      </div>
      <div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Assets</div>
        <div class="text-2xl font-bold dark:text-white">500</div>
      </div>
      <div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Size</div>
        <div class="text-2xl font-bold dark:text-white">15.4 MB</div>
      </div>
    </div>

    <div class="flex space-x-4 pt-8">
      <button 
        @click="router.push('/')" 
        class="px-6 py-3 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
      >
        Backup Another Site
      </button>
      <button 
        @click="downloadZip" 
        class="px-8 py-3 rounded-xl bg-gradient-to-r from-accent-500 to-primary-500 hover:from-accent-400 hover:to-primary-400 text-white font-bold shadow-lg transition-transform transform hover:scale-105 flex items-center space-x-2"
      >
        <span>Download ZIP</span>
        <span>⬇️</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
