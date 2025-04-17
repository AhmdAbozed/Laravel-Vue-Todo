<script setup lang="ts">
import { ref, computed } from 'vue'
import TodoList from '@/components/TodoList.vue'
import TodoInput from '@/components/TodoInput.vue'
export interface Todo {
  id: number
  title: string
  description?: string
  completed: boolean
  category?: string
  dueDate?: string
}
const todos = ref<Todo[]>([{id: 1, title: 'been spending most their lives', completed: false},{id: 2, title: 'living in the morshou"s', completed: false},{id: 3, title: 'paradise', completed: false},{id: 4, title: 'ma boy *sip*', completed: false},])
const newTodo = ref('')

const handleTodoSubmit = (todo: Todo) => {
  todos.value.push(todo)
}
const toggleTodo = (id: number) => {
    const todo = todos.value.find(t => t.id === id)
    if (todo) todo.completed = !todo.completed
}

const deleteTodo = (id: number) => {
    todos.value = todos.value.filter(t => t.id !== id)
}
</script>

<template>
    <div class="p-4 max-w-md mx-auto flex flex-col  min-w-3xl">
        <h1 class="text-2xl font-bold mb-4">Todo App</h1>
        <TodoInput @submit="handleTodoSubmit" />
        <TodoList :todos="todos" @toggle="toggleTodo" @delete="deleteTodo" />
    </div>
</template>