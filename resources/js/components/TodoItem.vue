<script setup lang="ts">
import { onMounted, ref } from 'vue';

defineProps(['todo'])
defineEmits(['toggle', 'delete', 'complete', 'archive'])
const desc = ref<HTMLElement | null>(null);
const expanded = ref(false);
const isTruncated = ref(false);
onMounted(() => {
    const el = desc.value;
    if (el && el.scrollWidth > el.clientWidth) {
        isTruncated.value = true;
    }
});
</script>

<template>
    <li class="flex py-2 border-b pl-2 border-gray-200">

        <div class="flex flex-col relative text-base grow border-r border-gray-200 pr-2">
            <div class="flex">

                <div :class="{ 'line-through text-gray-400': todo.completed } + ''">{{ 'border-r ' +
                    'border -gray - 200border - r border-gray-200bo' }}
                </div>

            </div>
            <span ref="desc" class=" max-w-sm text-gray-500 text-sm"
                :class="expanded ? '' : 'whitespace-nowrap overflow-ellipsis truncate'">{{ todo.description ||
                    'verylong' +
                    'textvery long textvery long textvery long textvery long textvery long textvery lony longtext'
                }}</span>

            <button v-if="isTruncated" @click="expanded = !expanded"
                class="absolute right-0 bottom-0 h-full px-2 text-gray-500 bg-gradient-to-l from-white">
                v
            </button>

        </div>
        
        <div class="text-base flex text-purple-500 font-bold h-auto items-center rounded px-4 border-r border-gray-200">
            {{ todo.category || 'Personal' }}
        </div>

        <div class="ml-auto flex text-xs text-gray-500 text-right items-center px-4 border-r border-gray-200">
            <div>
                <div>{{ todo.date || '23/5/2023' }}</div>
                <div>{{ todo.time || '23:59' }}</div>
            </div>
        </div>

        <div class="flex items-center gap-2 ml-4">
            <button @click="$emit('complete')" class="text-green-600 text-sm">/</button>
            <button @click="$emit('delete')" class="text-red-500 text-sm">x</button>
        </div>
    </li>
</template>
