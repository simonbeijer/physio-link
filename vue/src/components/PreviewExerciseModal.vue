<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
      @click.self="close"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[95dvh] flex flex-col overflow-hidden">

        <header class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Förhandsvisning</p>
            <h2 class="text-base font-bold text-slate-900 truncate">{{ name || 'Övning' }}</h2>
          </div>
          <button
            @click="close"
            aria-label="Stäng"
            class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500"
          >
            <X class="w-4 h-4" />
          </button>
        </header>

        <div class="flex-1 min-h-0 px-3 pt-3 bg-slate-50">
          <div v-if="!previewExercise" class="h-64 flex flex-col items-center justify-center text-slate-400 text-sm">
            <p>Lägg till YouTube-länk för att förhandsvisa</p>
          </div>
          <div v-else class="h-64 sm:h-72">
            <VideoPlayer :exercise="previewExercise" />
          </div>
        </div>

        <div class="px-5 py-4 space-y-3 border-t border-slate-100">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Start (s)</label>
              <Input type="number" step="0.1" :model-value="startTime" @update:model-value="v => emit('update:startTime', Number(v))" class="text-center" />
            </div>
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Slut (s)</label>
              <Input type="number" step="0.1" :model-value="endTime" @update:model-value="v => emit('update:endTime', Number(v))" class="text-center" />
            </div>
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Timer (s)</label>
              <Input type="number" :model-value="timerDuration" @update:model-value="v => emit('update:timerDuration', Number(v))" class="text-center" />
            </div>
          </div>
          <p v-if="invalidRange" class="text-xs text-amber-700 font-medium">Slutpunkt måste vara större än startpunkt.</p>

          <Button @click="close" class="w-full h-11 bg-blue-600 hover:bg-blue-700 text-white font-bold">
            Klar
          </Button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import VideoPlayer from '@/components/VideoPlayer.vue'
import { type Exercise } from '@/types'
import { X } from 'lucide-vue-next'

const props = defineProps<{
  open: boolean
  name: string
  youtubeUrl: string
  startTime: number
  endTime: number
  timerDuration: number
}>()

const emit = defineEmits<{
  (e: 'update:open', v: boolean): void
  (e: 'update:startTime', v: number): void
  (e: 'update:endTime', v: number): void
  (e: 'update:timerDuration', v: number): void
}>()

const invalidRange = computed(() => props.endTime > 0 && props.endTime <= props.startTime)

// Synthetic id flips when bounds change, forcing VideoPlayer to reload with new loop window
const previewExercise = computed<Exercise | undefined>(() => {
  if (!props.youtubeUrl.trim()) return undefined
  return {
    id: Number(`${Math.round(props.startTime * 10)}${Math.round(props.endTime * 10)}`) || 1,
    name: props.name,
    youtube_url: props.youtubeUrl,
    start_time: props.startTime,
    end_time: invalidRange.value ? 0 : props.endTime,
    timer_duration: props.timerDuration,
  }
})

function close() {
  emit('update:open', false)
}
</script>
