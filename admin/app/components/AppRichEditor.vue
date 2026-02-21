<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

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

const editorRef = ref<HTMLElement | null>(null)
let quill: any = null

onMounted(async () => {
  if (process.client && editorRef.value) {
    // Dynamically import Quill only on client-side
    const Quill = (await import('quill')).default
    await import('quill/dist/quill.snow.css')
    
    // Set Quill globally for older plugins
    if (typeof window !== 'undefined') {
      window.Quill = Quill
    }
    
    // Import and register QuillResize module (compatible with Quill 2.x)
    const { default: QuillResize } = await import('quill-resize-module')
    await import('quill-resize-module/dist/resize.css')
    Quill.register('modules/resize', QuillResize)

    // Import and register quill-better-table module
    // @ts-ignore - quill-better-table doesn't have TypeScript definitions
    const QuillBetterTable = (await import('quill-better-table')).default
    await import('quill-better-table/dist/quill-better-table.css')
    
    Quill.register('modules/better-table', QuillBetterTable, true)

    // Register Font Attributor
    const Font = Quill.import('attributors/style/font')
    Font.whitelist = ['arial', 'verdana', 'times-new-roman', 'georgia', 'courier-new', 'poppins']
    Quill.register(Font, true)

    // Register Size Attributor
    const Size = Quill.import('attributors/style/size')
    Size.whitelist = ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '30px', '32px', '36px']
    Quill.register(Size, true)

    // Polyfill for quill-better-table using Quill 2 string keys in Quill 1.3.7
    const Keyboard = Quill.import('modules/keyboard')
    if (Keyboard && Keyboard.prototype) {
      const originalAddBinding = Keyboard.prototype.addBinding
      Keyboard.prototype.addBinding = function() {
        if (arguments[0] && typeof arguments[0] === 'object') {
          if (arguments[0].key === 'Backspace') arguments[0].key = 8
          if (arguments[0].key === 'Enter') arguments[0].key = 13
          if (arguments[0].key === 'Delete') arguments[0].key = 46
        }
        return originalAddBinding.apply(this, arguments)
      }
    }

    Quill.register('modules/better-table-fix', function(quillInstance: any) {
      ['Backspace', 'Enter', 'Delete'].forEach(key => {
        const keyCode = key === 'Backspace' ? 8 : (key === 'Enter' ? 13 : 46);
        if (!quillInstance.keyboard.bindings[key]) {
          Object.defineProperty(quillInstance.keyboard.bindings, key, {
            get: function() { return this[keyCode] = this[keyCode] || []; },
            set: function(val) { this[keyCode] = val; }
          });
        }
      });
    }, true);

    if (!editorRef.value) return

    quill = new Quill(editorRef.value, {
      theme: 'snow',
      placeholder: props.placeholder,
      modules: {
        'better-table-fix': true,
        toolbar: {
          container: [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            
            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            
            [{ 'size': ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '30px', '32px', '36px'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            
            [{ 'color': ['#33b793', '#522953', '#1c3c34', '#00594e', '#6dd0bd', '#a1d6d4', '#6e1f7d', '#a15ec2', '#d7bfec', '#000000', '#e60000', '#ff9900', '#ffff00', '#008a00', '#0066cc', '#9933ff', '#ffffff', '#facccc', '#ffebcc', '#ffffcc', '#cce8cc', '#cce0f5', '#ebd6ff', '#bbbbbb', '#f06666', '#ffc266', '#ffff66', '#66b966', '#66a3e0', '#c285ff', '#888888', '#a10000', '#b26b00', '#b2b200', '#006100', '#0047b2', '#6b24b2', '#444444', '#5c0000', '#663d00', '#666600', '#003700', '#002966', '#3d1466'] }, { 'background': ['#33b793', '#522953', '#1c3c34', '#00594e', '#6dd0bd', '#a1d6d4', '#6e1f7d', '#a15ec2', '#d7bfec', '#000000', '#e60000', '#ff9900', '#ffff00', '#008a00', '#0066cc', '#9933ff', '#ffffff', '#facccc', '#ffebcc', '#ffffcc', '#cce8cc', '#cce0f5', '#ebd6ff', '#bbbbbb', '#f06666', '#ffc266', '#ffff66', '#66b966', '#66a3e0', '#c285ff', '#888888', '#a10000', '#b26b00', '#b2b200', '#006100', '#0047b2', '#6b24b2', '#444444', '#5c0000', '#663d00', '#666600', '#003700', '#002966', '#3d1466'] }],          // custom color palette with main and secondary colors first
            [{ 'font': [false, 'arial', 'verdana', 'times-new-roman', 'georgia', 'courier-new', 'poppins'] }],
            [{ 'align': [] }],
            
            ['link', 'image', 'video'],
            
            ['table'],                                        // table button
            ['clean']                                         // remove formatting button
          ],
          handlers: {
            table: function() {
              // @ts-ignore
              const quillInstance = this.quill
              const tableModule = quillInstance.getModule('better-table')
              if (tableModule) {
                tableModule.insertTable(3, 3)
              }
            }
          }
        },
        'better-table': {
          columnResize: true,
          operationMenu: {
            items: {
              unmergeCells: {
                text: 'Unmerge cells'
              },
              insertColumnRight: {
                text: 'Insert column right'
              },
              insertColumnLeft: {
                text: 'Insert column left'
              },
              insertRowUp: {
                text: 'Insert row above'
              },
              insertRowDown: {
                text: 'Insert row below'
              },
              mergeCells: {
                text: 'Merge cells'
              },
              deleteColumn: {
                text: 'Delete column'
              },
              deleteRow: {
                text: 'Delete row'
              },
              deleteTable: {
                text: 'Delete table'
              }
            }
          }
        },
        keyboard: {
          bindings: QuillBetterTable.keyboardBindings
        },
        clipboard: {
          matchers: QuillBetterTable.clipboardMatchers
        },
        resize: {
          minWidth: 100,
          minHeight: 100,
          locale: {
            // center: 'center',
          }
        }
      },
    })

    // Set initial content
    if (props.modelValue) {
      quill.root.innerHTML = props.modelValue
    }

    // Listen for text changes
    quill.on('text-change', () => {
      emit('update:modelValue', quill?.root.innerHTML || '')
    })
  }
})

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (quill && newValue !== quill.root.innerHTML) {
    quill.root.innerHTML = newValue
  }
})

onBeforeUnmount(() => {
  quill = null
})
</script>

<template>
  <div class="border border-gray-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-[#ED1D3F] focus-within:border-transparent transition-all bg-white dark:bg-slate-900">
    <div ref="editorRef" class="min-h-[250px] dark:text-slate-100"></div>
  </div>
</template>

<style>
/* Custom Quill Editor Styles */
.ql-toolbar {
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb !important;
  border-top: none !important;
  border-left: none !important;
  border-right: none !important;
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 0.25rem !important;
}

.ql-toolbar.ql-snow {
  padding: 0.75rem !important;
}

.ql-container {
  border: none !important;
  font-family: inherit;
  font-size: 0.875rem;
}

.ql-editor {
  min-height: 250px;
  padding: 0.75rem 1rem;
}

.ql-editor.ql-blank::before {
  color: #9ca3af;
  font-style: normal;
}

/* Toolbar button styles */
.ql-toolbar button:hover,
.ql-toolbar button:focus,
.ql-toolbar button.ql-active,
.ql-toolbar .ql-picker-label:hover,
.ql-toolbar .ql-picker-label.ql-active,
.ql-toolbar .ql-picker-item:hover,
.ql-toolbar .ql-picker-item.ql-selected {
  color: #ED1D3F !important;
}

.ql-toolbar button:hover .ql-fill,
.ql-toolbar button:focus .ql-fill,
.ql-toolbar button.ql-active .ql-fill,
.ql-toolbar .ql-picker-label:hover .ql-fill,
.ql-toolbar .ql-picker-label.ql-active .ql-fill,
.ql-toolbar .ql-picker-item:hover .ql-fill,
.ql-toolbar .ql-picker-item.ql-selected .ql-fill {
  fill: #ED1D3F !important;
}

.ql-toolbar button:hover .ql-stroke,
.ql-toolbar button:focus .ql-stroke,
.ql-toolbar button.ql-active .ql-stroke,
.ql-toolbar .ql-picker-label:hover .ql-stroke,
.ql-toolbar .ql-picker-label.ql-active .ql-stroke,
.ql-toolbar .ql-picker-item:hover .ql-stroke,
.ql-toolbar .ql-picker-item.ql-selected .ql-stroke {
  stroke: #ED1D3F !important;
}

/* Content styles */
.ql-editor img {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
}

.ql-editor h1,
.ql-editor h2,
.ql-editor h3,
.ql-editor h4,
.ql-editor h5,
.ql-editor h6 {
  font-weight: 600;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.ql-editor h1 {
  font-size: 2em;
}

.ql-editor h2 {
  font-size: 1.5em;
}

.ql-editor h3 {
  font-size: 1.17em;
}

.ql-editor blockquote {
  border-left: 4px solid #e5e7eb;
  padding-left: 1rem;
  margin-left: 0;
  margin-right: 0;
  color: #6b7280;
}

/* Table styles */
.ql-editor table {
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
}

.ql-editor table td,
.ql-editor table th {
  border: 1px solid #e5e7eb;
  padding: 0.5rem;
  min-width: 50px;
}

.ql-editor table th {
  background-color: #f9fafb;
  font-weight: 600;
}

.ql-editor table tr:hover {
  background-color: #f9fafb;
}

/* Better Table operation menu styles */
.quill-better-table-wrapper {
  z-index: 1000 !important;
}

.quill-better-table-operation-menu {
  background: white !important;
  border: 1px solid #e5e7eb !important;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
  border-radius: 0.5rem !important;
  padding: 0.5rem 0 !important;
  z-index: 2000 !important;
}

.quill-better-table-operation-menu .quill-better-table-operation-menu-item {
  padding: 0.5rem 1rem !important;
  font-size: 0.875rem !important;
  color: #374151 !important;
  cursor: pointer !important;
  display: flex !important;
  align-items: center !important;
}

.quill-better-table-operation-menu .quill-better-table-operation-menu-item:hover {
  background-color: #f3f4f6 !important;
  color: #ED1D3F !important;
}

/* Fix for the table icon in toolbar */
.ql-table {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.ql-table::after {
  content: 'T' !important;
  font-weight: bold !important;
  font-size: 14px !important;
}

/* Better Table Resizing Styles */
.quill-better-table-wrapper table {
  table-layout: fixed !important;
  width: 100% !important;
}

.quill-better-table-wrapper .column-resize-handle {
  background-color: #ED1D3F !important;
  opacity: 0;
  transition: opacity 0.2s;
  width: 4px !important;
  z-index: 1000 !important;
}

.quill-better-table-wrapper .column-resize-handle:hover {
  opacity: 0.5;
}

.quill-better-table-wrapper .column-resize-handle.dragging {
  opacity: 1;
}

/* Custom Font Styles */
.ql-font-arial {
  font-family: Arial, Helvetica, sans-serif !important;
}
.ql-font-verdana {
  font-family: Verdana, Geneva, sans-serif !important;
}
.ql-font-times-new-roman {
  font-family: 'Times New Roman', Times, serif !important;
}
.ql-font-georgia {
  font-family: Georgia, serif !important;
}
.ql-font-courier-new {
  font-family: 'Courier New', Courier, monospace !important;
}
.ql-font-poppins {
  font-family: 'Poppins', sans-serif !important;
}

/* Font Picker Label */
/* Arial */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="arial"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="arial"]::before {
  content: 'Arial' !important;
  font-family: Arial, Helvetica, sans-serif !important;
}
/* Verdana */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="verdana"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="verdana"]::before {
  content: 'Verdana' !important;
  font-family: Verdana, Geneva, sans-serif !important;
}
/* Times New Roman */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="times-new-roman"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="times-new-roman"]::before {
  content: 'Times New Roman' !important;
  font-family: 'Times New Roman', Times, serif !important;
}
/* Georgia */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="georgia"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="georgia"]::before {
  content: 'Georgia' !important;
  font-family: Georgia, serif !important;
}
/* Courier New */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="courier-new"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="courier-new"]::before {
  content: 'Courier New' !important;
  font-family: 'Courier New', Courier, monospace !important;
}
/* Poppins */
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value="poppins"]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value="poppins"]::before {
  content: 'Poppins' !important;
  font-family: 'Poppins', sans-serif !important;
}

.ql-snow .ql-picker.ql-font .ql-picker-label::before,
.ql-snow .ql-picker.ql-font .ql-picker-item::before {
  content: 'Default' !important;
}

/* Font Size Labels */
/* 10px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10px"]::before {
  content: '10px' !important;
}
/* 12px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12px"]::before {
  content: '12px' !important;
}
/* 14px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14px"]::before {
  content: '14px' !important;
}
/* 16px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16px"]::before {
  content: '16px' !important;
}
/* 18px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18px"]::before {
  content: '18px' !important;
}
/* 20px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="20px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="20px"]::before {
  content: '20px' !important;
}
/* 24px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24px"]::before {
  content: '24px' !important;
}
/* 30px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="30px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="30px"]::before {
  content: '30px' !important;
}
/* 32px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="32px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="32px"]::before {
  content: '32px' !important;
}
/* 36px */
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36px"]::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36px"]::before {
  content: '36px' !important;
}

/* Ensure editor uses Poppins by default if desired, or at least inherits correctly */
.ql-container {
  font-family: 'Poppins', sans-serif !important;
}

/* Dark Mode Overrides */
.dark .ql-toolbar {
  background-color: #0f172a;
  border-bottom: 1px solid #1e293b !important;
}
.dark .ql-editor {
  color: #f8fafc;
}
.dark .ql-editor.ql-blank::before {
  color: #475569;
}
.dark .ql-editor table td, 
.dark .ql-editor table th {
  border-color: #334155;
}
.dark .ql-editor table th {
  background-color: #1e293b;
}
.dark .ql-editor table tr:hover {
  background-color: #1e293b;
}
.dark .quill-better-table-operation-menu {
  background: #1e293b !important;
  border-color: #334155 !important;
}
.dark .quill-better-table-operation-menu .quill-better-table-operation-menu-item {
  color: #f8fafc !important;
}
.dark .quill-better-table-operation-menu .quill-better-table-operation-menu-item:hover {
  background-color: #334155 !important;
  color: #8b5cf6 !important;
}
.dark .ql-picker-options {
  background-color: #1e293b !important;
  border-color: #334155 !important;
}
.dark .ql-picker-item {
  color: #cbd5e1 !important;
}
.dark .ql-picker-item:hover, .dark .ql-picker-item.ql-selected {
  color: #ED1D3F !important;
}
.dark .ql-picker-label {
  color: #cbd5e1 !important;
}
.dark .ql-stroke {
  stroke: #cbd5e1 !important;
}
.dark .ql-fill {
  fill: #cbd5e1 !important;
}
.dark .ql-toolbar button:hover .ql-stroke,
.dark .ql-toolbar button:focus .ql-stroke,
.dark .ql-toolbar button.ql-active .ql-stroke,
.dark .ql-toolbar .ql-picker-label:hover .ql-stroke,
.dark .ql-toolbar .ql-picker-label.ql-active .ql-stroke {
  stroke: #ED1D3F !important;
}
.dark .ql-toolbar button:hover .ql-fill,
.dark .ql-toolbar button:focus .ql-fill,
.dark .ql-toolbar button.ql-active .ql-fill,
.dark .ql-toolbar .ql-picker-label:hover .ql-fill,
.dark .ql-toolbar .ql-picker-label.ql-active .ql-fill {
  fill: #ED1D3F !important;
}
</style>
