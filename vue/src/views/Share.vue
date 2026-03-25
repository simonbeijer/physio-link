<template>
    <h1>test</h1>
</template>
<script setup>
import { apiFetch } from '@/lib/api'
import { onMounted, ref } from 'vue'

const exercises = ref([])
const error = ref('')

import { useRoute } from 'vue-router'
const route = useRoute()
const share_token = route.params.share_token


onMounted(async () => {
  error.value = ''
  await Promise.all([
    getExercises()
  ])
})

async function getExercises() {
  try {
    const response = await apiFetch(`/api/share/${share_token}`)
    const data = await response.json()

    if (response.ok) {
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