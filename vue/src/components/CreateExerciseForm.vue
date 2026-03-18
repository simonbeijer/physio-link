<template>
  <div style="width: 200px;">
    <Card>
      <CardHeader>
        <CardTitle>Skapa</CardTitle>
        <CardDescription>Skapa ny övning</CardDescription>
      </CardHeader>
      <CardContent class="flex flex-col gap-2">
          <label class="text-sm font-medium">Namn</label>
          <Input type="text" v-model="name" placeholder="Ex: Knäböj" />

          <label class="text-sm font-medium">YouTube-länk</label>
          <Input type="text" v-model="youtube_url" placeholder="https://youtube.com/..." />

          <label class="text-sm font-medium">Starttid (sekunder)</label>
          <Input type="number" v-model="start_time" />

          <label class="text-sm font-medium">Sluttid (sekunder)</label>
          <Input type="number" v-model="end_time" />

          <label class="text-sm font-medium">Timer (sekunder)</label>
          <Input type="number" v-model="timer_duration" />
      </CardContent>
      <CardFooter>
        <Button @click="createExercise">Skapa</Button>
      </CardFooter>
    </Card>
  </div>
</template>

<script setup>
import Button from '@/components/ui/button/Button.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import Input from '@/components/ui/input/Input.vue';
import { apiFetch } from '@/lib/api.js'
import { ref } from 'vue'

const name = ref('')
const youtube_url = ref('')
const start_time = ref(0)
const end_time = ref(0)
const timer_duration = ref(0)

async function createExercise() {
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

    const data = await response.json()

    if (!response.ok) {
      return
    }

    name.value = ''
    youtube_url.value = ''
    start_time.value = 0
    end_time.value = 0
    timer_duration.value = 0

    console.log("DATA", data)

  } catch (error) {
    console.error('Något gick fel', error)
  }
}
</script>