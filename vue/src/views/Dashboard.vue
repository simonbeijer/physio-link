<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Du är inloggad!</p>
  </div>
  <div>
    <Button @click="getSchemas">Hämta scheman</Button>
  </div>
</template>
<script setup>
import Button from '@/components/ui/button/Button.vue';
import { apiFetch } from '@/lib/api.js'


async function getSchemas() {
  try {
    const token = localStorage.getItem('token')
    const response = await apiFetch('/api/schemas')

    const data = response.json()

    if (!response.ok) {
      return
    }
    console.log(data)

  } catch (error) {
    error.value = 'Något gick fel'
  }
}
</script>