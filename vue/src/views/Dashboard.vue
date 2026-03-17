<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Du är inloggad!</p>
  </div>
  <CreateSchemaForm />
</template>
<script setup>
import CreateSchemaForm from '@/components/CreateSchemaForm.vue';
import { apiFetch } from '@/lib/api.js'
import { onMounted } from 'vue'


onMounted(async () => {
  getSchemas()
})

async function getSchemas() {
  try {

    const response = await apiFetch('/api/schemas')
    const data = await response.json()

    if (!response.ok) {
      return
    }
    console.log("DATA", data)

  } catch (error) {
    error.value = 'Något gick fel'
  }
}
</script>