<template>
  <div class="space-y-2">
    <div class="p-5 bg-green-100/50 rounded-lg border border-green-200 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label
            class="block text-xs font-semibold text-green-900 uppercase tracking-wider mb-1 ml-1">Övningsnamn</label>
          <Input type="text" v-model="name" placeholder="Ex: Knäböj"
            class="bg-white border-green-200 focus-visible:ring-green-500" @keydown.enter="createExercise" />
        </div>
        <div>
          <label
            class="block text-xs font-semibold text-green-900 uppercase tracking-wider mb-1 ml-1">YouTube-länk</label>
          <Input type="text" v-model="youtube_url" placeholder="https://youtube.com/..."
            class="bg-white border-green-200 focus-visible:ring-green-500" @keydown.enter="createExercise" />
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-4 items-end">
        <div>
          <label class="block text-xs font-semibold text-green-900 uppercase tracking-wider mb-1 ml-1">Start (sek)</label>
          <Input type="number" v-model="start_time" step="0.1"
            class="bg-white border-green-200 focus-visible:ring-green-500" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-green-900 uppercase tracking-wider mb-1 ml-1">Slut (sek)</label>
          <Input type="number" v-model="end_time" step="0.1"
            class="bg-white border-green-200 focus-visible:ring-green-500" />
        </div>
        <div class="col-span-2 md:col-span-1">
          <label class="block text-xs font-semibold text-green-900 uppercase tracking-wider mb-1 ml-1">Timer (sek)</label>
          <Input type="number" v-model="timer_duration"
            class="bg-white border-green-200 focus-visible:ring-green-500" />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <Button v-if="updateId" @click="updateExercise" class="bg-blue-600 hover:bg-blue-700 text-white font-bold flex-grow h-12"
          :disabled="!isFormValid || isLoading">
          <CheckCircle class="w-4 h-4 mr-2" />
          {{ isLoading ? 'Sparar...' : 'Uppdatera övning' }}
        </Button>
        <Button v-else @click="createExercise" class="bg-green-600 hover:bg-green-700 text-white font-bold flex-grow h-12"
          :disabled="!isFormValid || isLoading">
          <Save class="w-4 h-4 mr-2" />
          {{ isLoading ? 'Sparar...' : 'Spara övning' }}
        </Button>
        
        <Button variant="outline" @click="clearFields" class="h-12 border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 px-6">
          <XCircle class="w-4 h-4 mr-2" />
          Rensa
        </Button>
      </div>
      <p v-if="error" class="text-sm text-red-600 font-medium ml-1 mt-2">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { apiFetch } from '@/lib/api'
import { Exercise } from '@/types';
import { ref, watch, computed } from 'vue'
import { XCircle, Save, CheckCircle } from 'lucide-vue-next'

const name = ref('')
const youtube_url = ref('')
const start_time = ref(0)
const end_time = ref(0)
const timer_duration = ref(0)
const updateId = ref(0)

const isLoading = ref(false)
const error = ref('')

const props = defineProps<{
  exercise: Exercise | null
}>()

const emit = defineEmits<{
  (e: 'exerciseUpdate'): void
  (e: 'clearSelection'): void
}>()

// Validation logic
const isFormValid = computed(() => {
  return name.value.trim() !== '' && 
         youtube_url.value.trim() !== '' && 
         timer_duration.value > 0
})

watch(() => props.exercise, (exercise: Exercise | null) => {
  if (exercise) {
    name.value = exercise.name ?? '';
    youtube_url.value = exercise.youtube_url ?? '';
    start_time.value = exercise.start_time ?? 0
    end_time.value = exercise.end_time ?? 0;
    timer_duration.value = exercise.timer_duration ?? 0
    updateId.value = exercise.id ?? 0
  } else {
    clearFields(false) // Clear internal fields without re-emitting
  }
}, { immediate: true })

async function updateExercise() {
  if (!updateId.value || !isFormValid.value || isLoading.value) return

  isLoading.value = true
  error.value = ''

  try {
    const response = await apiFetch(`/api/exercises/${updateId.value}`, {
      method: "PATCH",
      body: JSON.stringify({
        name: name.value,
        youtube_url: youtube_url.value,
        start_time: start_time.value,
        end_time: end_time.value,
        timer_duration: timer_duration.value
      })
    })

    if (!response.ok) {
      error.value = "Kunde inte uppdatera övning"
      return
    }

    clearFields()
    emit('exerciseUpdate')

  } catch (err) {
    console.error('Något gick fel', err)
    error.value = 'Ett nätverksfel uppstod'
  } finally {
    isLoading.value = false
  }
}

async function createExercise() {
  if (!isFormValid.value || isLoading.value) return

  isLoading.value = true
  error.value = ''

  try {
    const response = await apiFetch('/api/exercises', {
      method: "POST",
      body: JSON.stringify({
        name: name.value,
        youtube_url: youtube_url.value,
        start_time: start_time.value,
        end_time: end_time.value,
        timer_duration: timer_duration.value
      })
    })

    if (!response.ok) {
      const data = await response.json()
      error.value = data.message || 'Kunde inte skapa övning'
      return
    }

    clearFields()
    emit('exerciseUpdate')

  } catch (err) {
    console.error('Något gick fel', err)
    error.value = 'Ett nätverksfel uppstod'
  } finally {
    isLoading.value = false
  }
}

function clearFields(shouldEmit = true) {
  name.value = ''
  youtube_url.value = ''
  start_time.value = 0
  end_time.value = 0
  timer_duration.value = 0
  updateId.value = 0
  error.value = ''
  
  if (shouldEmit) {
    emit('clearSelection')
  }
}
</script>
