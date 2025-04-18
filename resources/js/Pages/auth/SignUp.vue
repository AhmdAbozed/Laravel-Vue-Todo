<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue';

const formProcessing = ref(false)
const errorMsg = ref("")

async function submitSignupForm(event: Event) {
    try {
        event.preventDefault();
        formProcessing.value = true;
        const target = event.target as any
        const submission = { name: target.elements.username.value, password: target.elements.password.value, email: target.elements.email.value }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'

            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol + "//" + location.host + "/register"
        const res = await fetch(endpoint, options);
        if (res.status == 200) {
            router.visit('/login')
        }
        else if (res.status == 403 || res.status == 422) {
            errorMsg.value = (await res.json()).message;

            formProcessing.value = false;
        } else {
            errorMsg.value = (res.status + ": Something went wrong. Try again later.")!
            formProcessing.value = false;
        }
    }
    catch (e) {
        console.error(e)
        errorMsg.value = ("Something went wrong. Try again later.")!

formProcessing.value = false;

    }
}
</script>

<template>
    <section class="bg-gray-100 h-full">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 h-full">
            <div
                class="w-full bg-white animate-slideFadeIn rounded-lg shadow border border-gray-300 md:mt-0 sm:max-w-md xl:p-0 transition-transform">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-purple-800 md:text-2xl">
                        Create an account
                    </h1>
                    <form class="space-y-4 md:space-y-6" @submit="submitSignupForm">
                        <div>
                            <label for="email" class="block mb-2 text-sm">Email</label>
                            <input type="email" name="email" id="email" required
                                class="bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-purple-300 focus:ring-1 outline-none focus:border-purple-300 block w-full p-2.5"
                                placeholder="Email">
                        </div>
                        <div>
                            <label for="username" class="block mb-2 text-sm">Username</label>
                            <input type="text" name="username" id="username" required minlength="3"
                                class="bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-purple-300 focus:ring-1 outline-none focus:border-purple-300 block w-full p-2.5"
                                placeholder="Username">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                minlength="5"
                                class="bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-purple-300 focus:ring-1 outline-none focus:border-purple-300 block w-full p-2.5">
                        </div>
                        
                        <button type="submit" :disabled="formProcessing"
                            class="w-full text-white disabled:opacity-50 mt-4 font-semibold text-md bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-purple-700 rounded-lg px-5 py-2.5 text-center">
                            Sign Up
                        </button>
                        <div class="flex justify-center text-sm text-red-600">
                            {{ errorMsg }}
                        </div>
                        <p class="text-sm font-light text-gray-500">
                            Already have an Account?
                            <Link href="/login" class="font-medium text-purple-500 hover:underline">Login</Link>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

</template>