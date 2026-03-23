<template>
  <div class="space-y-2">
    <div class="flex items-end gap-3 p-4 bg-blue-100/50 rounded-lg border border-blue-200">
      <div class="flex-grow">
        <label class="block text-xs font-semibold text-blue-900 uppercase tracking-wider mb-1 ml-1">Schema Namn</label>
        <Input 
          name="name" 
          type="text" 
          v-model="name" 
          placeholder="Ex: Rehab Knä" 
          class="bg-white border-blue-200 focus-visible:ring-blue-500"
          @keydown.enter="createSchema"
        />
      </div>
      <Button 
        @click="createSchema" 
        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6"
        :disabled="!name || isLoading"
      >
        {{ isLoading ? 'Skapar...' : 'Skapa' }}
      </Button>
    </div>
    <p v-if="error" class="text-sm text-red-600 ml-1 font-medium">{{ error }}</p>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { apiFetch } from '@/lib/api.js'
import { ref } from 'vue'

const name = ref('')
const isLoading = ref(false)
const error = ref('')
const emit = defineEmits(['schemaCreated'])

async function createSchema() {
  if (!name.value || isLoading.value) return
  
  isLoading.value = true
  error.value = ''

  try {
    const response = await apiFetch('/api/schemas', {
      method: "POST",
      body: JSON.stringify({ name: name.value })
    })

    if (!response.ok) {
      const data = await response.json()
      error.value = data.message || 'Kunde inte skapa schema'
      return
    }
    
    name.value = ''
    emit('schemaCreated')

  } catch (err) {
    console.error('Något gick fel', err)
    error.value = 'Ett nätverksfel uppstod'
  } finally {
    isLoading.value = false
  }
}
</script>
