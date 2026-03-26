<template>
  <div class="flex flex-col gap-3">
    <div v-for="exercise in exercises" :key="exercise.id"
      class="group p-4 bg-white rounded-xl border border-slate-200 hover:border-green-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
      
      <div class="flex items-start gap-4 flex-grow min-w-0">
        <div class="p-2 bg-slate-50 rounded-lg group-hover:bg-green-50 transition-colors">
          <Youtube class="w-5 h-5 text-slate-400 group-hover:text-red-500 transition-colors" />
        </div>
        
        <div class="flex flex-col gap-1 min-w-0 flex-grow">
          <p class="font-bold text-slate-900 truncate">{{ exercise.name }}</p>
          <div class="flex items-center gap-3 text-xs font-medium text-slate-400">
            <span class="flex items-center gap-1">
              <Timer class="w-3 h-3" />
              {{ exercise.timer_duration }}s
            </span>
            <span class="truncate max-w-[150px]">{{ exercise.youtube_url }}</span>
          </div>
        </div>
      </div>

      <Button 
        variant="ghost" 
        size="icon" 
        @click="$emit('delete', exercise.id)"
        class="h-9 w-9 text-slate-300 hover:text-red-600 hover:bg-red-50 transition-colors flex-shrink-0"
        title="Delete Exercise"
      >
        <Trash2 class="w-4 h-4" />
      </Button>
    </div>

    <div v-if="exercises.length === 0" class="text-center py-8 border-2 border-dashed rounded-xl border-slate-100">
      <p class="text-sm text-slate-400">No exercises in library.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { type Exercise } from '@/types';
import { Button } from '@/components/ui/button';
import { Youtube, Timer, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    exercises: Exercise[]
}>()

defineEmits(['delete'])
</script>