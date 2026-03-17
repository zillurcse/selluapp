<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import Editor from '@tinymce/tinymce-vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Write something...',
  },
})

const emit = defineEmits(['update:modelValue'])

const editorValue = ref(props.modelValue)

// Watch for internal changes to emit to parent
watch(() => editorValue.value, (newValue) => {
  if (newValue !== props.modelValue) {
    emit('update:modelValue', newValue)
  }
})

// Watch for external changes to update internal ref
watch(() => props.modelValue, (newValue) => {
  if (newValue !== editorValue.value) {
    editorValue.value = newValue || ''
  }
})

const colorMode = useColorMode()

const initOptions = ref({
  height: 400,
  menubar: true,
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount'
  ],
  toolbar: 'undo redo | blocks | ' +
    'bold italic forecolor | alignleft aligncenter ' +
    'alignright alignjustify | bullist numlist outdent indent | ' +
    'table link image | removeformat | help',
  content_style: 'body { font-family:Poppins,Helvetica,Arial,sans-serif; font-size:14px }',
  placeholder: props.placeholder,
  branding: false,
  promotion: false,
  skin: colorMode.value === 'dark' ? 'oxide-dark' : 'oxide',
  content_css: colorMode.value === 'dark' ? 'dark' : 'default',
})

// Watch for color mode changes to update TinyMCE skin
watch(() => colorMode.value, (newMode) => {
  initOptions.value = {
    ...initOptions.value,
    skin: newMode === 'dark' ? 'oxide-dark' : 'oxide',
    content_css: newMode === 'dark' ? 'dark' : 'default',
  }
})
const tinymceEditor = ref<any>(null)

const onEditorInit = (e: any) => {
  tinymceEditor.value = e.target
}

const insertContent = (content: string) => {
  if (tinymceEditor.value) {
    tinymceEditor.value.insertContent(content)
  } else {
    editorValue.value += content
  }
}

defineExpose({
  insertContent
})
</script>

<template>
  <div class="border border-gray-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-[#ED1D3F] focus-within:border-transparent transition-all bg-white dark:bg-slate-900">
    <Editor
      v-model="editorValue"
      :init="initOptions"
      @init="onEditorInit"
      api-key="l195s2zxhzodqftl13yibjalz0fht3bgzrlgddkipvxc5kye"
    />
  </div>
</template>

<style>
/* Custom styling for TinyMCE to match the app */
.tox-tinymce {
  border: none !important;
}

.tox-editor-header {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
}

.dark .tox-editor-header {
  background-color: #0f172a !important;
  border-bottom: 1px solid #1e293b !important;
}

.dark .tox-tinymce {
  background-color: #0f172a !important;
  border: none !important;
}

.dark .tox-toolbar, 
.dark .tox-toolbar-overlord, 
.dark .tox-toolbar__primary {
  background-color: #0f172a !important;
}

.dark .tox-mbtn {
  background-color: #0f172a !important;
  color: #f1f5f9 !important;
}

.dark .tox-mbtn:hover:not(:disabled):not(.tox-mbtn--active) {
  background-color: #1e293b !important;
}

.dark .tox-tbtn {
  color: #f1f5f9 !important;
}

.dark .tox-tbtn:hover {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

.dark .tox-tbtn--enabled, 
.dark .tox-tbtn--enabled:hover {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

.dark .tox-edit-area__iframe {
  background-color: #0f172a !important;
}

/* Adjust colors to match brand red */
.tox .tox-tbtn--enabled, 
.tox .tox-tbtn--enabled:hover,
.tox .tox-tbtn:focus {
  color: #ED1D3F !important;
}

.tox .tox-tbtn:hover svg,
.tox .tox-tbtn--enabled svg,
.tox .tox-tbtn--enabled:hover svg {
  fill: #ED1D3F !important;
}
</style>
