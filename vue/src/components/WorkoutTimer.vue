<script setup lang="ts">
import { ref, watch, onUnmounted, computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Play, Pause, RotateCcw } from 'lucide-vue-next'

const props = defineProps<{
  duration: number
}>()

const emit = defineEmits<{
  (e: 'finished'): void
}>()

const timeLeft = ref(props.duration)
const isActive = ref(false)
const timer = ref<number | null>(null)

// Circular progress calculation
const strokeDasharray = 283 // 2 * PI * 45 (radius)
const progress = computed(() => {
  return (timeLeft.value / props.duration) * strokeDasharray
})

// Audio feedback (Base64 Beep to avoid external assets)
const playFinishSound = () => {
  const audioContext = new (window.AudioContext || (window as any).webkitAudioContext)()
  const oscillator = audioContext.createOscillator()
  const gainNode = audioContext.createGain()

  oscillator.connect(gainNode)
  gainNode.connect(audioContext.destination)

  oscillator.type = 'sine'
  oscillator.frequency.setValueAtTime(880, audioContext.currentTime) // A5 note
  gainNode.gain.setValueAtTime(0, audioContext.currentTime)
  gainNode.gain.linearRampToValueAtTime(0.5, audioContext.currentTime + 0.1)
  gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5)

  oscillator.start(audioContext.currentTime)
  oscillator.stop(audioContext.currentTime + 0.5)
}

const startTimer = () => {
  if (isActive.value || timeLeft.value <= 0) return
  isActive.value = true
  timer.value = window.setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      stopTimer()
      playFinishSound()
      emit('finished')
    }
  }, 1000)
}

const stopTimer = () => {
  isActive.value = false
  if (timer.value) {
    clearInterval(timer.value)
    timer.value = null
  }
}

const toggleTimer = () => {
  if (isActive.value) stopTimer()
  else startTimer()
}

const resetTimer = () => {
  stopTimer()
  timeLeft.value = props.duration
}

// Reset timer when exercise changes
watch(() => props.duration, (newDuration) => {
  resetTimer()
}, { immediate: true })

onUnmounted(() => {
  stopTimer()
})
</script>

<template>
  <div class="flex flex-row items-center gap-6 p-6 bg-white rounded-2xl border shadow-sm w-full max-w-md">
    <!-- Controls -->
    <div class="flex flex-col gap-4 sm:gap-10 items-center min-w-[100px] sm:min-w-[120px]">
      <Button :variant="isActive ? 'outline' : 'default'" size="lg"
        class="h-10 sm:h-12 w-24 sm:w-32 rounded-full font-bold transition-all shadow-md active:scale-95 text-xs sm:text-base"
        :class="isActive ? 'bg-white border-orange-500 text-orange-600 hover:bg-orange-50' : 'bg-blue-600 hover:bg-blue-700'"
        @click="toggleTimer">
        <component :is="isActive ? Pause : Play" class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 fill-current" />
        {{ isActive ? 'Pause' : 'Start' }}
      </Button>
      <Button variant="outline" size="icon" class="h-10 w-10 sm:h-12 sm:w-12 rounded-full border-2" @click="resetTimer">
        <RotateCcw class="w-4 h-4 sm:w-5 sm:h-5" />
      </Button>

    </div>
    <!-- Progress Ring -->
    <div class="relative w-32 h-32 sm:w-48 sm:h-48 flex-shrink-0">
      <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
        <!-- Background Circle -->
        <circle class="text-slate-100 stroke-current" stroke-width="8" fill="transparent" r="45" cx="50" cy="50" />
        <!-- Progress Circle -->
        <circle class="transition-all duration-1000 ease-linear stroke-current"
          :class="timeLeft < 10 ? 'text-orange-500' : 'text-blue-600'" stroke-width="8" stroke-linecap="round"
          fill="transparent" r="45" cx="50" cy="50" :style="{
            strokeDasharray: `${strokeDasharray}`,
            strokeDashoffset: `${strokeDasharray - progress}`
          }" />
      </svg>
      <!-- Time Display -->
      <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-2xl sm:text-4xl font-mono font-black tabular-nums text-slate-900">
          {{ timeLeft }}s
        </span>
      </div>
    </div>


  </div>
</template>

<style scoped>
/* Ensure smooth transitions for the ring */
circle {
  transition: stroke-dashoffset 1s linear, stroke 0.3s ease;
}
</style>
