<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Du är inloggad!</p>
  </div>
  <div class="flex justify-center">
    <CreateSchemaForm />
    <CreateExerciseForm />
  </div>
</template>
<script setup>
import CreateExerciseForm from '@/components/CreateExerciseForm.vue';
import CreateSchemaForm from '@/components/CreateSchemaForm.vue';
import { apiFetch } from '@/lib/api.js'
import { onMounted } from 'vue'


onMounted(async () => {
  getSchemas()
  getExercises()
})

async function getSchemas() {
  try {

    const response = await apiFetch('/api/schemas')
    const data = await response.json()

    if (!response.ok) {
      return
    }
    console.log("schema", data)

  } catch (error) {
    error.value = 'Något gick fel'
  }
}


async function getExercises() {
  try {

    const response = await apiFetch('/api/exercises')
    const data = await response.json()

    if (!response.ok) {
      return
    }
    console.log("exercies", data)

  } catch (error) {
    error.value = 'Något gick fel'
  }
}
</script>