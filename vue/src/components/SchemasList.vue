<template>
  <div class="flex flex-col gap-2">
    <div v-for="schema in schemas" :key="schema.id"
      class="p-3 bg-white rounded-lg border text-sm font-medium flex items-center justify-between">
      <span>{{ schema.name }}</span>
      <a :href="`/share/${schema.share_token}`">Link</a>
      <Button variant="outline" size="sm" @click="$emit('edit', schema)">
        Edit Exercises
      </Button>
    </div>
    <p v-if="schemas.length === 0" class="text-sm text-gray-400">Inga scheman än.</p>
  </div>
</template>
<script setup>
import { Button } from '@/components/ui/button'

const props = defineProps({
    schemas: Array
})

defineEmits(['edit'])
</script>