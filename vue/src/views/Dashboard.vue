<template>
  <div class="min-h-screen bg-slate-50/50">
    <!-- Top Navigation -->
    <header class="border-b bg-white/80 backdrop-blur-md sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">P</div>
          <span class="font-bold text-xl tracking-tight text-slate-900">PhysioLink</span>
        </div>
        
        <div class="flex items-center gap-4">
          <Button variant="ghost" size="sm" class="text-slate-500 hover:text-slate-900" @click="handleLogout">
            <LogOut class="w-4 h-4 mr-2" />
            Logout
          </Button>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
      <!-- Edit Mode Header -->
      <div v-if="selectedSchema" class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Schema</h1>
          <p class="text-slate-500 text-lg mt-1">{{ selectedSchema.name }}</p>
        </div>
        <Button variant="outline" @click="selectedSchema = null" class="bg-white">
          <ChevronLeft class="w-4 h-4 mr-2" />
          Back to Dashboard
        </Button>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl flex items-center justify-between">
        <span>{{ error }}</span>
        <Button variant="ghost" size="sm" @click="error = ''" class="text-red-600 hover:bg-red-100">Stäng</Button>
      </div>

      <!-- Default Mode Header -->
      <div v-else class="mb-10">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Welcome Back</h1>
        <p class="text-slate-500 text-lg">Manage your exercise programs and share them with patients.</p>
      </div>

      <!-- Content -->
      <div v-if="selectedSchema" class="transition-all duration-300">
        <div class="bg-white rounded-2xl border shadow-sm p-2 overflow-hidden">
           <SchemaExerciseSelector :schemaId="selectedSchema.id" />
        </div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Schemas Column -->
        <div class="lg:col-span-5 space-y-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="p-2 bg-blue-100 text-blue-700 rounded-lg">
                <LayoutDashboard class="w-5 h-5" />
              </div>
              <h2 class="text-xl font-bold text-slate-900">Your Schemas</h2>
            </div>
          </div>

          <div class="bg-white rounded-2xl border shadow-sm p-6 overflow-hidden">
            <SchemasList :schemas="schemas" @edit="selectedSchema = $event" />
            
            <Accordion type="single" collapsible class="mt-6 border-t pt-2">
              <AccordionItem value="create-schema" class="border-none">
                <AccordionTrigger class="py-4 text-blue-600 hover:no-underline font-medium hover:text-blue-700">
                  <span class="flex items-center gap-2">
                    <Plus class="w-4 h-4" />
                    Create New Schema
                  </span>
                </AccordionTrigger>
                <AccordionContent>
                  <div class="pt-2">
                    <CreateSchemaForm @schemaCreated="getSchemas" />
                  </div>
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>
        </div>

        <!-- Exercises Column -->
        <div class="lg:col-span-7 space-y-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="p-2 bg-green-100 text-green-700 rounded-lg">
                <Dumbbell class="w-5 h-5" />
              </div>
              <h2 class="text-xl font-bold text-slate-900">Exercise Library</h2>
            </div>
          </div>

          <div class="bg-white rounded-2xl border shadow-sm p-6 overflow-hidden">
            <ExercisesList :exercises="exercises" />
            
            <Accordion type="single" collapsible class="mt-6 border-t pt-2">
              <AccordionItem value="create-exercise" class="border-none">
                <AccordionTrigger class="py-4 text-green-600 hover:no-underline font-medium hover:text-green-700">
                  <span class="flex items-center gap-2">
                    <Plus class="w-4 h-4" />
                    Add New Exercise to Library
                  </span>
                </AccordionTrigger>
                <AccordionContent>
                  <div class="pt-2">
                    <CreateExerciseForm @exerciseCreated="getExercises" />
                  </div>
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { 
  LogOut, 
  LayoutDashboard, 
  Dumbbell, 
  Plus, 
  ChevronLeft 
} from 'lucide-vue-next'
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import CreateExerciseForm from '@/components/CreateExerciseForm.vue';
import CreateSchemaForm from '@/components/CreateSchemaForm.vue';
import ExercisesList from '@/components/ExercisesList.vue';
import SchemasList from '@/components/SchemasList.vue';
import SchemaExerciseSelector from '@/components/SchemaExerciseSelector.vue';
import { apiFetch, logout } from '@/lib/api.js'
import { onMounted, ref } from 'vue'

const exercises = ref([])
const schemas = ref([])
const selectedSchema = ref(null)
const error = ref('')

onMounted(async () => {
  error.value = ''
  await Promise.all([
    getSchemas(),
    getExercises()
  ])
})

async function getSchemas() {
  try {
    const response = await apiFetch('/api/schemas')
    const data = await response.json()

    if (response.ok) {
      schemas.value = data.schemas
    } else {
      error.value = 'Kunde inte hämta scheman'
    }
  } catch (err) {
    console.error('Failed to get schemas:', err)
    error.value = 'Nätverksfel vid hämtning av scheman'
  }
}

async function getExercises() {
  try {
    const response = await apiFetch('/api/exercises')
    const data = await response.json()

    if (response.ok) {
      exercises.value = data.exercises
    } else {
      error.value = 'Kunde inte hämta övningar'
    }
  } catch (err) {
    console.error('Failed to get exercises:', err)
    error.value = 'Nätverksfel vid hämtning av övningar'
  }
}

function handleLogout() {
  logout()
}
</script>
