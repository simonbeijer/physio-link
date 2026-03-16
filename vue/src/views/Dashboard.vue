<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Du är inloggad!</p>
  </div>
  <div style="width: 200px;">

    <Card>
      <CardHeader>
        <CardTitle>Skapa</CardTitle>
        <CardDescription>Skapa nytt schema</CardDescription>
      </CardHeader>
      <CardContent>

        <Input name="name" type="text" v-model="name" />
      </CardContent>
      <CardFooter>
        <Button @click="createSchema">Skapa</Button>

      </CardFooter>
    </Card>
  </div>
</template>
<script setup>
import Button from '@/components/ui/button/Button.vue';
import {
  Card, CardHeader
  , CardTitle
  , CardDescription
  , CardContent
  , CardFooter
} from '@/components/ui/card';
import Input from '@/components/ui/input/Input.vue';
import { apiFetch } from '@/lib/api.js'
import { onMounted, ref } from 'vue'

const name = ref('')


onMounted(async () => {
  console.log("test")
  getSchemas()
})

async function createSchema() {

  try {
    console.log("runs")

    const response = await apiFetch('/api/schemas', {
      method: "POST",
      body: JSON.stringify({ name: name.value })
    })

    const data = await response.json()

    if (!response.ok) {
      return
    }
    console.log("DATA", data)

  } catch (error) {
    error.value = 'Något gick fel'
  }
}


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