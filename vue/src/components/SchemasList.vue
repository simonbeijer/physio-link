<template>
  <div class="flex flex-col gap-3">
    <div v-for="schema in schemas" :key="schema.id"
      class="group p-4 bg-white rounded-xl border border-slate-200 hover:border-blue-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
      
      <div class="flex flex-col gap-1">
        <span class="font-bold text-slate-900 group-hover:text-blue-700 transition-colors">{{ schema.name }}</span>
        <a :href="`/share/${schema.share_token}`" target="_blank" class="text-xs text-slate-400 hover:text-blue-500 flex items-center gap-1">
          <ExternalLink class="w-3 h-3" />
          {{ schema.share_token.substring(0, 8) }}...
        </a>
      </div>

      <div class="flex items-center gap-2">
        <Button variant="outline" size="sm" @click="$emit('edit', schema)" class="h-9 px-3 text-slate-600 hover:text-blue-600 hover:border-blue-200">
          <Edit class="w-4 h-4 mr-2" />
          Edit
        </Button>
        
        <Button 
          variant="ghost" 
          size="icon" 
          @click="$emit('delete', schema.id)"
          class="h-9 w-9 text-slate-300 hover:text-red-600 hover:bg-red-50 transition-colors"
          title="Delete Schema"
        >
          <Trash2 class="w-4 h-4" />
        </Button>
      </div>
    </div>
    
    <div v-if="schemas.length === 0" class="text-center py-8 border-2 border-dashed rounded-xl border-slate-100">
      <p class="text-sm text-slate-400">No schemas found yet.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { ExternalLink, Trash2, Edit } from 'lucide-vue-next'
import { type Schema } from '@/types';

const props = defineProps<{
  schemas: Schema[]
}>()

defineEmits(['edit', 'delete'])
</script>