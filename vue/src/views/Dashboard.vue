<template>
  <div class="min-h-screen p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-8">Dashboard</h1>

    <!-- Scheman -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
      <h2 class="text-lg font-semibold text-blue-800 mb-4">Scheman</h2>
      <SchemasList :schemas="schemas" />
      <Accordion type="single" collapsible class="mt-4">
        <AccordionItem value="create-schema">
          <AccordionTrigger class="text-blue-700">Skapa nytt schema</AccordionTrigger>
          <AccordionContent>
            <CreateSchemaForm @schemaCreated="getSchemas" />
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>

    <!-- Övningar -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
      <h2 class="text-lg font-semibold text-green-800 mb-4">Övningar</h2>
      <ExercisesList :exercises="exercises" />
      <Accordion type="single" collapsible class="mt-4">
        <AccordionItem value="create-exercise">
          <AccordionTrigger class="text-green-700">Skapa ny övning</AccordionTrigger>
          <AccordionContent>
            <CreateExerciseForm @exerciseCreated="getExercises" />
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>
  </div>
</template>

<script setup>
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion'
import CreateExerciseForm from '@/components/CreateExerciseForm.vue';
import CreateSchemaForm from '@/components/CreateSchemaForm.vue';
import ExercisesList from '@/components/ExercisesList.vue';
import SchemasList from '@/components/SchemasList.vue';
import { apiFetch } from '@/lib/api.js'
import { onMounted, ref } from 'vue'

const exercises = ref([])
const schemas = ref([])

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
    schemas.value = data.schemas


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
    exercises.value = data.exercises

  } catch (error) {
    error.value = 'Något gick fel'
  }
}
</script>