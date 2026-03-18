<template>
  <div style="width: 200px;">
    <Card>
      <CardHeader>
        <CardTitle>Skapa</CardTitle>
        <CardDescription>Skapa nytt schema</CardDescription>
      </CardHeader>
      <CardContent>
        <label class="text-sm font-medium">Namn</label>
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
import { ref } from 'vue'

const name = ref('')

async function createSchema() {

  try {
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
</script>