<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    // expects: { hour: Number, minute: Number }
  }
});
const emit = defineEmits(['update:modelValue']);

const hour = computed({
  get: () => props.modelValue.hour,
  set: (val) => {
    emit('update:modelValue', {
      ...props.modelValue,
      hour: clamp(val, 0, 23)
    });
  }
});

const minute = computed({
  get: () => props.modelValue.minute,
  set: (val) => {
    console.log('hewwo', val)
    emit('update:modelValue', {
      ...props.modelValue,
      minute: clamp(val, 0, 59)
    });
  }
});

const clamp = (val, min, max) => Math.min(max, Math.max(min, val));
</script>

<template>
  <div class="flex items-center space-x-2">
    <input
      type="number"
      v-model.number="hour"
      class="w-12 text-center"
      min="0"
      max="23"
    />
    <span class="text-xl">:</span>
    <input
      type="number"
      v-model.number="minute"
      class="w-12 text-center"
      min="0"
      max="59"
    />
  </div>
</template>
