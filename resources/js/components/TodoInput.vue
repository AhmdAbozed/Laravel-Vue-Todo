<script setup lang="ts">
import { Todo } from '@/Pages/App.vue';
import { computed, ref } from 'vue'
import TimeInput from './TimeInput.vue';

const categories = ref([
    'Work', 'Personal', 'Shopping', 'Others'
])

const emit = defineEmits<{
    (e: 'submit', todo: Todo): void
}>()

const title = ref('')
const description = ref('')
const category = ref('')
const dueDate = ref('')

const time = ref({ hour: 0, minute: 0 });

const datetime = computed(() => {
    console.log(time.value.hour, 'heyy');
    if (!dueDate.value) return '';
    const pad = (n: any) => String(n).padStart(2, '0');
    console.log(pad)
    return `${dueDate.value}T${pad(time.value.hour)}:${pad(time.value.minute)}`;
});

const handleSubmit = () => {

    emit('submit', {
        id: Date.now(),
        title: title.value,
        description: description.value,
        category: category.value,
        dueDate: dueDate.value,
        completed: false
    })

    title.value = ''
    description.value = ''
    category.value = ''
    dueDate.value = ''
}
//<label for="title" class="block text-sm">Title (Max 50 characters)</label>
//<label for="description" class="block text-sm">Description (Max 200 characters)</label>

</script>

<template>
    <form @submit="handleSubmit" class="flex flex-col shadow-md rounded-xl border-gray-100 border grow">
        <div class=" p-4 pt-3 pb-1 border-b border-gray-200">
            <div div class="flex flex-col relative">
                <input v-model="title" type="text" id="title" maxlength="50" placeholder="Enter title..."
                    class="bg-white pl-1 border-none w-full font-semibold text-lg" required />
            </div>
            <textarea v-model="description" id="description" maxlength="200" placeholder="Enter description..."
                class="bg-white pl-1 my-1 border-none rounded w-full h-6 text-gray-800" required></textarea>


        </div>

        <div class="flex px-2 py-2">
            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label for="category" class="block pl-1 text-purple-800 font-semibold">Category</label>
                <select v-model="category" id="category" class="bg-white text-gray-700 border-none p-2 w-full">
                    <option value="" disabled selected>Uncategorized</option>
                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
            </div>

            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label for="dueDate" class="block pl-1 text-purple-800 font-semibold">Due Date</label>
                <input v-model="dueDate" type="date" id="dueDate"
                    class="bg-white text-gray-700 border-none p-2 w-full" />
            </div>
            <div class="flex flex-col justify-center border-r-2 border-gray-200 mr-2 pr-3">
                <label for="dueDate" class="block pl-1 text-purple-800 font-semibold">Due Time</label>
                <TimeInput v-model="time"></TimeInput>
            </div>
            <button type="submit" 
                class=" mx-auto my-auto hover:text-white text-purple-800 py-2 rounded hover:bg-purple-500 font-bold p-2">
                Create Task
                {{ datetime }}
            </button>
        </div>


    </form>
</template>
