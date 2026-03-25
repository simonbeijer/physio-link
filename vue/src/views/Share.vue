<script setup lang="ts">
import { apiFetch } from '@/lib/api'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { type SchemaExercise } from '@/types'

const route = useRoute()
const share_token = route.params.share_token

const exercises = ref<SchemaExercise[]>([])
const error = ref('')

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
</script>

<template>
    <div v-if="error" class="text-red-500">{{ error }}</div>
    <div v-else>
        <h1>Workout Preview</h1>
        <ul>
            <li v-for="ex in exercises" :key="ex.exercise_id">
                {{ ex.exercise.name }}
            </li>
        </ul>
    </div>
</template>