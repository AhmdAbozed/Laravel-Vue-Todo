<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import TodoInput from '@/components/TodoInput.vue'
import TodoItem from '@/components/TodoItem.vue'
import { signout } from '@/util/util'
export interface Todo {
  id: number
  title: string
  description?: string
  is_completed: boolean
  category?: string
  due_date?: string
}
const todos = ref<Todo[]>([]);

const categories = ['All', 'Urgent', 'Work', 'Personal'];
const activeCategory = ref(categories[0]);
const filterStatus = ref<string | null>(null); //pending or completed?
const sortBy = ref<string | null>(null);
const search = ref<string | null>(null);
const page = ref(1);
const lastPage = ref(1);
const archiveOnly = ref(false)
const editing = ref<Todo | null>(null) //used for setting editing on todoInput

//abort former fetchTodos if another one is sent before it resolves
let controller: AbortController | null = null;

const fetchTodos = async () => {
  if (controller) controller.abort();
  controller = new AbortController();

  const params = new URLSearchParams();
  if (search.value) params.append('search', search.value);
  if (sortBy.value) params.append('sortBy', sortBy.value);
  if (filterStatus.value) params.append('status', filterStatus.value);
  if (activeCategory.value !== 'All') params.append('category', activeCategory.value);
  if (page.value) params.append('page', page.value.toString());
  if (archiveOnly.value) params.append('archive_only', archiveOnly.value.toString());

  try {
    const res = await fetch(`/api/tasks?${params.toString()}`, {
      signal: controller.signal,
      credentials: 'include',
    });
    const data = await res.json();
    todos.value = data.data || [];
    lastPage.value = data.last_page
  } catch (e: any) {
    if (e.name !== 'AbortError') console.error(e);
  }
};

onMounted(fetchTodos);

watch([sortBy, filterStatus, activeCategory, archiveOnly], fetchTodos);

//only send request after 500ms of no input inside search
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(search, () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    page.value = 1;
    fetchTodos();
  }, 500);
});

const handleEdit = (todo: Todo) => {
  console.log('editting: ', todo.id)
  editing.value = { ...todo }
}
</script>

<template>
  <div class="p-4 mx-auto flex flex-col  max-w-[58em] animate-slideFadeIn">
    <div class="flex">
      <h1 class="text-2xl font-bold mb-4 text-purple-800">Todo App</h1>

      <button @click="signout"
        class=" my-auto ml-auto hover:text-white text-purple-800 py-1 cursor-pointer rounded hover:bg-purple-500 font-bold p-2">
        Sign out
      </button>
    </div>
    <TodoInput @fetchTodos="fetchTodos" :editing="editing" :categories="categories" />
    <div class="flex flex-col shadow-md min-h-52 rounded-xl border-2 border-gray-200 mt-2">
      <div class="w-full h-10  flex border-b border-gray-200">

        <div class="flex border-gray-200">
          <button v-for="category in categories" :key="category" @click="activeCategory = category"
            class="px-4 py-2 -mb-px   font-semibold border-r  border-t-0 first:border-l border-l-0 rounded-t border-gray-200"
            :class="{
              'border-b-white bg-white text-purple-700': activeCategory === category,
              'bg-gray-50 border-b border-gray-200 text-purple-900': activeCategory !== category
            }">
            {{ category }}
          </button>
        </div>
        <input v-model="search" type="text" name="search" class="grow mx-1 px-4" placeholder="Search..." />
        <div class="ml-auto flex items-center">
          <select v-model="filterStatus"
            class="h-full w-28 px-2 text-purple-800 font-semibold text-sm border-l-2 border-gray-200 ">
            <option :value=null>Any</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>

          <select v-model="sortBy"
            class="h-full w-28 px-2 text-purple-800 font-semibold text-sm border-l-2 border-gray-200 ml-2">
            <option :value=null>Unsorted</option>
            <option value="due_date">Due Date</option>
            <option value="status">Status</option>
          </select>
          <button class="h-full px-2 rounded-tr border shadow-inner cursor-pointer border-gray-6"
            @click="archiveOnly = !archiveOnly"
            :class="archiveOnly ? ' bg-white text-purple-700' : 'bg-gray-100 border-b border-gray-200 text-purple-900'">
            <img class="w-7" src="@/images/delete.svg">
          </button>
        </div>
      </div>
      <ul class="flex grow flex-col" v-if="todos.length">

        <TodoItem v-for="todo in todos" :key="todo.id" :todo="todo" @edit="handleEdit" @fetchTodos="fetchTodos"
          :archived="archiveOnly" />
      </ul>
      <div class="text-gray-400 m-auto text-xl " v-else>No Todos Found</div>
    </div>
    <div class="flex justify-center gap-2 py-2 border-t border-gray-200">
      <button v-for="n in lastPage" :key="n" @click="() => { page = n; fetchTodos(); }" :class="[
        'px-3 py-1 rounded border text-sm',
        page === n ? 'bg-purple-600 text-white' : 'bg-white text-purple-800 border-gray-300'
      ]">
        {{ n }}
      </button>
    </div>

  </div>
</template>