<template>
  <div>
    <q-form @submit.prevent="handleSubmit">
      <q-input
        v-model="form.title"
        label="Título"
        outlined
        class="q-mb-md"
        :error="!!errors.title"
        :error-message="errors.title"
        required
      />

      <div class="q-mb-md">
        <label>Conteúdo</label>
        <div ref="editorContainer" style="border:1px solid #ccc; min-height:200px; border-radius:4px;"></div>
        <div v-if="errors.content" class="text-negative">{{ errors.content }}</div>
      </div>

      <q-uploader
        ref="coverUploader"
        label="Capa (cover)"
        accept="image/*"
        :auto-upload="false"
        :max-files="1"
        class="q-mb-md"
        @added="onCoverAdded"
        :error="!!errors.cover"
        :error-message="errors.cover"
      />
      <!-- <div v-if="form.coverPreview" class="q-mb-md">
        <img :src="form.coverPreview" alt="Preview da capa" style="max-width: 200px;" />
      </div> -->

      <q-toggle v-model="form.visible" label="Visível" class="q-mb-md" />

      <q-input
        v-model="form.created_at"
        label="Data de Criação"
        type="date"
        outlined
        class="q-mb-md"
        :disable="true"
      />

      <q-btn color="primary" label="Salvar Post" type="submit" class="q-mr-sm" />
    </q-form>

    <pre>{{ output }}</pre>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import EditorJS from '@editorjs/editorjs'
import ImageTool from '@editorjs/image'
import { useQuasar } from 'quasar'
import axiosInstance from '../axiosInstance'
import { useAuthStore } from '../store/authStore'
import { useToast } from 'vue-toastification'

const $q = useQuasar()
const authStore = useAuthStore()

const editor = ref(null)
const editorContainer = ref(null)
const output = ref('')
const errors = reactive({})
const toast = useToast()
const coverUploader = ref(null)

const form = reactive({
  title: '',
  content: '',
  cover: null,
  coverPreview: null,
  visible: true,
  created_at: new Date().toISOString().slice(0, 10),
})

onMounted(() => {
  editor.value = new EditorJS({
    holder: editorContainer.value,
    autofocus: true,
    tools: {
      image: {
        class: ImageTool,
        config: {
          endpoints: {
            byFile: 'http://localhost:9000/api/v1/posts/upload-image',
          },
          field: 'upload',
          additionalRequestHeaders: {
            Authorization: `Bearer ${authStore.authToken}`,
          },
        },
      },
    },
    data: {
      blocks: [],
    },
  })
})

function onCoverAdded(files) {
  if (files && files.length > 0) {
    form.cover = files[0]
    const reader = new FileReader()
    reader.onload = ev => { form.coverPreview = ev.target.result }
    reader.readAsDataURL(files[0])
  } else {
    form.cover = null
    form.coverPreview = null
  }
}

const handleSubmit = async () => {
  errors.title = ''
  errors.content = ''
  errors.cover = ''

  try {
    const data = await editor.value.save()
    form.content = JSON.stringify(data)
  } catch (err) {
    errors.content = 'Erro ao salvar conteúdo do editor.'
    return
  }

  const formData = new FormData()
  formData.append('title', form.title)
  formData.append('content', form.content)
  formData.append('visible', form.visible ? 1 : 0)
  formData.append('created_at', form.created_at)
  if (form.cover) formData.append('image', form.cover)

  try {
    const token = authStore.authToken
    const response = await axiosInstance.post('/v1/posts', formData, {
      headers: { Authorization: `Bearer ${token}` },
    })
    toast.success('Post criado com sucesso!')
    output.value = JSON.stringify(response.data, null, 2)
    // Limpar formulário
    form.title = ''
    form.cover = null
    form.coverPreview = null
    form.visible = true
    if (coverUploader.value) {
      coverUploader.value.reset()
    }
    form.created_at = new Date().toISOString().slice(0, 10)
    // Limpar o editor
    editor.value.clear()
  } catch (error) {
    if (error.response && error.response.data && error.response.data.errors) {
      Object.assign(errors, error.response.data.errors)
    } else {
    toast.error('Erro ao criar post.')
    }
  }
}
</script>