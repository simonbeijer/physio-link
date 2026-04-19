<template>
  <div class="h-dvh flex flex-col bg-slate-50 overflow-hidden max-w-md mx-auto">

    <!-- Error -->
    <div v-if="error" class="flex-1 flex items-center justify-center p-4">
      <p class="text-red-600 bg-red-50 p-4 rounded-lg border border-red-200 text-sm font-medium">{{ error }}</p>
    </div>

    <!-- Completed -->
    <div v-else-if="isCompleted" class="flex-1 flex flex-col items-center justify-center gap-4 p-4">
      <div class="text-5xl">🎉</div>
      <h1 class="text-2xl font-bold text-slate-900">Bra jobbat!</h1>
      <p class="text-slate-500">Du har genomfört hela passet.</p>
      <Button @click="restart" class="rounded-full px-6 bg-blue-600 text-white font-bold">Kör igen</Button>
    </div>

    <!-- Loading -->
    <div v-else-if="!currentExercise" class="flex-1 flex flex-col items-center justify-center gap-3">
      <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
      <p class="text-xs font-medium text-slate-400">Laddar schema...</p>
    </div>

    <!-- Active workout -->
    <template v-else>
      <!-- Video — fills remaining space, metadata overlaid -->
      <div class="flex-1 min-h-0 px-2 pt-2 pb-1 relative">
        <VideoPlayer :exercise="currentExercise" />

        <!-- Overlays on top of video -->
        <div class="absolute top-4 left-4 z-20 pointer-events-none">
          <span v-if="schema" class="bg-blue-600/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
            {{ schema.name }}
          </span>
        </div>
        <div class="absolute top-4 right-4 z-20 pointer-events-none">
          <span class="bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md">
            {{ currentExercise.name }}
          </span>
        </div>
        <div class="absolute bottom-3 right-4 z-20 pointer-events-none">
          <span class="bg-slate-900/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md tabular-nums">
            {{ currentIndex + 1 }}/{{ exercises.length }}
          </span>
        </div>
      </div>

      <!-- Controls -->
      <div class="shrink-0 px-4 py-3 bg-white border-t border-slate-100 flex items-center gap-3">
        <WorkoutTimer :key="currentIndex" :duration="currentExercise.timer_duration" @finished="timerFinished = true" />
        <Button @click="nextExercise"
          class="flex-1 h-12 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-base">
          {{ isLastExercise ? 'Slutför' : 'Nästa' }}
          <ArrowRight class="ml-2 w-4 h-4" />
        </Button>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { apiFetch } from '@/lib/api'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { type Schema, type SchemaExercise } from '@/types'
import Button from '@/components/ui/button/Button.vue'
import VideoPlayer from '@/components/VideoPlayer.vue'
import WorkoutTimer from '@/components/WorkoutTimer.vue'
import { ArrowRight } from 'lucide-vue-next'

const route = useRoute()
const share_token = route.params.share_token

const schema = ref<Schema | null>(null)
const exercises = ref<SchemaExercise[]>([])
const error = ref('')
const currentIndex = ref(0)
const isCompleted = ref(false)
const timerFinished = ref(false)

const currentExercise = computed(() => {
  return exercises.value[currentIndex.value]?.exercise
})

const isLastExercise = computed(() => {
  return currentIndex.value === exercises.value.length - 1
})

watch(currentIndex, () => {
  timerFinished.value = false
})

onMounted(async () => {
  error.value = ''
  await getExercises()
})

async function getExercises() {
  try {
    const response = await apiFetch(`/api/share/${share_token}`)

    if (response.ok) {
      const data = await response.json()
      schema.value = data.schema
      exercises.value = data.exercises
    } else {
      error.value = 'Kunde inte hämta scheman'
    }
  } catch (err) {
    console.error('Failed to get exercises:', err)
    error.value = 'Nätverksfel vid hämtning av scheman'
  }
}

function nextExercise() {
  if (isLastExercise.value) {
    isCompleted.value = true
  } else {
    currentIndex.value++
  }
}

function restart() {
  currentIndex.value = 0
  isCompleted.value = false
}
</script>
