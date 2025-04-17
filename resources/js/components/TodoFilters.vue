<script setup lang="ts">
import TodoItem from './TodoItem.vue'

interface Todo {
    id: number
    title: string
    completed: boolean
}

const props = defineProps<{
    todos: Todo[]
}>()

const emit = defineEmits<{
    (e: 'toggle', id: number): void
    (e: 'delete', id: number): void
}>()
</script>

<template>
    <ul>
        <TodoItem v-for="todo in todos" :key="todo.id" :todo="todo" @toggle="emit('toggle', todo.id)"
            @delete="emit('delete', todo.id)" />
    </ul>
</template>
