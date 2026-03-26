<template>
  <div class="flex flex-col gap-2">
    <div v-for="exercise in exercises" :key="exercise.id"
      class="p-3 bg-white rounded-lg border text-sm flex justify-between">
      <div style="max-width: 80%;">

        <p class="font-medium">{{ exercise.name }}</p>
        <p class="text-gray-400 text-xs truncate">{{ exercise.youtube_url }}</p>
      </div>
      <Button variant="destructive" size="icon-sm" @click="$emit('delete', exercise.id)">X</Button>
    </div>
    <p v-if="exercises.length === 0" class="text-sm text-gray-400">Inga övningar än.</p>
  </div>
</template>
<script setup lang="ts">
import { type Exercise } from '@/types';
import Button from './ui/button/Button.vue';

const props = defineProps<{
    exercises: Exercise[]
}>()

defineEmits(['delete'])

</script>