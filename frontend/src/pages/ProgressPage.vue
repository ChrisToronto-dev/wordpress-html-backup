<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const uuid = route.params.uuid

const progress = ref(0)
const status = ref('crawling')
const currentUrl = ref('https://example.com/starting-crawl...')

onMounted(() => {
  // 모의 진행률 시뮬레이션
  const interval = setInterval(() => {
    progress.value += 5
    if (progress.value >= 100) {
      clearInterval(interval)
      router.push(`/backup/${uuid}/complete`)
    } else if (progress.value > 80) {
      status.value = 'packaging'
      currentUrl.value = 'Creating ZIP archive...'
    } else if (progress.value > 50) {
      status.value = 'processing'
      currentUrl.value = 'Rewriting URLs...'
    } else {
      currentUrl.value = `https://example.com/page-${progress.value}`
    }
  }, 300)
})
</script>

<template>
  <div class="max-w-4xl mx-auto flex flex-col items-center justify-center min-h-[50vh] space-y-12">
    <div class="text-center space-y-4">
      <h2 class="text-3xl font-bold dark:text-white">Backup in Progress</h2>
      <p class="text-slate-600 dark:text-slate-400 capitalize">{{ status }} - Please wait...</p>
    </div>

    <div class="w-full max-w-2xl">
      <div class="flex justify-between text-sm mb-2 dark:text-slate-300 font-mono">
        <span>{{ currentUrl }}</span>
        <span>{{ progress }}%</span>
      </div>
      <div class="h-4 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
        <div 
          class="h-full bg-gradient-to-r from-primary-500 to-accent-500 transition-all duration-300 ease-out"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full max-w-2xl text-center">
      <div class="glass p-4 rounded-xl">
        <div class="text-2xl font-bold text-primary-500">45</div>
        <div class="text-xs text-slate-500">Pages Crawled</div>
      </div>
      <div class="glass p-4 rounded-xl">
        <div class="text-2xl font-bold text-primary-500">120</div>
        <div class="text-xs text-slate-500">Total Pages</div>
      </div>
      <div class="glass p-4 rounded-xl">
        <div class="text-2xl font-bold text-accent-500">230</div>
        <div class="text-xs text-slate-500">Assets Downloaded</div>
      </div>
      <div class="glass p-4 rounded-xl">
        <div class="text-2xl font-bold text-accent-500">500</div>
        <div class="text-xs text-slate-500">Total Assets</div>
      </div>
    </div>

    <button @click="router.push('/')" class="text-sm text-red-500 hover:text-red-600 transition-colors">
      Cancel Backup
    </button>
  </div>
</template>
