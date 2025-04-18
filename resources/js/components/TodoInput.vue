<script setup lang="ts">
import { Todo } from '@/Pages/App.vue';
import { computed, ref, watch } from 'vue'
import TimeInput from './TimeInput.vue';
import { getDayFromDateString, getTimeFromDateString } from '@/util/util';

const props = defineProps<{
    editing: Todo | null,
    categories: Array<String>
}>()

const emit = defineEmits(['fetchTodos'])


const title = ref('')
const description = ref('')
const category = ref('')
const dueDate = ref(new Date().toISOString().split('T')[0]) //DD:MM:YY
const time = ref({ hour: 23, minute: 59 });
const editId = ref(0);

const dueDateTime = computed(() => {
    if (!dueDate.value) return '';
    const pad = (n: any) => String(n).padStart(2, '0');
    console.log(pad)
    return `${dueDate.value}T${pad(time.value.hour)}:${pad(time.value.minute)}`;
});

watch(() => props.editing, (editingTodo) => {
    if (editingTodo) {
        title.value = editingTodo.title;
        description.value = editingTodo.description || '';
        category.value = editingTodo.category || '';
        //format from api: 'YY:MM:DD HH:MM:SS'
        dueDate.value = getDayFromDateString(editingTodo.due_date!);
        time.value = {hour: Number(getTimeFromDateString(editingTodo.due_date!).hour), minute: Number(getTimeFromDateString(editingTodo.due_date!).minute)} ;
        editId.value = editingTodo.id;
    }
}, { immediate: true })

const clearForm = () => {
    title.value = '';
    description.value = '';
    category.value = '';
    dueDate.value = new Date().toISOString().split('T')[0];
    time.value = { hour: 23, minute: 59 };
    editId.value = 0;
}

const handleSubmit = async (e: any) => {

    const taskData = {
        title: title.value,
        description: description.value,
        category: category.value=='All' ? null : category.value ,
        due_date: dueDateTime.value,
        is_completed: false
    };

    try {
        e.preventDefault()
        const options:RequestInit = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",
            body: JSON.stringify(taskData),
        }
        if (editId.value) {options.method = 'PUT';}
        const response = await fetch((editId.value ? '/api/tasks/' + editId.value : 'api/tasks/'), options);

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error:', errorData);
            return;
        }

        const createdTask = await response.json();
        clearForm()
        emit('fetchTodos');
    } catch (error) {
        console.error('submit todo error:', error);
    }
}
</script>

<template>
    <form @submit="handleSubmit" class="flex flex-col shadow-md rounded-xl border-gray-100 border grow">
        <div class=" p-4 pt-3 pb-1 border-b border-gray-200">
            <div div class="flex flex-col relative">
                <input v-model="title" type="text" id="title" maxlength="50" placeholder="Enter title..."
                    class="bg-white pl-1 border-none w-full font-semibold text-lg" required />
            </div>
            <textarea v-model="description" id="description" maxlength="150" placeholder="Enter description..."
                class="bg-white pl-1 my-1 border-none rounded w-full h-6 text-gray-800" required></textarea>


        </div>

        <div class="flex px-2 py-2">
            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label for="category" class="block pl-1 text-purple-800 font-semibold">Category</label>
                <select v-model="category" id="category" class="bg-white text-gray-700 border-none p-2 w-full">
                    <option value="" disabled selected>Uncategorized</option>
                    <option v-for='category in categories'
                     :key="category as PropertyKey" :value="category">
                    
                    {{ category }}
                    
                    </option>
                </select>
            </div>

            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label for="dueDate" class="block pl-1 text-purple-800 font-semibold">Due Date</label>
                <input v-model="dueDate" type="date" id="dueDate" :min="new Date().toISOString().split('T')[0]"
                    class="bg-white text-gray-700 border-none p-2 w-full" required />
            </div>
            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label class="block pl-1 text-purple-800 font-semibold">Due Time</label>
                <TimeInput v-model="time"></TimeInput>
            </div>
            <button type="submit"
                class=" mx-auto my-auto hover:text-white text-purple-800 py-2 rounded hover:bg-purple-500 font-bold p-2">
                {{ editId ? 'Edit Todo' : 'Create Todo' }}
            </button>
            <button type="button" @click="clearForm"
                class=" mx-auto my-auto hover:text-white text-purple-800 py-2 rounded hover:bg-purple-500 font-bold p-2">
                {{ editId ? 'Cancel' : 'Clear' }}
            </button>
        </div>


    </form>
</template>
