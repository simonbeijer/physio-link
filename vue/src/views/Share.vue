<template>
  <div class="h-screen w-full bg-slate-50 flex flex-col overflow-hidden">
    <div class="flex-grow flex flex-col items-center justify-center p-2 overflow-y-auto pb-48">
      <div v-if="error" class="text-red-600 font-medium p-4 bg-red-50 rounded-lg border border-red-200 m-4">
        {{ error }}
      </div>

      <div v-else-if="isCompleted" class="text-center space-y-6 animate-in fade-in zoom-in duration-300">
        <div class="text-6xl mb-4">🎉</div>
        <h1 class="text-3xl font-black text-slate-900 leading-tight">Bra jobbat!</h1>
        <p class="text-slate-500 font-medium">Du har genomfört hela passet.</p>
        <Button @click="restart" size="lg" class="rounded-full px-8 bg-blue-600 font-bold text-white">Kör igen</Button>
      </div>

      <div v-else-if="currentExercise" class="w-full max-w-sm flex flex-col items-center">
        <div class="w-full px-2">
          <VideoPlayer :exercise="currentExercise" />
        </div>
      </div>

      <div v-else class="flex flex-col items-center gap-3">
        <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Laddar schema...</p>
      </div>
    </div>

    <!-- Combined Bottom Bar -->
    <div v-if="currentExercise && !isCompleted"
      class="fixed bottom-0 left-0 right-0 p-3 bg-white/80 backdrop-blur-lg border-t border-slate-100 shadow-2xl z-50">
      <div class="max-w-md mx-auto space-y-3">
        <div class="flex items-center justify-between px-1">
          <div class="flex flex-col">
            <h1 v-if="schema" class="text-[8px] font-bold text-blue-600 uppercase tracking-widest leading-none mb-1">{{
              schema.name }}</h1>
            <h2 class="text-sm font-black text-slate-900 truncate max-w-[200px]">{{ currentExercise.name }}</h2>
          </div>
          <div class="text-right">
            <p class="text-[10px] text-slate-400 font-black tabular-nums">{{ currentIndex + 1 }} / {{ exercises.length
              }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <WorkoutTimer 
            :key="currentIndex" 
            :duration="currentExercise.timer_duration"
            variant="minimal" 
            @finished="timerFinished = true" 
          />

          <Button @click="nextExercise" size="lg"
            class="flex-grow h-12 text-base rounded-2xl shadow-lg active:scale-95 transition-all bg-blue-600 hover:bg-blue-700 text-white font-black">
            {{ isLastExercise ? 'Slutför' : 'Nästa' }}
            <ArrowRight class="ml-2 w-5 h-5 stroke-[3px]" />
          </Button>
        </div>
      </div>
    </div>
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