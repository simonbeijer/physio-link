<template>
  <div class="flex items-center gap-2 shrink-0">
    <button
      @click="toggleTimer"
      class="relative w-16 h-16 shrink-0 flex items-center justify-center group"
      :aria-label="isActive ? 'Pausa' : 'Starta'"
    >
      <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
        <circle class="text-slate-200 stroke-current" stroke-width="8" fill="transparent" r="45" cx="50" cy="50" />
        <circle
          class="stroke-current"
          :class="timeLeft < 10 ? 'text-orange-500' : 'text-blue-600'"
          stroke-width="8" stroke-linecap="round" fill="transparent"
          r="45" cx="50" cy="50"
          :style="{ strokeDasharray, strokeDashoffset: strokeDasharray - progress }"
        />
      </svg>
      <span
        v-if="!isActive"
        class="absolute inset-2 rounded-full bg-blue-600/20 animate-ping"
        aria-hidden="true"
      />
      <div
        class="relative w-10 h-10 rounded-full flex items-center justify-center shadow-md ring-2 ring-white transition-colors"
        :class="'bg-blue-600'"
      >
        <component :is="isActive ? Pause : Play"
          class="w-5 h-5 fill-white text-white"
          :class="{ 'translate-x-[1px]': !isActive }"
        />
      </div>
      <span class="absolute bottom-0.5 text-[9px] font-mono font-bold tabular-nums text-slate-700 bg-white/80 px-1 rounded">{{ timeLeft }}s</span>
    </button>

    <button
      @click="resetTimer"
      class="h-10 w-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 shrink-0"
      aria-label="Återställ"
    >
      <RotateCcw class="w-4 h-4" />
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { Play, Pause, RotateCcw } from 'lucide-vue-next'

const props = withDefaults(defineProps<{
  duration: number
  autoStart?: boolean
}>(), {
  autoStart: false,
})

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
  const now = ctx.currentTime
  const notes = [
    { freq: 587.33, start: 0,    dur: 0.35 },
    { freq: 880.00, start: 0.18, dur: 0.55 },
  ]
  notes.forEach(({ freq, start, dur }) => {
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()
    osc.type = 'sine'
    osc.frequency.setValueAtTime(freq, now + start)
    gain.gain.setValueAtTime(0, now + start)
    gain.gain.linearRampToValueAtTime(0.3, now + start + 0.02)
    gain.gain.exponentialRampToValueAtTime(0.001, now + start + dur)
    osc.connect(gain)
    gain.connect(ctx.destination)
    osc.start(now + start)
    osc.stop(now + start + dur)
  })
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

onMounted(() => {
  if (props.autoStart) startTimer()
})

onUnmounted(() => {
  stopTimer()
})
</script>

<style scoped>
circle {
  transition: stroke-dashoffset 1s linear, stroke 0.3s ease;
}
</style>
