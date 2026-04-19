<template>
  <div class="flex items-center gap-2 shrink-0">
    <button
      @click="toggleTimer"
      class="h-9 w-9 rounded-full flex items-center justify-center transition-colors shrink-0"
      :class="isActive ? 'bg-orange-100 text-orange-600' : 'bg-blue-600 text-white'"
      :aria-label="isActive ? 'Pausa' : 'Starta'"
    >
      <component :is="isActive ? Pause : Play" class="w-3.5 h-3.5 fill-current" />
    </button>

    <div class="relative w-14 h-14 shrink-0">
      <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
        <circle class="text-slate-100 stroke-current" stroke-width="8" fill="transparent" r="45" cx="50" cy="50" />
        <circle
          class="stroke-current"
          :class="timeLeft < 10 ? 'text-orange-500' : 'text-blue-600'"
          stroke-width="8" stroke-linecap="round" fill="transparent"
          r="45" cx="50" cy="50"
          :style="{ strokeDasharray, strokeDashoffset: strokeDasharray - progress }"
        />
      </svg>
      <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-xs font-mono font-bold tabular-nums text-slate-900">{{ timeLeft }}s</span>
      </div>
    </div>

    <button
      @click="resetTimer"
      class="h-8 w-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 shrink-0"
      aria-label="Återställ"
    >
      <RotateCcw class="w-3.5 h-3.5" />
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onUnmounted, computed } from 'vue'
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
const strokeDasharray = 283

const progress = computed(() => {
  return (timeLeft.value / props.duration) * strokeDasharray
})

function playFinishSound() {
  const ctx = new (window.AudioContext || (window as any).webkitAudioContext)()
  const osc = ctx.createOscillator()
  const gain = ctx.createGain()
  osc.connect(gain)
  gain.connect(ctx.destination)
  osc.type = 'sine'
  osc.frequency.setValueAtTime(880, ctx.currentTime)
  gain.gain.setValueAtTime(0, ctx.currentTime)
  gain.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.1)
  gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5)
  osc.start(ctx.currentTime)
  osc.stop(ctx.currentTime + 0.5)
}

function startTimer() {
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

function stopTimer() {
  isActive.value = false
  if (timer.value) {
    clearInterval(timer.value)
    timer.value = null
  }
}

function toggleTimer() {
  if (isActive.value) stopTimer()
  else startTimer()
}

function resetTimer() {
  stopTimer()
  timeLeft.value = props.duration
}

watch(() => props.duration, () => {
  resetTimer()
}, { immediate: true })

onUnmounted(() => {
  stopTimer()
})
</script>

<style scoped>
circle {
  transition: stroke-dashoffset 1s linear, stroke 0.3s ease;
}
</style>
