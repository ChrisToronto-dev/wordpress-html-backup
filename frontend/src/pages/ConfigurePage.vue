<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const settings = ref({
  maxPages: 500,
  maxDepth: 10,
  concurrency: 5,
  includeImages: true,
  includeVideos: false,
})

const submitConfig = () => {
  // 실제 백엔드 API 연동 위치
  const mockUuid = '1234-abcd-5678-efgh'
  router.push(`/backup/${mockUuid}/progress`)
}
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-8 animate-fade-in">
    <div class="space-y-2">
      <h2 class="text-3xl font-bold dark:text-white">Configure Backup Settings</h2>
      <p class="text-slate-600 dark:text-slate-400">Customize the crawling behavior for your site.</p>
    </div>

    <div class="glass p-8 rounded-2xl space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="block text-sm font-medium dark:text-slate-300">Max Pages</label>
          <input type="number" v-model="settings.maxPages" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-lg p-3 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-primary-500" />
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium dark:text-slate-300">Max Depth</label>
          <input type="number" v-model="settings.maxDepth" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-lg p-3 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-primary-500" />
        </div>
      </div>

      <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold dark:text-white">Assets Included</h3>
        <div class="flex items-center space-x-6">
          <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" v-model="settings.includeImages" class="form-checkbox text-primary-500 rounded focus:ring-primary-500 w-5 h-5 bg-slate-100 dark:bg-slate-800 border-none" />
            <span class="dark:text-slate-300">Images</span>
          </label>
          <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" v-model="settings.includeVideos" class="form-checkbox text-primary-500 rounded focus:ring-primary-500 w-5 h-5 bg-slate-100 dark:bg-slate-800 border-none" />
            <span class="dark:text-slate-300">Videos</span>
          </label>
        </div>
      </div>

      <div class="pt-6 flex justify-end space-x-4">
        <button @click="router.back()" class="px-6 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
          Cancel
        </button>
        <button @click="submitConfig" class="px-8 py-3 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-bold shadow-lg transition-transform transform hover:scale-105">
          Start Backup
        </button>
      </div>
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
