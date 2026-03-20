<script setup>
import { ref, computed, onMounted } from 'vue'
import { apiFetch } from '@/lib/api'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

const props = defineProps({
  schemaId: {
    type: Number,
    required: true
  }
})

const exercises = ref([])
const schemaExercises = ref([])
const searchQuery = ref('')
const isSaving = ref(false)

// Fetch all exercises from library
const fetchExercises = async () => {
  try {
    const response = await apiFetch('/api/exercises')
    if (response.ok) {
      const data = await response.json()
      exercises.value = data.exercises
    }
  } catch (error) {
    console.error('Failed to fetch exercises:', error)
  }
}

// Fetch current schema's exercises (to populate right column if editing)
const fetchSchemaExercises = async () => {
  try {
    // We need the full exercise library first to get names, 
    // because the original API for schema exercises only returns IDs/orders.
    if (exercises.value.length === 0) {
      await fetchExercises()
    }

    const response = await apiFetch(`/api/schemas/${props.schemaId}/exercises`)
    if (response.ok) {
      const data = await response.json()
      
      // Map the IDs from the schema to the full exercise objects from the library
      schemaExercises.value = data.exercises.map(se => {
        const fullExercise = exercises.value.find(e => e.id === se.exercise_id)
        return {
          ...fullExercise,
          id: se.exercise_id
        }
      }).filter(ex => ex.name) // Ensure we only keep ones we successfully matched
    }
  } catch (error) {
    console.error('Failed to fetch schema exercises:', error)
  }
}

onMounted( async () => {
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

const addExercise = (exercise) => {
  schemaExercises.value.push({ ...exercise })
}

const removeExercise = (index) => {
  schemaExercises.value.splice(index, 1)
}

const saveSchema = async () => {
  isSaving.value = true
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
      alert('Schema saved successfully!')
    } else {
      const errorData = await response.json()
      alert('Failed to save schema: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error saving schema:', error)
    alert('An error occurred while saving.')
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <!-- Left: Exercise Library -->
    <Card>
      <CardHeader>
        <CardTitle>Exercise library</CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <Input 
          v-model="searchQuery" 
          placeholder="Search exercises..." 
          class="mb-4"
        />
        
        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2">
          <div 
            v-for="exercise in filteredExercises" 
            :key="exercise.id"
            class="flex items-center justify-between p-3 border rounded-lg bg-card hover:bg-accent transition-colors"
          >
            <span class="font-medium">{{ exercise.name }}</span>
            <Button 
              variant="outline" 
              size="sm" 
              @click="addExercise(exercise)"
              class="text-primary border-primary hover:bg-primary hover:text-white"
            >
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
          <div 
            v-for="(exercise, index) in schemaExercises" 
            :key="index"
            class="flex items-center justify-between p-3 border rounded-lg bg-card"
          >
            <div class="flex items-center gap-3">
              <span class="flex items-center justify-center w-6 h-6 rounded-full bg-secondary text-secondary-foreground text-xs font-bold">
                {{ index + 1 }}
              </span>
              <span class="font-medium">{{ exercise.name }}</span>
            </div>
            <Button 
              variant="destructive" 
              size="sm" 
              @click="removeExercise(index)"
              class="bg-red-100 text-red-600 hover:bg-red-200 border-none"
            >
              remove
            </Button>
          </div>
          
          <div v-if="schemaExercises.length === 0" class="text-center text-muted-foreground py-8 border-2 border-dashed rounded-lg">
            Add exercises from the library to build your schema.
          </div>
        </div>

        <div class="pt-4 border-t mt-auto">
          <Button 
            class="w-full bg-green-100 text-green-700 hover:bg-green-200 border-none font-bold py-6 text-lg"
            :disabled="isSaving || schemaExercises.length === 0"
            @click="saveSchema"
          >
            {{ isSaving ? 'Saving...' : 'Save schema' }}
          </Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

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
