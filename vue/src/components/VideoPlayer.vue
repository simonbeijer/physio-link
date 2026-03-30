<template>
  <div
    class="relative w-full aspect-[9/16] max-w-[400px] mx-auto bg-black rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
    <div ref="playerElement" class="w-full h-full"></div>

    <div class="absolute inset-0 z-10 bg-transparent"></div>

    <div v-if="isLoading"
      class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 text-white gap-4">
      <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <p class="font-medium animate-pulse text-blue-200">Preparing Exercise...</p>
    </div>

    <div v-if="error"
      class="absolute inset-0 flex flex-col items-center justify-center bg-red-900/90 text-white p-6 text-center">
      <span class="text-4xl mb-2">⚠️</span>
      <p class="font-bold text-xl">{{ error }}</p>
      <p class="text-sm text-red-200 mt-2">The video could not be loaded. Please check the YouTube link.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { type Exercise } from '@/types'

const props = defineProps<{
  exercise: Exercise | undefined
}>()

const playerElement = ref<HTMLElement | null>(null)
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const player = ref<any>(null)
const isLoading = ref(true)
const error = ref('')
const loopInterval = ref<number | null>(null)

let apiPromise: Promise<void> | null = null

function loadYouTubeAPI(): Promise<void> {
  if (apiPromise) return apiPromise

  apiPromise = new Promise((resolve) => {
    if ((window as any).YT && (window as any).YT.Player) {
      resolve()
      return
    }

    const tag = document.createElement('script')
    tag.src = 'https://www.youtube.com/iframe_api'
    const firstScriptTag = document.getElementsByTagName('script')[0]
    firstScriptTag.parentNode?.insertBefore(tag, firstScriptTag)

    const previousOnReady = (window as any).onYouTubeIframeAPIReady
      ; (window as any).onYouTubeIframeAPIReady = () => {
        if (previousOnReady) previousOnReady()
        resolve()
      }
  })

  return apiPromise
}

function getVideoId(url: string): string | null {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=|shorts\/)([^#&?]*).*/
  const match = url.match(regExp)
  return match && match[2].length === 11 ? match[2] : null
}

async function initPlayer() {
  if (!props.exercise) return

  try {
    await loadYouTubeAPI()

    const videoId = getVideoId(props.exercise.youtube_url)
    if (!videoId) {
      error.value = 'Invalid Video URL'
      isLoading.value = false
      return
    }

    if (!playerElement.value) return

    if (player.value && player.value.destroy) {
      player.value.destroy()
    }

    player.value = new (window as any).YT.Player(playerElement.value, {
      height: '100%',
      width: '100%',
      videoId: videoId,
      playerVars: {
        autoplay: 1,
        controls: 0,
        modestbranding: 1,
        rel: 0,
        iv_load_policy: 3,
        start: props.exercise.start_time,
        mute: 1,
        playsinline: 1,
      },
      events: {
        onReady: onPlayerReady,
        onStateChange: onPlayerStateChange,
        onError: () => {
          error.value = 'YouTube Player Error'
          isLoading.value = false
        }
      }
    })
  } catch (err) {
    console.error('Failed to init YouTube player:', err)
    error.value = 'Connection Error'
    isLoading.value = false
  }
}

function onPlayerReady(event: any) {
  isLoading.value = false
  
  if (props.exercise) {
    event.target.seekTo(props.exercise.start_time, true)
  }
  
  event.target.playVideo()
  if (event.target.getPlayerState() !== 1) {
    event.target.mute()
    event.target.playVideo()
  }
  startLoop()
}

function onPlayerStateChange(event: any) {
  if (event.data === 1) {
    isLoading.value = false
    startLoop()
  } else if (event.data === 0) {
    if (props.exercise) {
      event.target.seekTo(props.exercise.start_time, true)
      event.target.playVideo()
    }
  } else if (event.data === 3) {
    isLoading.value = true
  }
}

function startLoop() {
  stopLoop()

  if (!props.exercise || props.exercise.end_time <= 0) return

  loopInterval.value = window.setInterval(() => {
    if (!player.value || !player.value.getCurrentTime) return

    const currentTime = player.value.getCurrentTime()
    const startTime = props.exercise?.start_time || 0
    const endTime = props.exercise?.end_time || 0

    if (currentTime >= endTime) {
      player.value.seekTo(startTime)
    }
  }, 100)
}

function stopLoop() {
  if (loopInterval.value) {
    clearInterval(loopInterval.value)
    loopInterval.value = null
  }
}

watch(() => props.exercise?.id, async (newId) => {
  if (!newId || !props.exercise) return

  error.value = ''
  isLoading.value = true
  stopLoop()

  const videoId = getVideoId(props.exercise.youtube_url)
  if (!videoId) {
    error.value = 'Invalid Video URL'
    isLoading.value = false
    return
  }

  if (player.value && player.value.loadVideoById) {
    player.value.loadVideoById({
      videoId: videoId,
      startSeconds: props.exercise.start_time,
      endSeconds: props.exercise.end_time > 0 ? props.exercise.end_time : undefined
    })
  } else {
    await nextTick()
    initPlayer()
  }
}, { immediate: true })

onUnmounted(() => {
  stopLoop()
  if (player.value && player.value.destroy) {
    player.value.destroy()
  }
})
</script>