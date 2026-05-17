<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Placeholder from '@tiptap/extension-placeholder'
import { Bold, Heading2, Heading3, Italic, Link2Icon, List, ListOrdered, Quote, ImageIcon } from '@lucide/vue'
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import Toggle from '@/components/ui/toggle/Toggle.vue'
import MediaModal from './MediaModal.vue'

const props = defineProps<{
  modelValue: string
  placeholder?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const linkDialogOpen = ref(false)
const linkUrl = ref('')
const imagePickerOpen = ref(false)

const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit.configure({
      heading: { levels: [2, 3] },
      link: { openOnClick: false },
    }),
    Image,
    Placeholder.configure({
      placeholder: props.placeholder ?? '',
    }),
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
})

watch(() => props.modelValue, (val) => {
  if (editor.value && val !== editor.value.getHTML()) {
    editor.value.commands.setContent(val || '', false)
  }
})

function openLinkDialog() {
  if (!editor.value)
    return
  linkUrl.value = editor.value.getAttributes('link').href || ''
  linkDialogOpen.value = true
}

function applyLink() {
  if (!editor.value)
    return
  if (linkUrl.value === '')
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
  else
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: linkUrl.value }).run()
  linkDialogOpen.value = false
}

function openImagePicker() {
  imagePickerOpen.value = true
}

function handleImageSelect(data: { id: number; url: string; thumbnail_url?: string }) {
  if (!editor.value || !data.url)
    return
  editor.value.chain().focus().setImage({ src: data.url }).run()
  imagePickerOpen.value = false
}
</script>

<template>
  <div class="border rounded-md overflow-hidden" @click.stop>
    <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 border-b bg-muted/50">
      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('bold') ?? false"
              @click="editor?.chain().focus().toggleBold().run()"
            >
              <Bold class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Bold</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('italic') ?? false"
              @click="editor?.chain().focus().toggleItalic().run()"
            >
              <Italic class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Italic</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('heading', { level: 2 }) ?? false"
              @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
            >
              <Heading2 class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Heading 2</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('heading', { level: 3 }) ?? false"
              @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
            >
              <Heading3 class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Heading 3</p></TooltipContent>
        </Tooltip>
        <span class="w-px h-5 bg-border mx-1" />
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('bulletList') ?? false"
              @click="editor?.chain().focus().toggleBulletList().run()"
            >
              <List class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Bullet list</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('orderedList') ?? false"
              @click="editor?.chain().focus().toggleOrderedList().run()"
            >
              <ListOrdered class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Ordered list</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('blockquote') ?? false"
              @click="editor?.chain().focus().toggleBlockquote().run()"
            >
              <Quote class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Blockquote</p></TooltipContent>
        </Tooltip>
        <span class="w-px h-5 bg-border mx-1" />
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle
              size="sm"
              :pressed="editor?.isActive('link') ?? false"
              @click="openLinkDialog"
            >
              <Link2Icon class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Link</p></TooltipContent>
        </Tooltip>
        <Tooltip>
          <TooltipTrigger as-child>
            <Toggle size="sm" @click="openImagePicker">
              <ImageIcon class="size-4" />
            </Toggle>
          </TooltipTrigger>
          <TooltipContent><p>Image</p></TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </div>
    <EditorContent :editor="editor" class="prose prose-sm max-w-none p-4 min-h-[200px] focus:outline-none" />
  </div>

  <Dialog v-model:open="linkDialogOpen">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <DialogTitle>Link URL</DialogTitle>
      </DialogHeader>
      <div class="py-4">
        <Label>URL</Label>
        <Input v-model="linkUrl" placeholder="https://..." @keydown.enter.prevent="applyLink" />
      </div>
      <DialogFooter>
        <Button variant="outline" @click="linkDialogOpen = false">Cancel</Button>
        <Button @click="applyLink">Apply</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <MediaModal :open="imagePickerOpen" select-mode @close="imagePickerOpen = false" @select="handleImageSelect" />
</template>

<style lang="css">
.ProseMirror {
  outline: none;
  min-height: 200px;
}
.ProseMirror p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
.ProseMirror img {
  max-width: 100%;
  height: auto;
  border-radius: 0.375rem;
}
.ProseMirror a {
  color: var(--primary);
  text-decoration: underline;
}
</style>
