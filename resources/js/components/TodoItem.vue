<script setup lang="ts">
import { Todo } from '@/Pages/App.vue';
import { getDayFromDateString, getTimeFromDateString } from '@/util/util';
import { onMounted, ref } from 'vue';

const props = defineProps<{ todo: Todo, archived: boolean }>()

//edits are handled by todoInput, edit emit notifies parent
const emit = defineEmits(['edit', 'fetchTodos'])

const desc = ref<HTMLElement | null>(null);
const descExpanded = ref(false);
const descTruncated = ref(false);
const checked = ref(props.todo.is_completed)

onMounted(() => {
  //See if desc is truncated to show expand button
  //if overflow width is bigger than visible content width, its truncated
  if (desc.value && desc.value.scrollWidth > desc.value.clientWidth) {
    descTruncated.value = true;
  }
});

const markComplete = async () => {
  try {
    //update endpoint is used
    const res = await fetch(`/api/tasks/${props.todo.id}`, {
      method: 'PUT',
      headers: {
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        title: props.todo.title,
        description: props.todo.description,
        due_date: props.todo.due_date,
        category: props.todo.category,
        is_completed: true
      })
    })
    if (res.ok) {
      checked.value = true
      emit('fetchTodos') //refetch after completion for filters 
    } else {
      console.error('Failed to update task')
    }
  } catch (err) {
    console.error(err)
  }
}

const deleteTodo = async (taskId: number) => {
  try {
    const res = await fetch(`/api/tasks/${taskId}`, {
      method: 'DELETE',
      headers: {
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    });

    if (!res.ok) {
      console.error('Failed to delete task');
    } else {
      console.log('Task deleted');
      emit('fetchTodos') //refetch after deletion for pagination
    }
  } catch (error) {
    console.error('Error deleting task:', error);
  }
};


const restoreTodo = async (taskId: number) => {
  try {
    const res = await fetch(`/api/tasks/restore/${taskId}`, {
      method: 'post',
      headers: {
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    });

    if (!res.ok) {
      console.error('Failed to restore task');
    } else {
      console.log('Task restored');
      emit('fetchTodos') //refetch after deletion for pagination
    }
  } catch (error) {
    console.error('Error restoring task:', error);
  }
};

</script>

<template>
  <li class="flex py-2 border-b pl-2 border-gray-200">

    <div class="flex flex-col relative text-base grow border-r border-gray-200 pr-2 pl-1 max-w-[28rem]">
      <div class="flex">

        <div :class="{ 'line-through text-gray-400': props.todo.is_completed } + ''">{{ props.todo.title }}</div>

      </div>
      <span ref="desc" class=" text-gray-500 text-sm pr-3"
        :class="descExpanded ? 'break-all' : 'truncate overflow-ellipsis'">{{ props.todo.description }}</span>

      <button v-if="descTruncated" @click="descExpanded = !descExpanded"
        class="absolute right-0 bottom-0 px-1 cursor-pointer text-gray-500 bg-gradient-to-l from-white">
        <img src="@/images/down.svg" class="w-5" :class="{ 'rotate-180': descExpanded }" alt="" srcset="">
      </button>

    </div>

    <div
      class="text-base flex  font-semibold h-auto items-center w-[8.4rem] rounded text-center justify-center border-r border-gray-200"
      :class="props.todo.category ? 'text-purple-700' : 'text-gray-500'">
      {{ props.todo.category || 'uncategorized' }}
    </div>

    <div class="ml-auto flex text-xs text-gray-500 text-right items-center px-4 border-r border-gray-200">
      <div>
        <div>{{ getDayFromDateString(props.todo.due_date!) }}</div>
        <div>{{ getTimeFromDateString(props.todo.due_date!).hour +
          ' : '
          + getTimeFromDateString(props.todo.due_date!).minute }}</div>
      </div>
    </div>

    <div class="flex items-center gap-2 mx-4 ">

      <button type="button" @click="markComplete" :class="{
        'bg-purple-600 text-white ': checked,
        'bg-gray-50 border border-gray-300 text-gray-400': archived && !checked,
        'bg-white text-purple-800 border border-purple-400 cursor-pointer transition-colors hover:bg-purple-50': !checked && !archived,
      }" class="px-2 py-1 rounded  w-24 cursor-default" :disabled="archived">
        {{ checked ? 'Completed' : archived ? 'Incomplete' : 'Complete' }}
      </button>

      <div class="flex w-14 content-around justify-center">

        <button @click="deleteTodo(todo.id)" class="cursor-pointer " :hidden="archived"><img class="w-7"
            src="@/images/delete.svg"></button>
        <button @click="$emit('edit', todo)" class="cursor-pointer ml-auto" :hidden="checked || archived">
          <img class="w-6" src="@/images/edit.svg">
        </button>
        <button @click="restoreTodo(todo.id)" class="cursor-pointer " :hidden="!archived"><img class="w-10"
            src="@/images/restore.svg">
        </button>

      </div>
    </div>
  </li>
</template>
