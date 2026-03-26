<template>
  <div class="space-y-4 p-4">
    <!-- Notifications -->
    <div v-if="error"
      class="p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm font-medium animate-in fade-in slide-in-from-top-1">
      {{ error }}
    </div>
    <div v-if="success"
      class="p-3 bg-green-50 border border-green-200 text-green-600 rounded-lg text-sm font-medium animate-in fade-in slide-in-from-top-1">
      {{ success }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Left: Exercise Library -->
      <Card>
        <CardHeader>
          <CardTitle>Exercise library</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <Input v-model="searchQuery" placeholder="Search exercises..." class="mb-4" />

          <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2">
            <div v-for="exercise in filteredExercises" :key="exercise.id"
              class="flex items-center justify-between p-3 border rounded-lg bg-card hover:bg-accent transition-colors">
              <span class="font-medium">{{ exercise.name }}</span>
              <Button variant="outline" size="sm" @click="addExercise(exercise)"
                class="text-primary border-primary hover:bg-primary hover:text-white">
                + add
              </Button>
            </div>
            <div v-if="filteredExercises.length === 0" class="text-center text-muted-foreground py-4">
              No exercises found.
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Right: Current Schema Queue -->
      <Card>
        <CardHeader>
          <CardTitle>Schema — Exercises</CardTitle>
        </CardHeader>
        <CardContent class="flex flex-col h-full space-y-4">
          <div class="space-y-2 flex-grow max-h-[500px] overflow-y-auto pr-2">
            <div v-for="(exercise, index) in schemaExercises" :key="index"
              class="group flex items-center justify-between p-3 border rounded-xl bg-white hover:border-blue-200 hover:shadow-sm transition-all duration-200">
              <div class="flex items-center gap-3">
                <span
                  class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold">
                  {{ index + 1 }}
                </span>
                <span class="font-bold text-slate-700">{{ exercise.name }}</span>
              </div>
              <Button 
                variant="ghost" 
                size="icon" 
                @click="removeExercise(index)"
                class="h-8 w-8 text-slate-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                title="Remove from schema"
              >
                <Trash2 class="w-4 h-4" />
              </Button>
            </div>

            <div v-if="schemaExercises.length === 0"
              class="text-center text-muted-foreground py-8 border-2 border-dashed rounded-lg">
              Add exercises from the library to build your schema.
            </div>
          </div>

          <div class="pt-4 border-t mt-auto">
            <Button class="w-full bg-green-100 text-green-700 hover:bg-green-200 border-none font-bold py-6 text-lg"
              :disabled="isSaving || schemaExercises.length === 0" @click="saveSchema">
              {{ isSaving ? 'Sparar...' : 'Spara schema' }}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { apiFetch } from '@/lib/api'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Trash2 } from 'lucide-vue-next'
import { type Exercise, type SchemaExercise } from '@/types';


const props = defineProps<{
  schemaId: number
}>()

const exercises = ref<Exercise[]>([])
const schemaExercises = ref<Exercise[]>([])
const searchQuery = ref('')
const isSaving = ref(false)
const error = ref('')
const success = ref('')

// Fetch all exercises from library
const fetchExercises = async () => {
  error.value = ''
  try {
    const response = await apiFetch('/api/exercises')
    if (response.ok) {
      const data = await response.json()
      exercises.value = data.exercises
    } else {
      error.value = 'Kunde inte hämta övningsbiblioteket.'
    }
  } catch (err) {
    console.error('Failed to fetch exercises:', err)
    error.value = 'Nätverksfel vid hämtning av övningar.'
  }
}

const fetchSchemaExercises = async () => {
  error.value = ''
  try {
    const response = await apiFetch(`/api/schemas/${props.schemaId}/exercises`)
    if (response.ok) {
      const data = await response.json()

      // Ensure we only add defined exercises
      schemaExercises.value = data.exercises
        .map((se: SchemaExercise) => se.exercise)
        .filter((ex: Exercise | undefined) => !!ex)
    } else {
      error.value = 'Kunde inte hämta schemats övningar.'
    }
  } catch (err) {
    console.error('Failed to fetch schema exercises:', err)
    error.value = 'Nätverksfel vid hämtning av schemats övningar.'
  }
}

onMounted(async () => {
  await fetchExercises()
  await fetchSchemaExercises()
})

const filteredExercises = computed(() => {
  if (!searchQuery.value) return exercises.value
  const query = searchQuery.value.toLowerCase()
  return exercises.value.filter(ex =>
    ex.name.toLowerCase().includes(query)
  )
})

const addExercise = (exercise: Exercise) => {
  if (!exercise) return
  schemaExercises.value.push({ ...exercise })
  success.value = ''
}

const removeExercise = (index: number) => {
  schemaExercises.value.splice(index, 1)
  success.value = ''
}

const saveSchema = async () => {
  isSaving.value = true
  error.value = ''
  success.value = ''

  const payload = {
    exercises: schemaExercises.value.map((ex, index) => ({
      exercise_id: ex.id,
      order: index + 1
    }))
  }

  try {
    const response = await apiFetch(`/api/schemas/${props.schemaId}/exercises`, {
      method: 'POST',
      body: JSON.stringify(payload)
    })

    if (response.ok) {
      success.value = 'Schema har sparats framgångsrikt!'
    } else {
      const errorData = await response.json()
      error.value = 'Kunde inte spara schema: ' + (errorData.message || 'Okänt fel')
    }
  } catch (err) {
    console.error('Error saving schema:', err)
    error.value = 'Ett nätverksfel uppstod vid sparning.'
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
/* Custom scrollbar styling using standard CSS variables (matching Shadcn theme) */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: hsl(var(--muted));
  border-radius: 9999px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: hsl(var(--muted-foreground));
}
</style>
