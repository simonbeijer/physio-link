
<template>
  <div class="min-h-screen bg-slate-50 flex flex-col items-center p-4">
    <div v-if="error" class="text-red-600 font-medium p-4 bg-red-50 rounded-lg border border-red-200">
      {{ error }}
    </div>

    <div v-else-if="isCompleted" class="text-center space-y-6 mt-10">
      <h1 class="text-4xl font-bold text-green-600">Bra jobbat! 🎉</h1>
      <p class="text-xl text-slate-600">Du har genomfört hela passet.</p>
      <Button @click="restart" size="lg">Kör igen</Button>
    </div>

    <div v-else-if="currentExercise" class="w-full max-w-3xl space-y-4">
      <!-- Header -->
      <div class="text-center space-y-1 mb-2">
        <h1 v-if="schema" class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em] opacity-70">{{ schema.name }}</h1>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">{{ currentExercise.name }}</h2>
        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Steg {{ currentIndex + 1 }} av {{ exercises.length }}</p>
      </div>

      <!-- Video Player -->
      <VideoPlayer :exercise="currentExercise" />

      <!-- Controls & Timer -->
      <div class="flex flex-col gap-6 w-full max-w-sm mx-auto">
        <div class="flex justify-center">
          <WorkoutTimer 
            :key="currentIndex"
            :duration="currentExercise.timer_duration" 
            @finished="timerFinished = true" 
          />
        </div>

        <div class="flex flex-col justify-between p-6 bg-white rounded-2xl shadow-sm border min-h-[160px]">

          
          <Button 
            @click="nextExercise" 
            size="lg" 
            class="w-full h-14 text-lg rounded-xl shadow-md active:scale-95 transition-all bg-blue-600 hover:bg-blue-700 text-white font-bold"
          >
            {{ isLastExercise ? 'Slutför passet' : 'Nästa övning' }}
            <ArrowRight class="ml-2 w-5 h-5" />
          </Button>
        </div>
      </div>
    </div>
    
    <div v-else class="mt-10">
        Laddar schema...
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

// Reset state when changing exercise
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
