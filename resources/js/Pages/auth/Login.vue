<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3'
const errorMsg = ref("")
const formProcessing = ref(false)

const form = reactive({
    username: null,
    password: null,
})
async function submitForm(event: Event) {
    try {
        console.log(event.currentTarget)
        console.log(event.target)
        console.log(location.protocol + "//" + location.host + "/")
        event.preventDefault();
        formProcessing.value = true;
        const target = event.target as any
        const submission = { name: form.username, password: form.password }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol + "//" + location.host + "/login";
        const res = await fetch(endpoint, options);
        formProcessing.value = false;

        if (res.status == 200) {
            router.visit('/');
        }
        else if(res.status == 403 || res.status == 422) {
            errorMsg.value = (await res.json()).message;
            
            formProcessing.value = false;
        }
    }
    catch (e) {
        console.error(e);
        formProcessing.value = false;
        errorMsg.value = "Something has gone wrong, Try again later";
    }
}

</script>

<template>
   <section class="bg-gray-100 h-full">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 h-full">
        <div
            class="w-full bg-white animate-slideFadeIn rounded-lg shadow border border-gray-300 md:mt-0 sm:max-w-md xl:p-0 transition-transform">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-yellow-300 md:text-2xl">
                    Sign in to your account
                </h1>
                <form class="space-y-4 md:space-y-6" METHOD="post" @submit="submitForm">
                    <div>
                        <label for="email" class="block mb-2 text-sm    ">Username</label>
                        <input v-model="form.username" type="text" name="username" id="username" minlength="3"
                            required
                            class="bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-yellow-300 focus:ring-1 outline-none focus:border-yellow-300 block w-full p-2.5"
                            placeholder="Username">
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm   ">Password</label>
                        <input v-model="form.password" type="password" name="password" id="password" minlength="5"
                            required placeholder="••••••••"
                            class="bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-yellow-300 focus:ring-1 outline-none focus:border-yellow-300 block w-full p-2.5">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember" aria-describedby="remember" type="checkbox"
                                    class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-yellow-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="remember" class="">Remember me</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full text-white disabled:opacity-50 font-semibold text-md bg-yellow-300 hover:bg-yellow-400 focus:ring-4 focus:outline-none focus:ring-yellow-300 rounded-lg  px-5 py-2.5 text-center"
                        :disabled="formProcessing">Sign In</button>

                    <div class="flex justify-center text-sm text-red-600">
                        {{ errorMsg }}
                    </div>
                    <p class="text-sm font-light text-gray-500">
                        Don’t have an account yet?
                        <Link href="/register" class="font-medium text-yellow-500 hover:underline">
                            Sign up
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

</template>