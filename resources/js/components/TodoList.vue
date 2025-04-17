<script setup lang="ts">
import { ref } from 'vue';
import TodoItem from './TodoItem.vue'

interface Todo {
    id: number
    title: string
    completed: boolean
}

const props = defineProps<{
    todos: Todo[]
}>()

const tabs = ['Tab 1', 'Tab 2', 'Tab 3'];
const activeTab = ref(tabs[0]);
const filterStatus = ref('any');
const sortBy = ref('none');

const emit = defineEmits<{
    (e: 'toggle', id: number): void
    (e: 'delete', id: number): void
}>()
</script>

<template>
    <div class="flex flex-col shadow-md rounded-xl border-2 border-gray-200 mt-2">
        <div class="w-full h-10  flex border-b border-gray-200">
          
            <div class="flex border-gray-200">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                    class="px-4 py-2 -mb-px   font-semibold border-r  border-t-0 first:border-l border-l-0 rounded-t border-gray-200"
                    :class="{
                        'border-b-white bg-white text-purple-700': activeTab === tab,
                        'bg-gray-50 border-b border-gray-200 text-purple-900': activeTab !== tab
                    }">
                    {{ tab }}
                </button>
            </div>
            <input type="text" name="search" class="grow mx-1 px-4" placeholder="Search..."/>
            <div class="ml-auto flex items-center">
                <select v-model="filterStatus" class="h-8 px-2 text-purple-800 font-semibold text-sm border-l-2 border-gray-200 ">
                    <option value="any">Any</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>

                <select v-model="sortBy" class="h-8 px-2 text-purple-800 font-semibold text-sm border-l-2 border-gray-200 ml-2">
                    <option value="none">Unsorted</option>
                    <option value="date">Date</option>
                    <option value="status">Status</option>
                </select>
            </div>
        </div>
        <ul class="flex grow flex-col">
            <TodoItem v-for="todo in todos" :key="todo.id" :todo="todo" @toggle="emit('toggle', todo.id)"
                @delete="emit('delete', todo.id)" />
        </ul>
    </div>

</template>
