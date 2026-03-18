<template>
  <div class="flex flex-col gap-2">
    <div v-for="schema in schemas" :key="schema.id"
      class="p-3 bg-white rounded-lg border text-sm font-medium">
      {{ schema.name }}
    </div>
    <p v-if="schemas.length === 0" class="text-sm text-gray-400">Inga scheman än.</p>
  </div>
</template>
<script setup>

const props = defineProps({
    schemas: Array
})

</script>