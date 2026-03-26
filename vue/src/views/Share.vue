
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

    <div v-else-if="currentExercise" class="w-full max-w-3xl space-y-6">
      <!-- Header -->
      <div class="text-center space-y-2 mb-8">
        <h1 class="text-3xl font-bold text-slate-900">{{ currentExercise.name }}</h1>
        <p class="text-slate-500">Övning {{ currentIndex + 1 }} av {{ exercises.length }}</p>
      </div>

      <!-- Video Player -->
      <VideoPlayer :exercise="currentExercise" />

      <!-- Controls -->
      <div class="flex items-center justify-between mt-8 p-4 bg-white rounded-xl shadow-sm border">
        <div class="text-slate-600">
            <!-- Timer placeholder -->
            <span class="font-mono text-xl font-bold">⏱ {{ currentExercise.timer_duration }}s</span>
        </div>

        <Button @click="nextExercise" size="lg" class="px-8">
          {{ isLastExercise ? 'Avsluta' : 'Nästa övning' }}
        </Button>
      </div>
    </div>
    
    <div v-else class="mt-10">
        Laddar schema...
    </div>
  </div>
</template>

<script setup lang="ts">
import { apiFetch } from '@/lib/api'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { type SchemaExercise } from '@/types'
import Button from '@/components/ui/button/Button.vue'
import VideoPlayer from '@/components/VideoPlayer.vue'

const route = useRoute()
const share_token = route.params.share_token

const exercises = ref<SchemaExercise[]>([])
const error = ref('')
const currentIndex = ref(0)
const isCompleted = ref(false)

const currentExercise = computed(() => {
  return exercises.value[currentIndex.value]?.exercise
})

const isLastExercise = computed(() => {
  return currentIndex.value === exercises.value.length - 1
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
